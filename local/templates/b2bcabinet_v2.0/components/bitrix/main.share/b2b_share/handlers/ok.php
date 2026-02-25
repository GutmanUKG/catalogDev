<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

__IncludeLang(dirname(__FILE__) . "/lang/" . LANGUAGE_ID . "/ok.php");
$name = "ok";
$title = GetMessage("BOOKMARK_HANDLER_OK");
$icon_url_template = "<script>\n" .
    "if (__function_exists('ok_click') == false) \n" .
    "{\n" .
    "function ok_click(url) \n" .
    "{ \n" .
    "window.open('https://connect.ok.ru/offer?url='+encodeURIComponent(url),'sharer','toolbar=0,status=0,resizable=1,scrollbars=1,width=626,height=436'); \n" .
    "return false; \n" .
    "} \n" .
    "}\n" .
    "</script>\n";
    if($arParams['IMAGE_OK_SRC']) {
        $icon_url_template .= "<a href=\"https://connect.ok.ru/offer?url=#PAGE_URL#\"".
            " onclick=\"return ok_click('#PAGE_URL#');\" target=\"_blank\" title=\"" . $title . "\">" .
            "<img src=\"".$arParams["~IMAGE_OK_SRC"]."\"/>".
            "\"</a>\n\"";
        }else {
            $icon_url_template .= "<a href=\"https://connect.ok.ru/offer?url=#PAGE_URL#\"".
                " onclick=\"return ok_click('#PAGE_URL#');\" target=\"_blank\" title=\"" . $title . "\">" .
                "<img src=\"/include/share/ok.png\"/></a>\n";
    }
$sort = 600;
?>
