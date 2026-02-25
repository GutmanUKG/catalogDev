<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $APPLICATION;
?>

<div class="bx-ag-search-page search-page <?=$arResult["VISUAL_PARAMS"]["THEME_CLASS"]?>">
	<form class="search-page-form" action="" method="get">
		<input placeholder="<?=$arParams["INPUT_PLACEHOLDER"]?>" type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" size="50" />

		<?if($arParams["SHOW_WHERE"]):?>
			<select name="where">
				<option value=""><?=GetMessage("SEARCH_ALL")?></option>
				<?foreach($arResult["DROPDOWN"] as $key=>$value):?>
					<option value="<?=$key?>"<?if($arResult["REQUEST"]["WHERE"]==$key) echo " selected"?>><?=$value?></option>
				<?endforeach?>
			</select>
		<?endif;?>
		
		<input type="submit" value="<?=GetMessage("SEARCH_GO")?>" />
		<input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
		
		<?if($arParams["SHOW_WHEN"]):?>
			<script>
			var switch_search_params = function()
			{
				var sp = document.getElementById('search_params');
				var flag;
				var i;

				if(sp.style.display == 'none')
				{
					flag = false;
					sp.style.display = 'block'
				}
				else
				{
					flag = true;
					sp.style.display = 'none';
				}

				var from = document.getElementsByName('from');
				for(i = 0; i < from.length; i++)
					if(from[i].type.toLowerCase() == 'text')
						from[i].disabled = flag;

				var to = document.getElementsByName('to');
				for(i = 0; i < to.length; i++)
					if(to[i].type.toLowerCase() == 'text')
						to[i].disabled = flag;

				return false;
			}
			</script>
			<br /><a class="search-page-params" href="#" onclick="return switch_search_params()"><?echo GetMessage('CT_BSP_ADDITIONAL_PARAMS')?></a>
			<div id="search_params" class="search-page-params" style="display:<?echo $arResult["REQUEST"]["FROM"] || $arResult["REQUEST"]["TO"]? 'block': 'none'?>">
				<?$APPLICATION->IncludeComponent(
					'bitrix:main.calendar',
					'',
					array(
						'SHOW_INPUT' => 'Y',
						'INPUT_NAME' => 'from',
						'INPUT_VALUE' => $arResult["REQUEST"]["~FROM"],
						'INPUT_NAME_FINISH' => 'to',
						'INPUT_VALUE_FINISH' =>$arResult["REQUEST"]["~TO"],
						'INPUT_ADDITIONAL_ATTR' => 'size="10"',
					),
					null,
					array('HIDE_ICONS' => 'Y')
				);?>
			</div>
		<?endif?>
	</form>

	<?if(isset($arResult["REQUEST"]["ORIGINAL_QUERY"])):
		?>
		<div class="search-language-guess">
			<?echo GetMessage("CT_BSP_KEYBOARD_WARNING", array("#query#"=>'<a href="'.$arResult["ORIGINAL_QUERY_URL"].'">'.$arResult["REQUEST"]["ORIGINAL_QUERY"].'</a>'))?>
		</div><br /><?
	endif;?>
	
	<?if(is_array($arResult["SEARCH_HISTORY"]) && count($arResult["SEARCH_HISTORY"]) > 0):?>
		<div class="ag-spage-history">
			<?=GetMessage("SEARCH_HISTORY_TITLE")?>
			<?foreach($arResult["SEARCH_HISTORY"] as $k=>$v):
				if($k > 0) echo ', ';?><a href="?q=<?=$v?>"><?=$v?></a><?
			endforeach?>
		</div>
	<?endif;?>
	
	<?/* if($arResult["VISUAL_PARAMS"]["CLARIFY"] && count($arResult["CLARIFY_WORDS"]) > 1):?>
		<div class="ag-spage-clarify-list">
			<div class="ag-spage-clarify-title"><?=GetMessage("AG_SPAGE_CLARIFY_TITLE");?></div>
			<?foreach($arResult["CLARIFY_WORDS"] as $word):
			$word = strtolower($word);
			?>
				<a class="ag-spage-clarify-item" href="<?=$APPLICATION->GetCurPageParam('q='.$word, array("q"))?>"><?=$word?></a>
			<?endforeach;?>
		</div>
	<?endif; */?>
	
	<?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
		<?// clear //?>
	<?elseif($arResult["ERROR_CODE"]!=0):?>
		<p><?=GetMessage("SEARCH_ERROR")?></p>
		<?ShowError($arResult["ERROR_TEXT"]);?>
		<p><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></p>
		<br /><br />
	<?elseif(count($arResult["SEARCH"])>0):?>
		<div class="search-view-default">
			<?if($arParams["DISPLAY_TOP_PAGER"] != "N"):?>
				<?echo $arResult["NAV_STRING"]?><br />
			<?endif;?>
			
			<?foreach($arResult["SEARCH"] as $k=>$arItem):
				$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];
				// echo '<pre>'; print_r($arElement); echo '</pre>';
			?>
				<div class="<?if($k==0) echo 'search-item-first'?> search-item">
					<?if(is_array($arItem["PICTURE"])):?>
						<div class="search-preview"><img src="<?=$arItem["PICTURE"]["src"]?>"></div>
					<?endif;?>
					
					<div class="search-description">
						<div class="search-title"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["TITLE_FORMATED"]?></a></div>
						
						<div class="search-previewtext"><?echo $arItem["BODY_FORMATED"]?></div>
						
						<?if(!empty($arElement["PROPS"])):?>
							<div class="search-propslist">
								<?foreach($arElement["PROPS"] as $prop):
								if(empty($prop["VALUE"])) continue;
								?>
									<div class="search-propsitem">
										<span class="search-propsitem-name"><?=$prop["NAME"]?>:</span> <span class="search-propsitem-value"><?=implode(', ', $prop["VALUE"])?></span>
									</div>
								<?endforeach;?>
							</div>
						<?endif;?>
						
						<?if (
							$arParams["SHOW_RATING"] == "Y"
							&& strlen($arItem["RATING_TYPE_ID"]) > 0
							&& $arItem["RATING_ENTITY_ID"] > 0
						):?>
							<div class="search-item-rate"><?
								$APPLICATION->IncludeComponent(
									"bitrix:rating.vote", $arParams["RATING_TYPE"],
									Array(
										"ENTITY_TYPE_ID" => $arItem["RATING_TYPE_ID"],
										"ENTITY_ID" => $arItem["RATING_ENTITY_ID"],
										"OWNER_ID" => $arItem["USER_ID"],
										"USER_VOTE" => $arItem["RATING_USER_VOTE_VALUE"],
										"USER_HAS_VOTED" => $arItem["RATING_USER_VOTE_VALUE"] == 0? 'N': 'Y',
										"TOTAL_VOTES" => $arItem["RATING_TOTAL_VOTES"],
										"TOTAL_POSITIVE_VOTES" => $arItem["RATING_TOTAL_POSITIVE_VOTES"],
										"TOTAL_NEGATIVE_VOTES" => $arItem["RATING_TOTAL_NEGATIVE_VOTES"],
										"TOTAL_VALUE" => $arItem["RATING_TOTAL_VALUE"],
										"PATH_TO_USER_PROFILE" => $arParams["~PATH_TO_USER_PROFILE"],
									),
									$component,
									array("HIDE_ICONS" => "Y")
								);?>
							</div>
						<?endif;?>
						<small><?=GetMessage("SEARCH_MODIFIED")?> <?=$arItem["DATE_CHANGE"]?></small>
						<div class="clear"></div>
					</div>
				</div>
			<?endforeach;?>
			
			<?if($arParams["DISPLAY_BOTTOM_PAGER"] != "N"):?>
				<?echo $arResult["NAV_STRING"]?><br />
			<?endif;?>
			
			<div class="search-change-how">
				<?if($arResult["REQUEST"]["HOW"]=="d"):?>
					<a href="<?=$arResult["URL"]?>&amp;how=r<?echo $arResult["REQUEST"]["FROM"]? '&amp;from='.$arResult["REQUEST"]["FROM"]: ''?><?echo $arResult["REQUEST"]["TO"]? '&amp;to='.$arResult["REQUEST"]["TO"]: ''?>"><?=GetMessage("SEARCH_SORT_BY_RANK")?></a>&nbsp;|&nbsp;<b><?=GetMessage("SEARCH_SORTED_BY_DATE")?></b>
				<?else:?>
					<b><?=GetMessage("SEARCH_SORTED_BY_RANK")?></b>&nbsp;|&nbsp;<a href="<?=$arResult["URL"]?>&amp;how=d<?echo $arResult["REQUEST"]["FROM"]? '&amp;from='.$arResult["REQUEST"]["FROM"]: ''?><?echo $arResult["REQUEST"]["TO"]? '&amp;to='.$arResult["REQUEST"]["TO"]: ''?>"><?=GetMessage("SEARCH_SORT_BY_DATE")?></a>
				<?endif;?>
			</div>
		</div>
	<?else:?>
		<?ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));?>
	<?endif;?>
</div>

<?if($arResult["VISUAL_PARAMS"]["THEME_COLOR"]):?>
	<style>
		.search-page .search-item, .search-page input[type=text], .search-page input[type=submit], .ag-spage-clarify-item, .ag-spage-clarify-item:hover {
			border-color: <?=$arResult["VISUAL_PARAMS"]["THEME_COLOR"]?> !important;
		}
		.search-page input[type=submit] {
			background-color: <?=$arResult["VISUAL_PARAMS"]["THEME_COLOR"]?> !important;
		}
	</style>
<?endif;?>