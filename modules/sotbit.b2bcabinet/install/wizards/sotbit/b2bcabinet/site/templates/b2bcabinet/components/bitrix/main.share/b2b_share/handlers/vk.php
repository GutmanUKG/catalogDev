<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

__IncludeLang(dirname(__FILE__) . "/lang/" . LANGUAGE_ID . "/vk.php");
$name = "vk";
$title = GetMessage("BOOKMARK_HANDLER_VK");
$icon_url_template = "<script>\n" .
    "if (__function_exists('vk_click') == false) \n" .
    "{\n" .
    "function vk_click(url) \n" .
    "{ \n" .
    "window.open('https://vk.com/share.php?url='+encodeURIComponent(url),'sharer','toolbar=0,status=0,width=626,height=436'); \n" .
    "return false; \n" .
    "} \n" .
    "}\n" .
    "</script>\n";
if ($arParams["~IMAGE_VK_SRC"]) {
    $icon_url_template .= "<a href=\"https://vk.com/share.php?url=#PAGE_URL#\"" .
        " onclick=\"return vk_click('#PAGE_URL#');\" target=\"_blank\" title=\"" . $title . "\" >" .
        "<img src=\"" . $arParams["~IMAGE_VK_SRC"] . "\"/></a>\n";
} else {
    $icon_url_template .= "<a href=\"https://vk.com/share.php?url=#PAGE_URL#\"" .
        " onclick=\"return vk_click('#PAGE_URL#');\" target=\"_blank\"  title=\"" . $title . "\">" .
        "<img src=\"/include/share/vk.jpg\"/></a>\n";
}
$sort = 400;
?>