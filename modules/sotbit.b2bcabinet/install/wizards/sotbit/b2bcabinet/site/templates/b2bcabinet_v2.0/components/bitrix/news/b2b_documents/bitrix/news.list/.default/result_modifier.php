<?
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Loader;
use Sotbit\B2BCabinet\Controller\FileController;
use Sotbit\B2BCabinet\Personal\Buyers;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\ElementPropertyTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Sale;
use Sotbit\B2bCabinet;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

$properyId = PropertyTable::query()
    ->addSelect('ID')
    ->where('IBLOCK_ID', $arParams['IBLOCK_ID'])
    ->where('CODE', 'USER')
    ->getQuery();

$iblockElemtsId = ElementPropertyTable::query()
    ->addSelect('IBLOCK_ELEMENT_ID')
    ->where('VALUE', CurrentUser::get()->getId())
    ->where('IBLOCK_PROPERTY_ID', new SqlExpression("($properyId)"))
    ->getQuery();

$arResult['TOTOAL_COUNT'] = ElementTable::query()
    ->addSelect(new ExpressionField('count', 'COUNT(ID)'))
    ->where('IBLOCK_ID', $arParams['IBLOCK_ID'])
    ->where('ACTIVE', 'Y')
    ->whereIn('ID', new SqlExpression("($iblockElemtsId)"))
    ->fetch()['count'];

$arResult['ROWS'] = [];
$isB2bTab = false;
if(strpos($arParams['DETAIL_URL'], 'b2b') !== false)
{
    $isB2bTab = true;
}

$files = [];

if($arResult['ITEMS'])
{
    foreach ($arResult['ITEMS'] as $item)
    {
        if($item['PROPERTIES']['DOCUMENT']['VALUE'] > 0)
        {
            $files[$item['PROPERTIES']['DOCUMENT']['VALUE']] = FileController::urlGenerate(
                'fileDownload',
                [
                    'fileId' => $item['PROPERTIES']['DOCUMENT']['VALUE'],
                    'fileName' => addslashes($item['~NAME']),
                ],
            );
        }
    }

//	$doc = new Doc();
//	$buyers = $doc->getBuyersByInn();

    $COMPANY_DETAIL_PATH = Option::get('sotbit.b2bcabinet', 'ADDRESS_COMPANY', '', SITE_ID);
    $ORDER_DETAIL_PATH = Option::get('sotbit.b2bcabinet', 'ADDRESS_ORDER', '', SITE_ID);

    $company = new Buyers(SITE_ID);
    $inn = $company->getInnProps();

    foreach ($arResult['ITEMS'] as $item)
    {
        $showCompany = '';
        $showOrder = '';

        $companyValues = $company->getCompanyByInn(trim($item['PROPERTIES']['ORGANIZATION']['VALUE']));

        if ($companyValues["ID"] && $companyValues["NAME"] && !empty($COMPANY_DETAIL_PATH)) {
            $companyPath = preg_replace("/#.*#/", $companyValues["ID"], $COMPANY_DETAIL_PATH);
            $showCompany .= '<p><a href="'.$companyPath.'">'.$companyValues["NAME"].' ('.$item['PROPERTIES']['ORGANIZATION']['VALUE'].')'.'</a></p>';
        } else {
            $showCompany = '<span class="not-found">'.$item['PROPERTIES']['ORGANIZATION']['VALUE'].'</span>';
        }

        if(Loader::includeModule("sotbit.auth") && Option::get("sotbit.auth", "EXTENDED_VERSION_COMPANIES", "N") == "Y"){
            if(!empty($ORDER_DETAIL_PATH) && is_array($item['PROPERTIES']['ORDER']['VALUE'])) {
                foreach ($item['PROPERTIES']['ORDER']['VALUE'] as $accountNumber)
                {
                    if (!$order = Sale\Order::loadByAccountNumber($accountNumber)){
                        continue;
                    }

                    /** @var Sale\PropertyValueCollectionBase */
                    $orderProperty = $order->getPropertyCollection();
                    $b2bOrder = new B2bCabinet\Shop\Order($order->toArray());
                    foreach ($inn as $i) {
                        $orderPropertyValue = $orderProperty->getItemByOrderPropertyId($i);

                        if (empty($orderPropertyValue)) {
                            continue;
                        }

                        $orderIsset = false;
                        if($resCompProps = \Sotbit\Auth\Internals\CompanyPropsValueTable::getList([
                            'filter' => ['PROPERTY_ID' => $orderPropertyValue->getField("ORDER_PROPS_ID"), 'VALUE' => $orderPropertyValue->getField("VALUE")],
                            'select' => ['COMPANY_ID']
                        ])->fetch()) {
                            if(\Sotbit\Auth\Internals\StaffTable::getList([
                                'filter' => ['COMPANY_ID' => $resCompProps["COMPANY_ID"], "USER_ID" => $USER->GetID()]
                            ])->fetch()){
                                $orderIsset = true;
                                $orderPath = $b2bOrder->getUrl($ORDER_DETAIL_PATH);
                                $showOrder .= '<p><a href="'.$orderPath.'">'.$b2bOrder->getDisplayId().'</a></p>';
                            }
                        }
                    }
                }
            }
        }
        else{
            if(Loader::includeModule("sale")){
                if(!empty($ORDER_DETAIL_PATH) && $item['PROPERTIES']['ORDER']['VALUE']){
                    $orderPath = '';
                    foreach ($item['PROPERTIES']['ORDER']['VALUE'] as $accountNumber){
                        if (!$order = Sale\Order::loadByAccountNumber($accountNumber)){
                            continue;
                        }
                        $b2bOrder = new B2bCabinet\Shop\Order($order->toArray());

                        if ($isSetOrder)
                        {
                            $showOrder .= '<p><span  class="not-found">'.$b2bOrder->getDisplayId().'</span></p>';
                        } else {
                            $orderPath = $b2bOrder->getUrl($ORDER_DETAIL_PATH);
                            $showOrder .= '<p><a href="'.$orderPath.'">'.$accountNumber.'</a></p>';
                        }
                    }
                }
            }
        }

        $actions = [];
        $name = $item["NAME"];


        if($files[$item['PROPERTIES']['DOCUMENT']['VALUE']])
        {
            $name = '<a href="' . $files[$item['PROPERTIES']['DOCUMENT']['VALUE']] . '">' . $name. '</a>';
            $actions = [
                [
                    "TEXT" => Loc::getMessage('DOC_DOWNLOAD'),
                    "HIDETEXT" => "Y",
                    "ONCLICK"=>"window.location.href='" . FileController::urlGenerate(
                        'fileDownload',
                        ['fileId' => $item['PROPERTIES']['DOCUMENT']['VALUE'], 'fileName' => addslashes($item['~NAME'])],
                    ) . "'",
                    "DEFAULT" => true,
                    "ICON" => "ph-download-simple"
                ]
            ];
        }
        $arResult['ROWS'][] = [
            'data' => [
                "ID" => Loc::getMessage('DOC_ROW_ID', ['#ID#' => $item['ID']]),
                "NAME" => $name,
                'DATE_CREATE' => $item["DATE_CREATE"],
                'DATE_UPDATE' => $item["TIMESTAMP_X"],
                'ORDER' => $showOrder,
                'ORGANIZATION' => $showCompany,
            ],
            'actions' => $actions,
            'COLUMNS' => [
                "ID" => $item['ID'],
                "NAME" => $item["NAME"],
                'DATE_CREATE' => $item["DATE_CREATE"],
                'DATE_UPDATE' => $item["TIMESTAMP_X"],
                'ORDER' => $showOrder,
                'ORGANIZATION' => $showCompany,
            ],
            'editable' => true,
        ];
    }
}

?>