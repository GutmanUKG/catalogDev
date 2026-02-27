<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Sotbit\B2bCabinet;

foreach($arResult["ORDERS"] as $val)
{
    $arResult["ORDER_BY_STATUS"][$val["ORDER"]["STATUS_ID"]][] = $val;
}

$methodIstall = Option::get('sotbit.b2bcabinet', 'method_install', '', SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR.'b2bcabinet/' : SITE_DIR;

$filterOption = new Bitrix\Main\UI\Filter\Options('ORDER_LIST');
$filterData = $filterOption->getFilter([]);
$arResult['FILTER_STATUS_NAME'] = (isset($filterData['STATUS'])) ? $arResult['INFO']['STATUS'][$filterData['STATUS']]['NAME']: '';

$buyers = [];
$innProps = [];
$orgProps = [];
$pt = unserialize(Option::get("sotbit.auth", "WHOLESALERS_PERSON_TYPE", "", SITE_ID));
if(!is_array($pt)) {
    $pt = [];
}

if ($pt) {
    foreach ($pt as $profileId) {
        if ($profileCode = Option::get('sotbit.auth', 'GROUP_ORDER_INN_FIELD_' . $profileId)) {
            $innProps[] = $profileCode;
        }

        if ($profileName = Option::get('sotbit.auth', 'COMPANY_PROPS_NAME_FIELD_' . $profileId)) {
            $orgProps[] = $profileName;
        }
    }
}


$idBuyers = [];
$rs = CSaleOrderUserProps::GetList(
    array("DATE_UPDATE" => "DESC"),
    array(
        "PERSON_TYPE_ID" => $pt,
        "USER_ID" => (int)$USER->GetID()
    )
);
while($buyer = $rs->Fetch())
{
    $idBuyers[]=$buyer['ID'];
}

if($idBuyers)
{
    $rs = \Bitrix\Sale\Internals\UserPropsValueTable::getList(
        array(
            'filter' => array(
                "USER_PROPS_ID" => $idBuyers,
                "ORDER_PROPS_CODE" => array_merge($innProps, $orgProps),
            ),
            "select" => array("ORDER_PROPS_ID",'USER_PROPS_ID','VALUE',  "ORDER_PROPS_CODE" => "PROPERTY.CODE")
        )
    );
    while($prop = $rs->Fetch())
    {
        if(in_array($prop['ORDER_PROPS_CODE'],$innProps))
        {
            $buyers[$prop['USER_PROPS_ID']]['INN'] = $prop['VALUE'];
        }
        if(in_array($prop['ORDER_PROPS_CODE'],$orgProps))
        {
            $buyers[$prop['USER_PROPS_ID']]['ORG'] = $prop['VALUE'];
        }
    }
}

$arResult['BUYERS'] = [];

if($buyers)
{
    foreach($buyers as $id=>$v)
    {
        $name = $v['ORG'];
        $name .= ($v['INN'])?' ('.$v['INN'].')':'';
        $arResult['BUYERS'][$id] = $name;
    }
}


foreach($arResult['ORDERS'] as $key => $arOrder)
{

    if(isset($filterData['FIND']) && !empty($filterData['FIND']) && $filterData['FIND'] != $arOrder['ORDER']['ID']) {
        unset($arResult['ORDERS'][$key]);
        continue;
    }

    $idOrders[] = $arOrder['ORDER']['ID'];
}


$rs = \Bitrix\Sale\Internals\OrderPropsValueTable::getList([
    'filter' => [
        'ORDER_ID' => $idOrders,
        'CODE' => $innProps
    ]
]);
while($org = $rs->fetch())
{
    $company = \Sotbit\Auth\Internals\CompanyPropsValueTable::getList([
        'filter' => [
            'VALUE'=>$org["VALUE"]
        ],
        'select' => ['COMPANY_ID', 'COMPANY_NAME' => 'COMPANY.NAME'],
    ])->fetch();


    if($company["COMPANY_ID"]){
        $name =  $company["COMPANY_NAME"];
        $name .= ( $org["VALUE"])?' ('. $org["VALUE"].')':'';
        $orgs[$org['ORDER_ID']] = '<a href="'. $methodIstall .'personal/companies/profile_detail.php?ID='. $company["COMPANY_ID"] .'">'. $name .'</a>';
    }
}

$dbstatus = CSaleStatus::GetList(
    [], [] , false, false, ["ID", "NAME", "COLOR"]
);
while($resultStatus = $dbstatus->Fetch()){

    $arResult["ORDER_STATUS"][$resultStatus["ID"]] = $resultStatus["NAME"];
    $color = '';
    if ($resultStatus["COLOR"]) {
        $color = '<span class="d-inline-block align-middle wmin-0 w-16px h-16px badge bg-opacity-20 rounded-pill me-2" style="background-color: '.$resultStatus["COLOR"].'!important; color: '.$resultStatus["COLOR"].'!important;"></span>';
    }
    $arResult['INFO']['STATUS'][$resultStatus["ID"]]["PRINT"] = '<div class="b2b-orderlist-status">'.$color.'<span class="align-middle">'.$resultStatus["NAME"].'</span><span class="badge bg-danger bg-opacity-20 text-danger rounded-pill ms-2">#CANCELED#</span></div>';
}

$dbpaySystem = CSalePaySystem::GetList(
    [], [] , false, false, ["ID", "NAME"]
);
while($resultPaySystem = $dbpaySystem->Fetch()){
    $arResult["PAY_SYSTEM"][$resultPaySystem["ID"]] = $resultPaySystem["NAME"];
}

$dbDelivery = CSaleDelivery::GetList(
    [], [] , false, false, ["ID", "NAME"]
);
while($resultDelivery = $dbDelivery->Fetch()){
    $arResult["DELIVERY"][$resultDelivery["ID"]] = $resultDelivery["NAME"];
}

$defaultFilter =  array(
    'PAY_SYSTEM_ID',
    'DELIVERY_ID',
    'BUYER'
);

$filter = [];
$filterOption = new Bitrix\Main\UI\Filter\Options( 'ORDER_LIST' );
$filterData = $filterOption->getFilter( [] );

foreach( $filterData as $key => $value )
{
    if( in_array($key, $defaultFilter) && !empty($value))
        $filter[$key] = $value;
}

if( $filterData['BUYER'] )
{
    $orders = [];
    $rs = \Bitrix\Sale\Internals\OrderTable::getList( [
        'filter' => [
            'USER_ID' => $USER->GetID()
        ]
    ] );
    while ( $order = $rs->fetch() )
    {
        $orders[] = $order['ID'];
    }
    if( $orders )
    {
        $innV = [];
        $innProps = unserialize( Bitrix\Main\Config\Option::get( 'sotbit.b2bcabinet', 'PROFILE_ORG_INN' ) );
        if( !is_array( $innProps ) )
        {
            $innProps = [];
        }
        $rs = \Bitrix\Sale\Internals\UserPropsValueTable::getList( array(
            'filter' => array(
                "USER_PROPS_ID" => $filterData['BUYER'],
                'ORDER_PROPS_ID' => $innProps
            ),
            "select" => array(
                "ORDER_PROPS_ID",
                'USER_PROPS_ID',
                'VALUE'
            )
        ) );
        while ( $buyer = $rs->fetch() )
        {
            $innV[] = $buyer['VALUE'];
        }

        $rOrders = [];
        $rs = \Bitrix\Sale\Internals\OrderPropsValueTable::getList( [
            'filter' => [
                'ORDER_ID' => $orders,
                'ORDER_PROPS_ID' => $innProps,
                'VALUE' => $innV
            ]
        ] );
        while ( $v = $rs->fetch() )
        {
            $rOrders[] = $v['ORDER_ID'];
        }

    }
}

foreach($arResult['ORDERS'] as $arOrder)
{
    if($filter){
        $continue = false;

        if($filter["BUYER"] && empty($rOrders)){
            $continue = true;
        }
        elseif($filter["BUYER"] && !empty($rOrders)){
            if(!in_array($arOrder["ORDER"]["ID"], $rOrders)){
                $continue = true;
            }
        }

        if($filter["DATE_INSERT_to"] && $filter["DATE_INSERT_from"]){
            if($arOrder["ORDER"]["DATE_INSERT"]->toString()>=$filter["DATE_INSERT_from"] && $arOrder["ORDER"]["DATE_INSERT"]->toString()<=$filter["DATE_INSERT_to"]){
                $continue = false;
            }
            else{
                $continue = true;
            }
        }

        foreach ($filter as $code => $value){
            if($code == "ACCOUNT_NUMBER" && $arOrder["ORDER"]["ACCOUNT_NUMBER"] != $value) {
                $continue = true;
                break;
            }
            if($code == "STATUS_ID" && $arOrder["ORDER"]["STATUS_ID"] != $value) {
                $continue = true;
                break;
            }
            if($code == "PAYED" && $arOrder["ORDER"]["PAYED"] != $value) {
                $continue = true;
                break;
            }
            if($code == "PAY_SYSTEM_ID" && $arOrder["ORDER"]["PAY_SYSTEM_ID"] != $value) {
                $continue = true;
                break;
            }
            if($code == "DELIVERY_ID" && $arOrder["ORDER"]["DELIVERY_ID"] != $value) {
                $continue = true;
                break;
            }
            if($code == "FIND" && $arOrder["ORDER"]["ACCOUNT_NUMBER"] != $value) {
                $continue = true;
                break;
            }
        }
    }

    if($continue){
        continue;
    }

    $urlToDitail = (new B2bCabinet\Shop\Order($arOrder['ORDER']))->getUrl($arParams["PATH_TO_DETAIL"]);
    $aActions = [
        ["TEXT" => GetMessage('SPOL_MORE_ABOUT_ORDER'), "ONCLICK" => "location.assign('".$urlToDitail."')" ],
    ];

    if(is_array($allowActions))
        foreach($allowActions as $licence)
            array_push($aActions, GetAction($licence, $arOrder));

    $payment = current($arOrder['PAYMENT']);
    $shipment = current($arOrder['SHIPMENT']);

    $items = array();
    $index = 1;
    foreach ($arOrder['BASKET_ITEMS'] as $item)
    {
        array_push($items, $index++.". $item[NAME] - ($item[QUANTITY] $item[MEASURE_TEXT])");
    }

    $arResult['ROWS'][] = array(
        'data' =>array_merge($arOrder['ORDER'], array(
            "ACCOUNT_NUMBER" => GetMessage('SPOL_ORDER_NUMBER', array('#ID#' => $arOrder['ORDER']['ACCOUNT_NUMBER'])),
            "SHIPMENT_METHOD" => $arResult["INFO"]["DELIVERY"][$arOrder["ORDER"]["DELIVERY_ID"]]["NAME"],
            "PAYMENT_METHOD" => $arResult["INFO"]["PAY_SYSTEM"][$arOrder["ORDER"]["PAY_SYSTEM_ID"]]["NAME"],
            'ITEMS' => implode('<br>', $items),
            'STATUS' => ($arOrder['ORDER']['CANCELED'] == 'Y' ? strtr($arResult['INFO']['STATUS'][$arOrder['ORDER']['STATUS_ID']]['PRINT'] , ["#CANCELED#" => Loc::GetMessage('SPOL_PSEUDO_CANCELLED') ]) :  strtr($arResult['INFO']['STATUS'][$arOrder['ORDER']['STATUS_ID']]['PRINT'] , ["#CANCELED#" => "" ])),
            'PAYED' => GetMessage('SPOL_'.($arOrder["ORDER"]["PAYED"] == "Y" ? 'YES' : 'NO')),
            'PAY_SYSTEM_ID' => $arOrder["ORDER"]["PAY_SYSTEM_ID"],
            'DELIVERY_ID' => $arOrder["ORDER"]["DELIVERY_ID"],
            'BUYER' => $orgs[$arOrder['ORDER']['ID']]
        ) ),
        'actions' => $aActions,
        'editable' => true,
    );
}

function GetAction($key, $arOrder)
{
    $arAction = array(
        'repeat' => array("TEXT"=>GetMessage('SPOL_REPEAT_ORDER'), "ONCLICK"=>"location.assign('".$arOrder['ORDER']["URL_TO_COPY"]."')"),
        'cancel' => array("TEXT"=>GetMessage('SPOL_CANCEL_ORDER'), "ONCLICK"=>"if(confirm('".GetMessage('SPOL_CONFIRM_DEL_ORDER')."')) window.location='".$arOrder['ORDER']["URL_TO_CANCEL"]."';"),
    );

    return $arAction[$key];
}