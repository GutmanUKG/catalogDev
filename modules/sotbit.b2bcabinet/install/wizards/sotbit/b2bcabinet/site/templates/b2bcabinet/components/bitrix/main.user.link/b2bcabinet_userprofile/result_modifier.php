<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (
	isset($arResult["User"])
	&& is_array($arResult["User"])
	&& isset($arResult["User"]["PersonalPhotoImgThumbnail"])
	&& empty($arResult["User"]["PersonalPhotoImgThumbnail"]["Image"])
)
{
    $path = $arResult["User"]["PERSONAL_GENDER"] == 'F' ? SITE_TEMPLATE_PATH . '/assets/images/acc_women.svg' : SITE_TEMPLATE_PATH . '/assets/images/acc_men.svg';
	$arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] = '<img src="'. $path .'" width="'.$arParams["THUMBNAIL_LIST_SIZE"].'" height="'.$arParams["THUMBNAIL_LIST_SIZE"].'" border="0">';
}

$arResult["User"]["DETAIL_URL"] = '';
?>