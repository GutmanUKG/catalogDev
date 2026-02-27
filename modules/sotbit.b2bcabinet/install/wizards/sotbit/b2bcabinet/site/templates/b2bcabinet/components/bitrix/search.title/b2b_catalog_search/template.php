<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc,
    Sotbit\B2bCabinet\Helper\Config;

$this->setFrameMode(true);

$INPUT_ID = trim($arParams["~INPUT_ID"]);
if($INPUT_ID == '')
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if($CONTAINER_ID == '')
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);



if($arParams["SHOW_INPUT"] !== "N"):?>
<div id="<?echo $CONTAINER_ID?>" class="bx-searchtitle <?=$themeClass;?>" xmlns="http://www.w3.org/1999/html">
    <form action="<?=$arParams["PAGE"]?>" id="b2b-catalog-search">
        <div class="form-group form-group-feedback form-group-feedback-right">
            <input id="<?echo $INPUT_ID?>" type="search" class="form-control" name="q" value="<?=htmlspecialcharsbx($_REQUEST["q"])?>" placeholder="<?=Loc::getMessage('CT_BST_SEARCH_BUTTON')?>" autocomplete="off"/>
            <div class="form-control-feedback">
                <button type="submit" class="b2b-catalog-search__submit btn btn-link" >
                    <i class="icon-search4"></i>
                </button>
            </div>
        </div>
    </form>
</div>


<?endif?>
<script>
	BX.ready(function(){
	    const MIN_QUERY_LEN = 1;

	    const searchForm = document.querySelector('#b2b-catalog-search'),
            searchFormSubmit = searchForm.querySelector('.b2b-catalog-search__submit'),
            inpudId = '<?echo $INPUT_ID?>',
            searchInput = searchForm.querySelector('#' + inpudId);

        var icon = searchFormSubmit.querySelector('i');

        searchInput.addEventListener('blur', function (e) {
            e.stopImmediatePropagation();
        });

        searchInput.addEventListener('input', function (e) {
            var searchResult = document.querySelector('.title-search-result');
            if (searchInput.value.length >= MIN_QUERY_LEN) {
                icon.className = 'icon-spinner2 spinner';
                if (searchResult) {
                    searchResult.style.display = 'block';
                }
            } else if (searchInput.value.length === 0) {
                if (searchResult) {
                    searchResult.style.display = 'none';
                }
            }
        });

		new JCTitleSearch({
			'AJAX_PAGE' : '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
			'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
			'INPUT_ID': '<?echo $INPUT_ID?>',
			'MIN_QUERY_LEN': MIN_QUERY_LEN
		});
	});
</script>

