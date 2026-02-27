<?php

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Page\Asset,
    Sotbit\B2bCabinet\Helper\Config;

global $APPLICATION, $USER;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/plugins/ui/fab.js');
?>
</div>
<!-- /content area -->
<? if (preg_match('#blank_zakaza#', $APPLICATION->GetCurPage()) && $USER->IsAuthorized()):?>
<!-- Personal manager -->
<div class="personal-manager-wrapper">
    <? $APPLICATION->IncludeComponent(
        "sotbit:sotbit.personal.manager",
        "b2bcabinet_manager_buttons",
        array(
            "COMPONENT_TEMPLATE" => "b2bcabinet_manager_buttons",
            "SHOW_FIELDS" => array(
                0 => "NAME",
                1 => "PERSONAL_PHOTO",
                2 => "WORK_PHONE",
                3 => "UF_P_MANAGER_EMAIL",
            ),
            "USER_PROPERTY" => array(
                0 => "UF_P_MANAGER_ID",
            ),
            "NAME_TEMPLATE" => "#NOBR##NAME# #LAST_NAME##/NOBR#"
        ),
        false
    );
    ?>
</div>
<!-- /personal manager -->
<? endif; ?>

<!-- Footer -->
<div class="navbar navbar-sm navbar-footer">
    <div class="container-fluid justify-content-end mt-2 px-0">
        <a href=" https://sotbit.ru" class="navbar-link" target="_blank">
            <span><?= Loc::getMessage('DEVELOPED_COMPANY', array('#YEAR#' => date("Y"))) ?></span>
        </a>
    </div>
</div>
<!-- /footer -->

</div>
<!-- /inner content -->
</div>
<!-- /main content -->

<div class="feed-back-form">
    <? $APPLICATION->IncludeComponent(
        "bitrix:form",
        "b2b_cabinet_feed_back",
        array(
            "AJAX_MODE" => "Y",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "A",
            "CHAIN_ITEM_LINK" => "",
            "CHAIN_ITEM_TEXT" => "",
            "EDIT_ADDITIONAL" => "N",
            "EDIT_STATUS" => "N",
            "IGNORE_CUSTOM_TEMPLATE" => "N",
            "NOT_SHOW_FILTER" => "",
            "NOT_SHOW_TABLE" => "",
            "RESULT_ID" => $_REQUEST['RESULT_ID'],
            "SEF_MODE" => "N",
            "SHOW_ADDITIONAL" => "N",
            "SHOW_ANSWER_VALUE" => "N",
            "SHOW_EDIT_PAGE" => "N",
            "SHOW_LIST_PAGE" => "N",
            "SHOW_STATUS" => "Y",
            "SHOW_VIEW_PAGE" => "N",
            "START_PAGE" => "new",
            "SUCCESS_URL" => "",
            "USE_EXTENDED_ERRORS" => "N",
            "VARIABLE_ALIASES" => array(
                "action" => "action"
            ),
            "WEB_FORM_ID" => Option::get('sotbit.b2bcabinet', 'B2BCABINET_FEED_BACK_FORM_ID'),
        )
    ); ?>
</div>