<?

use Bitrix\Main\Config\Option;
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Sotbit\Auth\Company;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
global $USER;

$rsPersonTypes = \Bitrix\Sale\Internals\PersonTypeTable::getList(
    [
        'filter' => [
            'PERSON_TYPE_SITE.SITE_ID' => SITE_ID,
            'ID' => $arParams['BUYER_PERSONAL_TYPE'],
            'ACTIVE' => "Y"
        ],
        'select' => ["ID", "NAME"]
    ]
);

while ($arPersonTypes = $rsPersonTypes->fetch()) {
    $arResult["PERSON_TYPES"][$arPersonTypes["ID"]] = $arPersonTypes["NAME"];
}


$arParams['GRID_HEADER'][] = [
    'id'       => 'ACTIVE',
    'name'     => Loc::getMessage('P_DATE_ACTIVE'),
    'sort'     => 'ACTIVE',
    'default'  => true,
    'editable' => false,
];

$arParams['GRID_HEADER'][] = [
    'id'       => 'STATUS',
    'name'     =>  Loc::getMessage('P_DATE_STATUS'),
    'sort'     => 'STATUS',
    'default'  => true,
    'editable' => false,
];

$arResult['ROWS'] = [];

foreach($arResult["PROFILES"] as $val) {
    $aActions = [];

    $aActions[] = [
        "TEXT"      => GetMessage('SPOL_DETAIL_PROFIL'),
        "ONCLICK"   => "location.assign('".$val["URL_TO_DETAIL"]."')",
        "DEFAULT"   => true,
    ];

    if(Company\Company::isUserAdmin((int)($USER->GetID()), $val['ID']) && $val['STATUS'] === 'A') {
        $aActions[] = [
            "TEXT"      => GetMessage('SPOL_DETAIL_EDIT'),
            "HIDETEXT"  => "Y",
            "ONCLICK"   => "location.assign('".dirname($val["URL_TO_DETAIL"]).'/add.php?EDIT_ID='.$val["ID"] ."')",
            "DEFAULT"   => true,
            "TYPE"      => "edit",
            "ICON"      => "ph-pencil-simple",
            "HIDDEN"    => "Y"
        ];
    }


    if($val['ACTIVE'] == 'N') {
        $val['ACTIVE'] = Loc::getMessage('P_DATE_ACTIVE_N');
    }
    else{
        $val['ACTIVE'] = Loc::getMessage('P_DATE_ACTIVE_Y');
    }

    $val['STATUS'] = Loc::getMessage('COMPANY_LIST_STATUS_' . $val['STATUS']);

    $arResult['ROWS'][] = [
        'data'     => $val,
        'actions'  => $aActions,
        'COLUMNS'  => $val,
        'editable' => true,
    ];
}