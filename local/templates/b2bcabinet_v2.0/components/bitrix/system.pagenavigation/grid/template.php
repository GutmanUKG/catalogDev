<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	/**
	 * @var array $arResult
	 * @var array $arParam
	 * @var CBitrixComponentTemplate $this
	 */

	$this->setFrameMode(true);

	if(!$arResult["NavShowAlways"]){
		if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
			return;
	}
?>

<?
	$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
	$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<?if($arResult["bDescPageNumbering"] === true):?>
	<ul class="pagination pagination-flat">
		<?
			if ($arResult["NavPageNomer"] > 1):
				if($arResult["bSavePage"]):
		?>
					<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a></li>
		<?
				else:
					if ($arResult["NavPageNomer"] > 2):
		?>
						<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a></li>
		<?
					else:
		?>
						<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"></a></li>
		<?
					endif;

				endif;
			else:
		?>
				<li class="page-item page-item-arrow disabled"><span class="page-link rounded ph-caret-left fw-semibold"></span></li>
		<?endif;?>



		<?
			if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
				if ($arResult["nStartPage"] < $arResult["NavPageCount"]):
					if($arResult["bSavePage"]):
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>">1</a></li>
		<?
					else:
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
		<?
					endif;
					if ($arResult["nStartPage"] < ($arResult["NavPageCount"] - 1)):
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=intval($arResult["nStartPage"] + ($arResult["NavPageCount"] - $arResult["nStartPage"]) / 2)?>">...</a></li>
		<?
					endif;
				endif;
			endif;

			do {
				$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;

				if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
		?>
					<li class="page-item active"><span class="page-link rounded"><?=$NavRecordGroupPrint?></span></li>
		<?
				elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):
		?>
					<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a></li>
		<?
				else:
		?>
					<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a></li>
		<?
				endif;

				$arResult["nStartPage"]--;
			}
			
			while($arResult["nStartPage"] >= $arResult["nEndPage"]);

			if ($arResult["NavPageNomer"] > 1):
				if ($arResult["nEndPage"] > 1):
					if ($arResult["nEndPage"] > 2):
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] / 2)?>">...</a></li>
		<?
					endif;
		?>
					<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1"><?=$arResult["NavPageCount"]?></a></li>
		<?
				endif;
			endif;
		?>



		<?
			if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
		?>
				<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-right fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"></a></li>
		<?
			else:
		?>
				<li class="page-item page-item-arrow disabled"><span class="page-link rounded ph-caret-right fw-semibold"></span></li>
		<?
			endif;
		?>
	</ul>
<?else:?>
	<ul class="pagination pagination-flat">
		<?
			if ($arResult["NavPageNomer"] > 1):
				if($arResult["bSavePage"]):
		?>
					<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a></li>
		<?
				else:
					if ($arResult["NavPageNomer"] > 2):
		?>
						<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a></li>
		<?
					else:
		?>
						<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"></a></li>
		<?
					endif;

				endif;
			else:
		?>
				<li class="page-item page-item-arrow disabled"><span class="page-link rounded ph-caret-left fw-semibold"></span></li>
		<?endif;?>



		<?
			if ($arResult["NavPageNomer"] > 1):
				if ($arResult["nStartPage"] > 1):
					if($arResult["bSavePage"]):
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1">1</a></li>
		<?
					else:
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
		<?
					endif;
					if ($arResult["nStartPage"] > 2):
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nStartPage"] / 2)?>">...</a></li>
		<?
					endif;
				endif;
			endif;

			do {
				if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
		?>
					<li class="page-item active"><span class="page-link rounded"><?=$arResult["nStartPage"]?></span></li>
		<?
				elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
		?>
					<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li>
		<?
				else:
		?>
					<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
		<?
				endif;
				$arResult["nStartPage"]++;
			}

			while($arResult["nStartPage"] <= $arResult["nEndPage"]);

			if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
				if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
					if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] + ($arResult["NavPageCount"] - $arResult["nEndPage"]) / 2)?>">...</a></li>
		<?
					endif;
		?>
						<li class="page-item"><a class="page-link rounded" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a></li>
		<?
				endif;
			endif;
		?>


		
		<?
			if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
		?>
				<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-right fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"></a></li>
		<?
			else:
		?>
				<li class="page-item page-item-arrow disabled"><span class="page-link rounded ph-caret-right fw-semibold"></span></li>
		<?
			endif;
		?>
	</ul>

	<?/*
	<div class="main-ui-pagination-arrows">
		<?
			if ($arResult["NavPageNomer"] > 1):
				if($arResult["bSavePage"]):
		?>
					<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a></li>
		<?
				else:
					if ($arResult["NavPageNomer"] > 2):
		?>
						<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a></li>
		<?
					else:
		?>
						<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-left fw-semibold" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"></a></li>
		<?
					endif;

				endif;
			else:
		?>
				<span class="page-link rounded ph-caret-left fw-semibold"></span>
		<?
			endif;

			if ($arResult["bShowAll"]):
				if ($arResult["NavShowAll"]):
		?>
					<li class="page-item page-item-arrow"><a class="main-ui-pagination-arrow" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0"><?=GetMessage("MAIN_UI_PAGINATION__PAGED")?></a></li>
		<?
				else:
		?>
					<li class="page-item page-item-arrow"><a class="main-ui-pagination-arrow" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1"><?=GetMessage("MAIN_UI_PAGINATION__ALL")?></a></li>
		<?
				endif;
			endif;

			if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
		?>
				<li class="page-item page-item-arrow"><a class="page-link rounded ph-caret-right fw-semibold" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"></a></li>
		<?
			else:
		?>
				<span class="page-link rounded ph-caret-right fw-semibold"></span>
		<?
			endif;
		?>
	</div>
	*/?>
<?endif;?>