<?
use Bitrix\Main\Config\Option;

if(\Bitrix\Main\Loader::includeModule('sotbit.offerlist') && SotbitOfferlist::getModuleEnable()) {
    $aMenuLinks[] = [
        "Предложения",
        "/offers/",
        [],
        [],
        ""
    ];
}


if(\Bitrix\Main\Loader::includeModule('sotbit.complaints') && Option::get("sotbit.complaints", "INCLUDE_COMPLAINTS", "N", SITE_ID) == "Y") {
    $aMenuLinks[] = [
        "Рекламации",
        "/complaints/",
        [],
        [],
        ""
    ];
}
?>