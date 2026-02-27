<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss($arGadget['PATH_SITEROOT'].'/styles.css');

$APPLICATION->IncludeComponent(
    "sotbit:sotbit.personal.manager",
    "b2bcabinet_gadget",
    array(
        "COMPONENT_TEMPLATE" => "b2bcabinet_gadget",
        "SHOW_FIELDS" => array(
            0 => "NAME",
            1 => "PERSONAL_PHOTO",
            2 => "WORK_PHONE",
            3 => "UF_P_MANAGER_EMAIL",
        ),
        "USER_PROPERTY" => array(
        ),
        "NAME_TEMPLATE" => $arGadget["SETTINGS"]["PERSONAL_MANAGER_NAME_TEMPLATE"] ?: "#NOBR##NAME# #LAST_NAME##/NOBR#"
    ),
    false
);

?>