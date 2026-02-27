<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Sotbit\B2bCabinet\Helper;
use Bitrix\Main\UI\Filter;
use Sotbit\B2BCabinet\Controller\FileController;
use Bitrix\Main\Context;
use Bitrix\Main\Config;
use Bitrix\Sale;
use Sotbit\B2bCabinet;


if (Loader::includeModule('sale') && Loader::includeModule('iblock')) {
    $arDocs = [];

    $request = Helper\Request::getInstance();
    $docIblockID = Helper\Document::getIblocks();

    $innProperty["CODE"] = Config\Option::get('sotbit.auth',
        'GROUP_ORDER_INN_FIELD_' . $arResult["PERSON_TYPE_ID"], '', SITE_ID);

    if ($innProperty["CODE"]) {
        foreach ($arResult["PROP"] as $companyProperty) {
            if ($companyProperty["COMPANY_PROPERTY_CODE"] == $innProperty["CODE"]) {
                $innProperty = [
                    "ID" => $companyProperty["PROPERTY_ID"],
                    "VALUE" => $companyProperty["VALUE"],
                ];
            }
        }
    }

    // Delete document
    if (isset($_REQUEST['DOC_DELETE']) && $request->get('DOC_DELETE') === 'Y' && !empty($request->get('DOC_ID'))) {
        $docId = (int)$request->get('DOC_ID');

        if (!empty($docId) && !empty($docIblockType) && !empty($docIblockID)) {
            if (Helper\Document::checkPermissionElement($docId)) {
                $DB->StartTransaction();
                if (!CIBlockElement::Delete($docId)) {
                    $strWarning .= 'Error!';
                    $DB->Rollback();
                } else {
                    $DB->Commit();
                }
            }
        }
    }

    $arResult['ROWS'] = [];

    if ($innProperty["VALUE"]) {

        $filter = [];
        $filterOption = new Filter\Options('DOCUMENTS_LIST');
        $filterData = $filterOption->getFilter([]);

        foreach ($filterData as $key => $value) {
            if (in_array($key, ['ID', 'NAME', 'DATE_CREATE_from', 'DATE_CREATE_to', 'FIND'])) {
                switch ($key) {
                    case 'NAME':
                        $filter['%NAME'] = $value;
                        break;
                    case 'DATE_CREATE_from':
                        $filter['>=DATE_CREATE'] = $value;
                        break;
                    case 'DATE_CREATE_to':
                        $filter['<=DATE_CREATE'] = $value;
                        break;
                    case 'ID':
                        $filter['=ID'] = $value;
                        break;
                    default:
                        $filter['%NAME'] = $value;
                }
            }
        }
        $by = isset($_GET['by']) ? $request->get('by') : (isset($arParams["SORT_BY1"]) ? $arParams["SORT_BY1"] : '');
        $order = isset($_GET['order']) ? strtoupper($request->get('order')) : (isset($arParams["SORT_ORDER1"]) ? $arParams["SORT_ORDER1"] : '');

        if ($by == 'DATE_UPDATE') {
            $by = 'TIMESTAMP_X';
        }

        $filter = array_merge($filter,
            array(
                "IBLOCK_ID" => $docIblockID,
                "PROPERTY_ORGANIZATION" => $innProperty["VALUE"],
            )
        );

        $resDocs = CIBlockElement::GetList(
            [$by => $order],
            $filter,
            false,
            false
        );

        while ($res = $resDocs->GetNextElement()) {
            $tmp = $res->GetFields();

            $arDocs[$tmp['ID']] = $tmp;
            $props = $res->GetProperties();
            if (is_array($props)) {
                if (!empty($props['DOCUMENT']['VALUE'])) {
                    $props['DOCUMENT']['DOCS'] = Bitrix\Main\FileTable::getById($props['DOCUMENT']['VALUE'])->fetch();
                }

                $arDocs[$tmp['ID']] = array_merge($arDocs[$tmp['ID']], $props);
            }
        }


        $company = new \Sotbit\B2BCabinet\Personal\Buyers(SITE_ID);
        $arResult["SALE_PROFILE_ID"] = $company->getProfileId($innProperty["VALUE"]) ?: '';
    }

}

foreach ($arDocs as $val) {
    $fileDownloadUrl = FileController::urlGenerate(
        'fileDownload',
        ['fileId' => $val['DOCUMENT']['VALUE'], 'fileName' => addslashes($val['~NAME'])],
    );

    $documentDeletUrl = FileController::urlGenerate(
        'DocumentDelete',
        ['documentID' => $val['ID'], 'backUrl' => Context::getCurrent()->getRequest()->getRequestUri()],
    );

    $aActions = array(
        array(
            "TEXT" => Loc::getMessage('OPP_DOWNLOAD_DOC'),
            "HIDETEXT"=>"Y",
            "ONCLICK"=>"window.location.href='" . $fileDownloadUrl . "'",
            "DEFAULT" => true,
            "ICON" => "ph-download-simple"
        ),
        array(
            "TEXT" => Loc::getMessage('OPP_DELETE_DOC'),
            "HIDETEXT"=>"Y",
            "ONCLICK"=>"if(confirm('". Loc::getMessage('OPP_DELETE_CONFIRM_MESS') ."')) ". "window.location.href='" . $documentDeletUrl . "'",
            "ICON"=>"ph-trash",
            "HIDDEN"=>"Y"),
    );

    $linkOrders = '';
    $company =  new \Sotbit\B2BCabinet\Personal\Buyers(SITE_ID);
    $inn = $company->getInnProps();
    if (!empty($val['ORDER']['VALUE'])) {
        $ORDER_DETAIL_PATH = Config\Option::get('sotbit.b2bcabinet', 'ADDRESS_ORDER', '', SITE_ID);
        foreach ($val['ORDER']['VALUE'] as $orderId) {
            $order = Sale\Order::loadByAccountNumber($orderId);
            if (!$order) {
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
                        $orderUrl = $b2bOrder->getUrl($ORDER_DETAIL_PATH);
                        $oderDisplayId = $b2bOrder->getDisplayId();
                        $linkOrders .= '<p><a href="' . $orderUrl . '" target="__blank">' . $oderDisplayId . '</a></p>';
                    }
                }
            }

        }

    }

    array_push($arResult['ROWS'], [
        'data' => array_merge(
            [
                "ID" => $val['ID'],
                "NAME" => '<a href="' . $fileDownloadUrl . '" target="__blank">' . $val['NAME'] . '</a>',
                "DATE_CREATE" => $val['DATE_CREATE'],
                "ORDER" => $linkOrders
            ]
        ),
        'actions' => $aActions,
        'COLUMNS' => $aCols,
        'editable' => true,
    ]);
}

if (isset($_GET['by']) && !in_array($_GET['by'], [
        'ID',
        'NAME',
        'DATE_UPDATE',
        'PERSON_TYPE_NAME'
    ])) {
    $by = $request->get('by');
    $order = in_array(strtolower($request->get('order')), [
        'asc',
        'desc'
    ]) ? strtolower($request->get('order')) : 'asc';

    for ($i = 0; $i < count($arResult['ROWS']); $i++) {
        for ($j = 0; $j < count($arResult['ROWS']) - 1; $j++) {
            $change = false;
            $t = [];

            if ($order == 'desc' && strcmp($arResult['ROWS'][$i]['data'][$by],
                    $arResult['ROWS'][$j]['data'][$by]) > 0) {
                $change = true;
            } elseif ($order == 'asc' && strcmp($arResult['ROWS'][$i]['data'][$by],
                    $arResult['ROWS'][$j]['data'][$by]) < 0) {
                $change = true;
            }

            if ($change) {
                $t = $arResult['ROWS'][$j];
                $arResult['ROWS'][$j] = $arResult['ROWS'][$i];
                $arResult['ROWS'][$i] = $t;
            }
        }
    }
}

