<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!\Bitrix\Main\Loader::includeModule('iblock'))
    return;
$boolCatalog = \Bitrix\Main\Loader::includeModule('catalog');

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
    $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
global $MESS;
//printr($MESS);

$arTemplateParameters['IBLOCK_TYPE'] = array(
    "NAME" => GetMessage("IBLOCK_TYPE"),
    "TYPE" => "LIST",
    "VALUES" => $arIBlockType,
    "REFRESH" => "Y",
);

$arTemplateParameters['IBLOCK_ID'] = array(
    "NAME" => GetMessage("IBLOCK_IBLOCK"),
    "TYPE" => "LIST",
    "ADDITIONAL_VALUES" => "Y",
    "VALUES" => $arIBlock,
    "REFRESH" => "Y",
);

$arSKU = false;
$boolSKU = false;
if ($boolCatalog && (isset($arCurrentValues['IBLOCK_ID']) && 0 < intval($arCurrentValues['IBLOCK_ID'])))
{
    $arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
    $boolSKU = !empty($arSKU) && is_array($arSKU);
}


if (isset($arCurrentValues['IBLOCK_ID']) && 0 < intval($arCurrentValues['IBLOCK_ID']))
{
    $arAllPropList = array();
    $arFilePropList = array(
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
    );
    $arListPropList = array(
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
    );
    $arHighloadPropList = array(
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
    );
    $arElementPropList = array(
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
    );
    $rsProps = CIBlockProperty::GetList(
        array('SORT' => 'ASC', 'ID' => 'ASC'),
        array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y')
    );
    while ($arProp = $rsProps->Fetch())
    {
        $strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
        if ('' == $arProp['CODE'])
            $arProp['CODE'] = $arProp['ID'];
        $arAllPropList[$arProp['CODE']] = $strPropName;
        if ('F' == $arProp['PROPERTY_TYPE'])
            $arFilePropList[$arProp['CODE']] = $strPropName;
        if ('L' == $arProp['PROPERTY_TYPE'])
            $arListPropList[$arProp['CODE']] = $strPropName;
        if ('E' == $arProp['PROPERTY_TYPE'])
            $arElementPropList[$arProp['CODE']] = $strPropName;
        if('S' == $arProp['PROPERTY_TYPE'] && 'directory' != $arProp['USER_TYPE'])
            $arStringPropList[$arProp['CODE']] = $strPropName;
        if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
            $arHighloadPropList[$arProp['CODE']] = $strPropName;
    }
}

if($boolSKU)
{
    $arAllOfferPropList = array();
    $arFileOfferPropList = array(
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
    );
    $arTreeOfferPropList = array(
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
    );
    $rsProps = CIBlockProperty::GetList(
        array('SORT' => 'ASC', 'ID' => 'ASC'),
        array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
    );
    while ($arProp = $rsProps->Fetch())
    {
        if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
            continue;
        $arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
        $strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
        if ('' == $arProp['CODE'])
            $arProp['CODE'] = $arProp['ID'];
        $arAllOfferPropList[$arProp['CODE']] = $strPropName;
        if ('F' == $arProp['PROPERTY_TYPE'])
            $arFileOfferPropList[$arProp['CODE']] = $strPropName;
        if ('N' != $arProp['MULTIPLE'])
            continue;
        if (
            //'L' == $arProp['PROPERTY_TYPE']
            //|| 'E' == $arProp['PROPERTY_TYPE']
            /*||*/ ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
        )
            $arTreeOfferPropList[$arProp['CODE']] = $strPropName;
    }
}

$arTemplateParameters['ARTICLE_PROPERTY_CODE'] = array(
    'NAME' => GetMessage('MS_TPL_OFFER_ARTICLE_PROPERTY_CODE'),
    'TYPE' => 'LIST',
    'MULTIPLE' => 'N',
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'N',
    'VALUES' => $arStringPropList
);
$arTemplateParameters['ARTICLE_OFFER_PROPERTY_CODE'] = array(
    'NAME' => GetMessage('MS_TPL_OFFER_ARTICLE_OFFER_PROPERTY_CODE'),
    'TYPE' => 'LIST',
    'MULTIPLE' => 'N',
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'N',
    'VALUES' => $arStringPropList
);
?>