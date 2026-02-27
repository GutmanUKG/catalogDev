<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="col-md-12 position-sticky">
    <div class="card card-position-sticky">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <button
                    type="submit"
                    name="save"
                    value="<?=$arParams['NAME']?>"
                    class="btn btn-primary order-md-0 order-1 <?=(!empty($arParams['CLASS']) ? $arParams['CLASS'] : "")?>"
            ><?=$arParams['NAME']?>
            </button>
            
            <?$APPLICATION->IncludeComponent(
                "bitrix:main.userconsent.request",
                "b2bcabinet",
                array(
                    "ID" => \COption::GetOptionString("sotbit.b2bcabinet", "AGREEMENT_ID"),
                    "IS_CHECKED" => "Y",
                    "AUTO_SAVE" => "Y",
                    "IS_LOADED" => "Y",
                    "ORIGINATOR_ID" => $arResult["AGREEMENT_ORIGINATOR_ID"],
                    "ORIGIN_ID" => $arResult["AGREEMENT_ORIGIN_ID"],
                    "INPUT_NAME" => $arResult["AGREEMENT_INPUT_NAME"],
                    "REPLACE" => array(
                        "button_caption" => GetMessage("AUTH_REGISTER"),
                        "fields" => array(
                            rtrim(GetMessage("AUTH_NAME"), ":"),
                            rtrim(GetMessage("AUTH_LAST_NAME"), ":"),
                            rtrim(GetMessage("AUTH_LOGIN_MIN"), ":"),
                            rtrim(GetMessage("AUTH_PASSWORD_REQ"), ":"),
                            rtrim(GetMessage("AUTH_EMAIL"), ":"),
                        )
                    ),
                )
            );?>
        </div>
    </div>
</div>