<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
$methodIstall = Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'method_install', '', SITE_ID) == 'AS_TEMPLATE' ?
    SITE_DIR . 'b2bcabinet/' : SITE_DIR;
?>

<div class="blank_personal">
    <div class="row">
        <div class="col-md-12">
            <? if ($arResult['DATA_SAVED'] == 'Y'): ?>
                <div class="alert alert-success alert-styled-left alert-dismissible">
                    <?= Loc::getMessage('PROFILE_DATA_SAVED') ?>
                    <script>
                        scrollToMessage();
                    </script>
                </div>
            <? endif; ?>
            <script type="text/javascript">
                function scrollToMessage() {
                    const messageBlock = document.querySelector('.blank_personal .alert-dismissible');
                    if (messageBlock) {
                        messageBlock.scrollIntoView(false);
                    }
                }
                var opened_sections = [<?
                    $arResult["opened"] = $_COOKIE[$arResult["COOKIE_PREFIX"] . "_user_profile_open"];
                    $arResult["opened"] = preg_replace("/[^a-z0-9_,]/i", "", $arResult["opened"]);
                    if (strlen($arResult["opened"]) > 0) {
                        echo "'" . implode("', '", explode(",", $arResult["opened"])) . "'";
                    } else {
                        $arResult["opened"] = "reg";
                        echo "'reg'";
                    }
                    ?>];

                var cookie_prefix = '<?=$arResult["COOKIE_PREFIX"]?>';
            </script>

            <? if ($arResult["strProfileError"]): ?>
                <div class="alert alert-danger alert-styled-left alert-dismissible">
                    <?= $arResult["strProfileError"] ?>
                </div>
                <script>
                    scrollToMessage();
                </script>
            <? endif; ?>

            <!--Main data-->
            <? if (is_array($arResult['MAIN_DATA']) && !empty($arResult['MAIN_DATA'])): ?>
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('MAIN_DATA') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form1" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['MAIN_DATA'] as $key => $mData) {
                                (empty($mData['LABEL']) ? $mData['LABEL'] = Loc::getMessage($mData['NAME']) : "");
                                (empty($mData['PLACEHOLDER']) ? $mData['PLACEHOLDER'] = Loc::getMessage("PH_" . $mData['NAME']) : "");
                                ($mData['NAME'] == 'NEW_PASSWORD' ? $mData['NOTES'] = Loc::getMessage('NOTE_NEW_PASSWORD') : "");
                                if ($key == 'SUBMIT_BUTTON') {
                                    $mData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $mData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $mData['NAME'] = Loc::getMessage($mData['NAME']);
                                }

                                $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $mData["arUserField"]["USER_TYPE"],
                                    $mData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                );
                            }
                            ?>

                        </form>
                    </div>
                </div>
            <? endif; ?>
            <!--/Main data-->

            <!-- Personal data 1-->
            <? if (is_array($arResult['PERSONAL_DATA']) && !empty($arResult['PERSONAL_DATA'])): ?>
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('PERSONAL_INFO') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form2" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['PERSONAL_DATA'] as $key => $pData) {
                                (empty($pData['LABEL']) ? $pData['LABEL'] = Loc::getMessage($pData['NAME']) : "");
                                if ($key == 'PERSONAL_GENDER') {
                                    foreach ($pData['ELEMENTS'] as &$element) {
                                        (empty($element['R_BUTTOM_CONTENT']) ? $element['R_BUTTOM_CONTENT'] = Loc::getMessage($element['NAME'] . "_" . $element['VALUE']) : "");
                                    }
                                }

                                (empty($pData['PLACEHOLDER']) ? $pData['PLACEHOLDER'] = Loc::getMessage("PH_" . $pData['NAME']) : "");
                                ($key == 'NEW_PASSWORD' ? $pData['NOTES'] = Loc::getMessage('NOTE_' . $key) : "");
                                ($pData['arUserField'] == 'file' ? $pData['NOTES'] = Loc::getMessage('FILE_NOTE') : "");

                                if ($key == 'SUBMIT_BUTTON') {
                                    $pData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $pData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $pData['NAME'] = Loc::getMessage($pData['NAME']);
                                } elseif ($key == 'PERSONAL_PHONE' || $key == 'PERSONAL_FAX') {
                                    $pData["ATTR"] = [
                                        0 => 'pattern=[\d+\-\s()]*'
                                    ];
                                } elseif ($key == 'PERSONAL_PHOTO') {
                                    $pData["ATTR"] = [
                                        0 => 'accept=image/*'
                                    ];
                                }
                                ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $pData['arUserField']["USER_TYPE"],
                                    $pData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                                <?
                            }
                            ?>
                        </form>
                    </div>
                </div>
            <? endif; ?>
            <!-- /Personal data -->

            <!-- Work data -->
            <? if (is_array($arResult['WORK_DATA']) && !empty($arResult['WORK_DATA'])): ?>
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('PERSONAL_WORK_INFO') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form3" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['WORK_DATA'] as $key => $wData) {
                                (empty($wData['LABEL']) ? $wData['LABEL'] = Loc::getMessage($wData['NAME']) : "");
                                (empty($wData['PLACEHOLDER']) ? $wData['PLACEHOLDER'] = Loc::getMessage("PH_" . $wData['NAME']) : "");
                                ($wData['arUserField'] == 'file' ? $wData['NOTES'] = Loc::getMessage('FILE_NOTE') : "");

                                if ($key == 'SUBMIT_BUTTON') {
                                    $wData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $wData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $wData['NAME'] = Loc::getMessage($wData['NAME']);
                                }
                                ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $wData['arUserField']["USER_TYPE"],
                                    $wData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                                <?
                            }
                            ?>
                        </form>
                    </div>
                </div>
            <? endif; ?>
            <!-- /Work data -->

            <? if ($arResult["INCLUDE_FORUM"] == "Y" && (is_array($arResult['FORUM_DATA']) && !empty($arResult['FORUM_DATA']))): ?>
                <!-- Forum data -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('FORUM_INFO') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form4" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['FORUM_DATA'] as $key => $fData) {
                                (empty($fData['LABEL']) ? $fData['LABEL'] = Loc::getMessage($fData['NAME']) : "");
                                (empty($fData['PLACEHOLDER']) ? $fData['PLACEHOLDER'] = Loc::getMessage("PH_" . $fData['NAME']) : "");
                                ($fData['arUserField'] == 'file' ? $fData['NOTES'] = Loc::getMessage('FILE_NOTE') : "");

                                if ($key == 'SUBMIT_BUTTON') {
                                    $fData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $fData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $fData['NAME'] = Loc::getMessage($fData['NAME']);
                                }
                                ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $fData['arUserField']["USER_TYPE"],
                                    $fData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                                <?
                            }
                            ?>
                        </form>
                    </div>
                </div>
                <!-- /Forum data -->
            <? endif; ?>

            <? if ($arResult['USER_PROPERTIES']['SHOW'] === 'Y'): ?>
                <!-- show users filds -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('OTHER_PROFILE_DATA') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form7" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['USER_PROPERTIES']['SHOW_DATA'] as $key => $mData) {
                                (empty($mData['LABEL']) ? $mData['LABEL'] = Loc::getMessage($mData['NAME']) : "");
                                (empty($mData['PLACEHOLDER']) ? $mData['PLACEHOLDER'] = Loc::getMessage("PH_" . $mData['NAME']) : "");
                                ($mData['NAME'] == 'NEW_PASSWORD' ? $mData['NOTES'] = Loc::getMessage('NOTE_NEW_PASSWORD') : "");
                                if ($key == 'SUBMIT_BUTTON') {
                                    $mData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $mData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $mData['NAME'] = Loc::getMessage($mData['NAME']);
                                }

                                $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $mData["arUserField"]["USER_TYPE"],
                                    $mData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                );
                            }
                            ?>
                        </form>
                    </div>
                </div>
                <!-- show users filds -->
            <? endif; ?>

            <? if ($arResult["INCLUDE_BLOG"] == "Y" && (is_array($arResult['BLOG_DATA']) && !empty($arResult['BLOG_DATA']))): ?>
                <!-- Blog data -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('BLOG_INFO') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form5" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['BLOG_DATA'] as $key => $bData) {
                                (empty($bData['LABEL']) ? $bData['LABEL'] = Loc::getMessage($bData['NAME']) : "");
                                (empty($bData['PLACEHOLDER']) ? $bData['PLACEHOLDER'] = Loc::getMessage("PH_" . $bData['NAME']) : "");
                                ($bData['arUserField'] == 'file' ? $bData['NOTES'] = Loc::getMessage('FILE_NOTE') : "");

                                if ($key == 'SUBMIT_BUTTON') {
                                    $bData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $bData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $bData['NAME'] = Loc::getMessage($bData['NAME']);
                                }
                                ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $bData['arUserField']["USER_TYPE"],
                                    $bData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                                <?
                            }
                            ?>
                        </form>
                    </div>
                </div>
                <!-- /Blog data -->
            <? endif; ?>

            <? if ($arResult["INCLUDE_LEARNING"] == "Y" && (is_array($arResult['LEARNING_DATA']) && !empty($arResult['LEARNING_DATA']))): ?>
                <!-- Learning data -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('LEARNING_INFO') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form6" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['LEARNING_DATA'] as $key => $lData) {
                                (empty($lData['LABEL']) ? $lData['LABEL'] = Loc::getMessage($lData['NAME']) : "");
                                (empty($lData['PLACEHOLDER']) ? $lData['PLACEHOLDER'] = Loc::getMessage("PH_" . $lData['NAME']) : "");
                                ($lData['arUserField'] == 'file' ? $lData['NOTES'] = Loc::getMessage('FILE_NOTE') : "");

                                if ($key == 'SUBMIT_BUTTON') {
                                    $lData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $lData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $lData['NAME'] = Loc::getMessage($lData['NAME']);
                                }
                                ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $lData['arUserField']["USER_TYPE"],
                                    $lData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                                <?
                            }
                            ?>
                        </form>
                    </div>
                </div>
                <!-- /Learning data -->
            <? endif; ?>

            <? if ($arResult["IS_ADMIN"] == "Y" && (is_array($arResult['ADMIN_DATA']) && !empty($arResult['ADMIN_DATA']))): ?>
                <!-- Admin data -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?= Loc::getMessage('USER_ADMIN_NOTES') ?></h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" name="form7" action="<?= $arResult["FORM_TARGET"] ?>"
                              enctype="multipart/form-data">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" name="lang" value="<?= LANG ?>"/>
                            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
                            <?
                            foreach ($arResult['ADMIN_DATA'] as $key => $aData) {
                                (empty($aData['LABEL']) ? $aData['LABEL'] = Loc::getMessage($aData['NAME']) : "");
                                (empty($aData['PLACEHOLDER']) ? $aData['PLACEHOLDER'] = Loc::getMessage("PH_" . $aData['NAME']) : "");
                                ($aData['arUserField'] == 'file' ? $aData['NOTES'] = Loc::getMessage('FILE_NOTE') : "");

                                if ($key == 'SUBMIT_BUTTON') {
                                    $aData['CONFIRM_LABEL'] = Loc::getMessage("LABEL_" . $aData['NAME'],
                                        array('#LINK#' => $methodIstall . 'confidentiality.php'));
                                    $aData['NAME'] = Loc::getMessage($aData['NAME']);
                                }
                                ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $aData['arUserField']["USER_TYPE"],
                                    $aData,
                                    null,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                                <?
                            }
                            ?>
                        </form>
                    </div>
                </div>
                <!-- /Admin data -->
            <? endif; ?>
        </div>
    </div>
</div>


<script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.form-check-input-switchery'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html);
    });

    $('.form-input-styled').uniform();

    $('.pickadate').pickadate();

    Select2Selects.init();
</script>