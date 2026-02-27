<?
$arResult['USER_ACCOUNT'] = $dbAccountCurrency = CSaleUserAccount::GetList(
    [],
    ['USER_ID' => $USER->GetID()],
    false,
    false,
    []
)->Fetch();
if(!empty($arResult['USER_ACCOUNT']) && !empty($arResult['USER_ACCOUNT']['CURRENT_BUDGET'])) {
    $arResult['USER_ACCOUNT']['FORMAT_CURRENT_BUDGET'] = CCurrencyLang::CurrencyFormat($arResult['USER_ACCOUNT']['CURRENT_BUDGET'], $arResult['USER_ACCOUNT']['CURRENCY'], true);
}
?>