<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if ($arResult["ROLES"]) {
    foreach ($arResult["ROLES"] as $role) {
        $arResult["FILTER_ROLES"][$role["ID"]] = $role["NAME"];
    }
}

if (!empty($arResult['CONFIRM_N']['ROWS'])) {
    foreach ($arResult['CONFIRM_N']['ROWS'] as &$item) {
        $item['actions'][0]['CLASS'] = 'btn btn-primary';
        $item['actions'][1]['CLASS'] = 'btn btn-link';
    }
}
