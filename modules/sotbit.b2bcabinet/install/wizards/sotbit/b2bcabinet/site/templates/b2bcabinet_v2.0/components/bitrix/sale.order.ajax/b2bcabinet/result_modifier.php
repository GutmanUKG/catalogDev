<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$arResult["EXTENDED_VERSION_COMPANIES"] = (Loader::includeModule('sotbit.auth') && Option::get(\SotbitAuth::idModule, "EXTENDED_VERSION_COMPANIES", "N") === 'Y') ? 'Y' : 'N';

if (Bitrix\Main\Loader::includeModule('Sale') && !empty($arParams['BUYER_PERSONAL_TYPE'])) {
    $result = [];
    $dbSales = CSaleOrderUserProps::GetList(
        array("DATE_UPDATE" => "DESC"),
        array(
            "USER_ID" => $USER->GetID(),
            "PERSON_TYPE_ID" => $arParams['BUYER_PERSONAL_TYPE']
        )
    );

    while ($arrProfiles = $dbSales->Fetch()) {
        $result[$arrProfiles['ID']] = $arrProfiles;

        if (count($result) == 1) {
            $dbPropVals = CSaleOrderUserPropsValue::GetList(
                array("ID" => "ASC"),
                array("USER_PROPS_ID" => $arrProfiles['ID'])
            );

            while ($arPropVals = $dbPropVals->Fetch()) {
                $result[$arrProfiles['ID']]['PROPS'][$arPropVals['ID']] = $arPropVals;
            }
        }
    }


    $arResult['PERSON_PROFILE'] = [];
    if (!empty($result)) {
        $arResult['PERSON_PROFILE'] = $result;
    }

    if((defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES == "Y") || (count($arResult['PERSON_PROFILE']) <= 1)) {
        $arParams['ALLOW_USER_PROFILES'] = "N";
    }

    $arResult['TRUE_PT'] = false;
    foreach ($arResult["PERSON_TYPE"] as $pt) {
        if ($pt['CHECKED'] === 'Y' && in_array($pt['ID'], $arParams['BUYER_PERSONAL_TYPE'])) {
            $arResult['TRUE_PT'] = true;
        }
    }
    if (!$arResult['PERSON_PROFILE']) {
        $arResult['TRUE_PT'] = false;
    }

    $userProfiles = is_array($arResult["ORDER_PROP"]["USER_PROFILES"]) ? $arResult["ORDER_PROP"]["USER_PROFILES"] : [];

    foreach ($userProfiles as $idProfile => $profile) {
        if ($profile['CHECKED'] === 'Y' && $arResult['PERSON_PROFILE'][$idProfile]) {
            $arResult['PERSON_PROFILE'][$idProfile]['CHECKED'] = 'Y';
            $PERSON_TYPE_ID = $arResult['PERSON_PROFILE'][$idProfile]["PERSON_TYPE_ID"];
            break;
        }
    }

}
$useReplace = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
$replaceValue = null;
if ($useReplace) {
    $replaceableValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
    $replaceValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);
    foreach ($arResult['JS_DATA']['GRID']['ROWS'] as $key => $val) {
        if (!empty($val['data']['DETAIL_PAGE_URL']))
            $arResult['JS_DATA']['GRID']['ROWS'][$key]['data']['DETAIL_PAGE_URL'] = str_replace($replaceableValue, $replaceValue, $val['data']['DETAIL_PAGE_URL']);
    }
}


$arResult['JS_DATA']['USER_PROFILES'] = $arResult['PERSON_PROFILE'];

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);