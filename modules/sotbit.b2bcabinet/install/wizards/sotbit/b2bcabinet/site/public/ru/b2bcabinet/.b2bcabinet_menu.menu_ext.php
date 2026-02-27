<?
use Bitrix\Main\Config\Option;

if(\Bitrix\Main\Loader::includeModule('sotbit.offerlist') && SotbitOfferlist::getModuleEnable()) {
    $aMenuLinks[] = [
        "Предложения",
       "/b2bcabinet/offers/",
        [],
        [],
        ""
    ];
}

if(\Bitrix\Main\Loader::includeModule('sotbit.complaints') && Option::get("sotbit.complaints", "INCLUDE_COMPLAINTS", "N", SITE_ID) == "Y") {
    $aMenuLinks[] = [
        "Рекламации",
       "/b2bcabinet/complaints/",
        [],
        [],
        ""
    ];
}

if(\Bitrix\Main\Loader::includeModule('support')) {
    $aMenuLinks[] = [
        "Техническая поддержка",
       "/b2bcabinet/support/",
        [],
        [],
        ""
    ];
}
?>