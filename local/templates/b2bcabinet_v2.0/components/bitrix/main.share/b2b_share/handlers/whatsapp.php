<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

__IncludeLang(dirname(__FILE__) . "/lang/" . LANGUAGE_ID . "/whatsapp.php");
$name = "whatsapp";
$title = GetMessage("BOOKMARK_HANDLER_WHATSAPP");
$icon_url_template = "<script>\n" .
    "if (__function_exists('whatsapp_click') == false) \n" .
    "{\n" .
    "function whatsapp_click(url) \n" .
    "{ \n" .
    "window.open('whatsapp://send?text='+encodeURIComponent(url),'sharer','toolbar=0,status=0,resizable=1,scrollbars=1,width=626,height=436'); \n" .
    "return false; \n" .
    "} \n" .
    "}\n" .
    "</script>\n";
if ($arParams["~IMAGE_WHATSAPP_SRC"]) {
    $icon_url_template .= "<a href=\"whatsapp://send?text=#PAGE_URL#\" data-action=\"share/whatsapp/share\"" .
        " onclick=\"return whatsapp_click('#PAGE_URL#');\" target=\"_blank\" title=\"" . $title . "\">" .
        "<img src=\"" . $arParams["~IMAGE_WHATSAPP_SRC"] . "\"/>" .
        "\"</a>\n\"";
} else {
    $icon_url_template .= "<a href=\"whatsapp://send?text=#PAGE_URL#\" data-action=\"share/whatsapp/share\"" .
        " onclick=\"return whatsapp_click('#PAGE_URL#');\" target=\"_blank\" title=\"" . $title . "\">" .
        "<img src=\"".$arResult['FOLDER_PATH']."/images/whatsapp.svg\"/></a>\n";
}
$sort = 600;
?>

