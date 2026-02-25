<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
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
                </label>
            </div>
        </div>
    </div>
</div>
<div class="text-end">
    <button
            type="submit"
            name="save"
            value="<?=$arParams['NAME']?>"
            class="btn btn-primary <?=(!empty($arParams['CLASS']) ? $arParams['CLASS'] : "")?>"
    ><?=$arParams['NAME']?>
    </button>
</div>