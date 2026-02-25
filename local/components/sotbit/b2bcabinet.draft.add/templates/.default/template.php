<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>

<button id="add-draft" type="button" class="btn btn_b2b index_checkout-formalize_button checkout_btn">
    <?=GetMessage("B2B_DRAFT_ADD_BTN_NAME")?>
</button>

<div class="popup-draft-add-wrap">
    <div class="popup-draft-add">
        <button class="popup-draft-add__close-btn">
            <svg class="popup-draft-add__close-icon" width="25" height="26">
                <g >
                    <line  x1="6.01022" y1="19.0104" x2="18.031" y2="6.98958" />
                    <line  x1="18.0312" y1="19.0104" x2="6.0104" y2="6.9896" />
                </g>
            </svg>
        </button>
        <p class="draft-add__title-form"><?=GetMessage("B2B_DRAFT_ADD_FORM_TITLE")?></p>
        <div class="draft-add__form-add">
            <p class="draft-add__title-input-name">
                <?=GetMessage("B2B_DRAFT_ADD_FORM_TITLE_2")?>
            </p>
            <form id="add_draft"
                  data-params="<?=$this->__component->getSignedParameters();?>"
            >
                <input type="text" name="DRAFT_NAME" class="add-draft__input-name form-control" placeholder="<?=GetMessage("B2B_DRAFT_ADD_FORM_INPUT_NAME")?>" required pattern="^\s*[\S]+[\s\S]*" maxlength="40">
                <button type="submit" class="btn btn_b2b add-draft__submit-btn">
                   <?=GetMessage("B2B_DRAFT_ADD_FORM_BTN_SAVE")?>
                </button>
            </form>
        </div>
        <div class="draft-add__success-block">
            <div class="draft-add__success-main-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 42 42" width="42" height="42">
                    <path stroke="#00b02a" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M 41 19.1714 V 21.0114 C 40.9975 25.3243 39.601 29.5208 37.0187 32.9751 C 34.4363 36.4294 30.8066 38.9564 26.6707 40.1792 C 22.5349 41.4021 18.1145 41.2552 14.0689 39.7606 C 10.0234 38.266 6.56931 35.5036 4.22192 31.8856 C 1.87453 28.2675 0.759582 23.9876 1.04335 19.6841 C 1.32712 15.3806 2.99441 11.2841 5.79656 8.00559 C 8.5987 4.72708 12.3856 2.44221 16.5924 1.49174 C 20.7992 0.541267 25.2005 0.976121 29.14 2.73145 M 41 5.01145 L 21 25.0314 L 15 19.0314" />
                </svg>
                <div class="title__success-name">
                    <p class="title__success-bold">
                    </p>
                </div>
            </div>
            <a href="<?=$arResult["LINK_TO_DRAFTS_LIST"]?>" class="btn btn_b2b add-draft__success-btn">
                <?=GetMessage("B2B_DRAFT_ADD_FORM_BTN_DRAFT_LIST")?>
            </a>
        </div>
    </div>
</div>