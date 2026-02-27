<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\Internals\OrderPropsVariantTable;
use Bitrix\Sale\Internals\PersonTypeTable;
use Sotbit\Auth\User\WholeSaler;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(empty($arResult["AGREEMENT_ORIGINATOR_ID"])) {
    $arResult["AGREEMENT_ORIGINATOR_ID"] = "main/reg";
}

if(empty($arResult["AGREEMENT_ORIGIN_ID"])) {
    $arResult["AGREEMENT_ORIGIN_ID"] = "register";
}

if(empty($arResult["AGREEMENT_INPUT_NAME"])) {
    $arResult["AGREEMENT_INPUT_NAME"] = "USER_AGREEMENT";
}

$_wholesaler = new WholeSaler();
$_groups = $_wholesaler->getPersonType();
$arResult['PERSON_GROUPS'] = $_groups;
$_fields = [];
$_userFields = [];
$_registerFieldsRequired = [];
$_allUserFields = [];
foreach ($_groups as $group) {
    $_fields[$group] = unserialize(Option::get(SotbitAuth::idModule, 'GROUP_FIELDS_' . $group, '', SITE_ID));
    $_registerFieldsRequired[$group] = unserialize(Option::get(SotbitAuth::idModule, 'GROUP_REQUIRED_FIELDS_' . $group, '', SITE_ID));
    $_userFields[$group] = unserialize(Option::get(SotbitAuth::idModule, 'USER_DOP_FIELDS_' . $group, '', SITE_ID));
    $dbOrderOptFields = unserialize(Option::get(SotbitAuth::idModule, 'GROUP_ORDER_FIELDS_' . $group, '', SITE_ID));
    $companyName = Option::get(\SotbitAuth::idModule, 'COMPANY_PROPS_NAME_FIELD_' . $group);

    $_registerFieldsRequired[$group][] = $companyName;

    if ($_registerFieldsRequired[$group]) {
        $dbOrderOptFields = array_merge(is_array($dbOrderOptFields) ? $dbOrderOptFields : [], $_registerFieldsRequired[$group]);
    }

    $select = ['ID','CODE','NAME', 'REQUIRED', 'SETTINGS', 'PERSON_TYPE_ID', 'DESCRIPTION', 'TYPE', 'DEFAULT_VALUE', 'MULTIPLE', 'SORT'];
    $_orderFields[$group] = OrderPropsTable::query()
        ->setSelect($select)
        ->where('PERSON_TYPE_ID', $group)
        ->addOrder('SORT', 'ASC')
        ->fetchAll()
    ;

    $massEnumProp = [];
    foreach($_orderFields[$group] as $key => $val){
        if ($val['TYPE'] == "ENUM")
            $massEnumPropId[] = $val['ID'];
    }
    if (!empty($massEnumPropId)) {
        $select = ['ID','NAME', 'VALUE', 'ORDER_PROPS_ID'];
        $variantEnumPropObj = OrderPropsVariantTable::getList([ 'select' => $select, 'filter' => ['ORDER_PROPS_ID' => $massEnumPropId]] );
        while($variantEnumProp = $variantEnumPropObj->fetch()){
            $massEnumProp[$variantEnumProp['ORDER_PROPS_ID']][] = $variantEnumProp;
        }
    }
    foreach($_orderFields[$group] as $key => $val){
        if ($val['TYPE'] == "ENUM" && array_key_exists($val['ID'], $massEnumProp)) {
            $_orderFields[$group][$key]['VARIANTS'] = $massEnumProp[$val['ID']];
        }
    }

    $_orderFields[$group] = array_filter($_orderFields[$group], function($i) use($dbOrderOptFields) {
        return in_array($i['CODE'], is_array($dbOrderOptFields) ? $dbOrderOptFields : []);
    });

    foreach ($_fields[$group] as $val)
        $arResult['OPT_FIELDS'][$group][] = $val;

    foreach ($_userFields[$group] as $val)
        $arResult['OPT_FIELDS'][$group][] = $val;

    if (!empty($_userFields[$group]))
        $_allUserFields = empty($_allUserFields) ? array_values($_userFields[$group]) :  (!empty($_userFields[$group]) ? array_merge($_allUserFields, array_values($_userFields[$group])) : $_allUserFields);

}

$dbUserFields = CUserTypeEntity::GetList(array($by => $order), array("ENTITY_ID" => "USER", "FIELD_NAME" => $_allUserFields, "LANG" => LANGUAGE_ID));
$dopUserFields = [];
while ($resUserFields = $dbUserFields->Fetch()) {
    $arResult['OPT_FIELDS_FULL'][$resUserFields["FIELD_NAME"]] = $resUserFields;
    if ($arResult['OPT_FIELDS_FULL'][$resUserFields["FIELD_NAME"]]["MANDATORY"] == "Y" ) {
        foreach($_groups as $val)
            $_registerFieldsRequired[$val][] = $resUserFields["FIELD_NAME"];
    }
}

$arResult["PERSON_TYPES"] = array_values($arResult["PERSON_TYPES"]);
$arResult['OPT_ORDER_FIELDS'] = $_orderFields;
$arResult['OPT_FIELDS_REQUIRED'] = $_registerFieldsRequired;