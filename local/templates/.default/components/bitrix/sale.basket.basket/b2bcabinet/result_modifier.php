<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Sotbit\Multibasket\Helpers;

$arResult['headers']['props'] = [];
$arResult['headers']['column_list'] = [];

foreach ($arResult['BASKET_ITEM_RENDER_DATA'] as $row) {
    foreach ($row['COLUMN_LIST'] as $col) {
        if(!empty($col['CODE']) && !empty($col['NAME']))
            $arResult['headers']['column_list'][$col['CODE']] = $col['NAME'];
    }
}

if(empty($arParams['IMAGE_SIZE_PREVIEW']))
    $arParams['IMAGE_SIZE_PREVIEW'] = 23;

$arResult['templateColums'] = [
    'PRICE' => GetMessage("SBB_BASKET_SUM"),
    'QUANTITY' => GetMessage("SBB_BASKET_QUANTITY"),
    'SUM' => GetMessage("SBB_TOTAL"),
    'DELETE' => ''
];

$arResult['module_multibasket_is_includet'] = Loader::includeModule('sotbit.multibasket')
    && Helpers\Config::moduleIsEnabled(SITE_ID);