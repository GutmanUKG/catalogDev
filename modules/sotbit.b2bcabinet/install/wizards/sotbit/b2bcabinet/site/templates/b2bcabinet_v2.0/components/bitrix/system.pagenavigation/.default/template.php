<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

if(!$arResult["NavShowAlways"])
{
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
        return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<ul class="pagination pagination-flat">

    <?
        if (1 < $arResult["NavPageNomer"])
        {
            ?>
            <li class="page-item page-item-arrow">
                <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" class="page-link rounded ph-caret-left fw-semibold" title="<? echo GetMessage('nav_prev_title'); ?>">
                </a>
            </li>
            <?
        }
        else
        {
            ?>
            <li class="page-item page-item-arrow disabled">
                <span class="page-link rounded ph-caret-left fw-semibold">
                </a>
            </li>
            <?
        }

		$NavRecordGroup = 1;
		while($NavRecordGroup <= $arResult["NavPageCount"])
		{
			$strTitle = GetMessage(
				'nav_page_num_title',
				array('#NUM#' => $NavRecordGroup)
			);
			if ($NavRecordGroup == $arResult["NavPageNomer"])
			{
				?><li class="page-item active" title="<? echo GetMessage('nav_page_current_title'); ?>">
                    <span class="page-link rounded">
                        <? echo $NavRecordGroup; ?>
                    </span>
                </li><?
			}
			elseif ($NavRecordGroup == 1 && $arResult["bSavePage"] == false)
			{
				?><li class="page-item">
                    <a href="<?=$arResult['sUrlPathParams']; ?>SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>" class="page-link rounded"><?=$NavRecordGroup?></a>
                </li><?
			}
			else
			{
				?>
                    <li class="page-item">
                        <a class="page-link rounded"
                            href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$NavRecordGroup?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" 
                            title="<? echo $strTitle; ?>"
                            >
                                <?=$NavRecordGroup?>
                            </a>
                    </li>
                <?
			}
			if ($NavRecordGroup == 1 && $arResult["nStartPage"] >= 1 && $arResult["nStartPage"] - $NavRecordGroup >= 1)
			{
				$middlePage = ceil(($arResult["nStartPage"] + $NavRecordGroup)/2);
				$strTitle = GetMessage(
					'nav_page_num_title',
					array('#NUM#' => $middlePage)
				);
				?><li class="page-item">
                    <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$middlePage?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>" class="page-link rounded">...</a>
                  </li><?
				$NavRecordGroup = $arResult["nStartPage"]+1;
			}
			elseif ($NavRecordGroup == $arResult["nEndPage"]-1 && $arResult["nEndPage"]-1 < ($arResult["NavPageCount"] - 2))
			{
				$middlePage = floor(($arResult["NavPageCount"] + $arResult["nEndPage"] - 1)/2);
				$strTitle = GetMessage(
					'nav_page_num_title',
					array('#NUM#' => $middlePage)
				);
				?><li class="page-item"><a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$middlePage?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>" class="page-link rounded">...</a></li><?
				$NavRecordGroup = $arResult["NavPageCount"];
			}
			else
			{
				$NavRecordGroup++;
			}
		}

		if ($arResult["NavPageNomer"] < $arResult["NavPageCount"])
		{
			?>
            <li class="page-item page-item-arrow">
                <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo GetMessage('nav_next_title'); ?>" class="page-link rounded ph-caret-right fw-semibold">
                </a>
            </li>
            <?
		}
		else
		{
			?>
            <li class="page-item page-item-arrow disabled">
                <span class="page-link rounded ph-caret-right fw-semibold">
                </span>
            </li>
            <?
		}

		if ($arResult["bShowAll"])
		{
            if ($arResult["NavShowAll"]):
			?><li class="page-item"><a class="page-link rounded" href="<?=$arResult['sUrlPathParams']; ?>SHOWALL_<?=$arResult["NavNum"]?>=0&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageSize"]?>"><? echo GetMessage('nav_show_pages'); ?></a></li><?
            else:
            ?><li class="page-item"><a class="page-link rounded" href="<?=$arResult['sUrlPathParams']; ?>SHOWALL_<?=$arResult["NavNum"]?>=1&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageSize"]?>"><? echo GetMessage('nav_all'); ?></a></li>
            <?
            endif;
		}
	
?>
</ul>