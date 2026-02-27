<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (isset($arResult["User"]) && is_array($arResult["User"])) {
    $path = $arResult["User"]["PERSONAL_GENDER"] == 'F' ? SITE_TEMPLATE_PATH . '/assets/images/acc_women.svg' : SITE_TEMPLATE_PATH . '/assets/images/acc_men.svg';

    if (empty($arResult["User"]["PersonalPhotoImgThumbnail"]["Image"])) {
        $arResult["User"]["PersonalPhotoImgThumbnail"]["src"] = $path;
        $arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] = '<img src="'. $path .'" width="'.$arParams["THUMBNAIL_LIST_SIZE"].'" height="'.$arParams["THUMBNAIL_LIST_SIZE"].'" border="0">';
    }

    if ($arResult["User"]["PERSONAL_PHOTO"]) {
        $detailImg = CFile::ResizeImageGet(
            $arResult["User"]["PERSONAL_PHOTO"],
            array('width' => $arParams["THUMBNAIL_DETAIL_SIZE"], 'height' => $arParams["THUMBNAIL_DETAIL_SIZE"]),
            BX_RESIZE_IMAGE_EXACT ,
            true
        );
    }

    $path = $detailImg["src"] ?: $path;
    $arResult["User"]["PersonalPhotoImgThumbnail"]["Deatil_Image"] = '<img src="'. $path .'" class="rounded-pill" width="'.$arParams["THUMBNAIL_DETAIL_SIZE"].'" height="'.$arParams["THUMBNAIL_DETAIL_SIZE"].'">';
}
?>