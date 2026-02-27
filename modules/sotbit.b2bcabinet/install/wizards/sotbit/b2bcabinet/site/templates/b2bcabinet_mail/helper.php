<?php

use Bitrix\Main\Config\Option;

if (!function_exists('getLogoPath')) {
    function getLogoPath($path)
    {
        return  pathinfo($path, PATHINFO_EXTENSION) === 'svg' ? base64ImgEncoded($path) : $path;
    }
}

if (!function_exists('base64ImgEncoded')) {
    function base64ImgEncoded($path)
    {
        $imageSize = getimagesize($path);
        $imageData = base64_encode(file_get_contents($path));

        return "data:" . ($imageSize['mime'] ?: 'image/svg+xml') . ";base64,{$imageData}";
    }
}

if (!function_exists('getSiteLogoPath')) {
    function getSiteLogoPath($siteId, $serverName)
    {
        $fileId = Option::get('sotbit.b2bcabinet', 'LOGO', false, $siteId);
        if (!$fileId) {
            return null;
        }

        $fileArray = CFile::ResizeImageGet(
            $fileId,
            array("width" => 90, "height" => 60),
            BX_RESIZE_IMAGE_EXACT,
            true
        );

        return $fileArray ? $serverName . $fileArray['src'] : null;
    }
}