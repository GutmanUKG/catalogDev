<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

__IncludeLang(dirname(__FILE__) . "/lang/" . LANGUAGE_ID . "/twitter.php");
$name = "twitter";
$title = GetMessage("BOOKMARK_HANDLER_TWITTER");

if (
    false &&
    is_array($arParams)
    && array_key_exists("SHORTEN_URL_LOGIN", $arParams)
    && trim($arParams["SHORTEN_URL_LOGIN"]) <> ''
    && array_key_exists("SHORTEN_URL_KEY", $arParams)
    && trim($arParams["SHORTEN_URL_KEY"]) <> ''
) {
    $icon_url_template = "<script>\n" .
        "if (__function_exists('twitter_click_" . $arResult["COUNTER"] . "') == false) \n" .
        "{\n" .
        "function twitter_click_" . $arResult["COUNTER"] . "(longUrl) \n" .
        "{ \n" .
        "BX.loadScript('https://bit.ly/javascript-api.js?version=latest&login=" . $arParams["SHORTEN_URL_LOGIN"] . "&apiKey=" . $arParams["SHORTEN_URL_KEY"] . "',\n" .
        "function () \n" .
        "{\n" .
        "BitlyClient.shorten(longUrl, '__get_shorten_url_twitter_" . $arResult["COUNTER"] . "');\n" .
        "}\n" .
        ");\n" .
        "return false; \n" .
        "} \n" .
        "}\n" .
        "function __get_shorten_url_twitter_" . $arResult["COUNTER"] . "(data) \n" .
        "{\n" .
        "var first_result;\n" .
        "var shortUrl;\n" .
        "for (var r in data.results) \n" .
        "{\n" .
        "first_result = data.results[r]; \n" .
        "break;\n" .
        "}\n" .
        "if (first_result != null)\n" .
        "{\n" .
        "shortUrl = first_result.shortUrl.toString();\n" .
        "}\n" .
        "window.open('https://twitter.com/home/?status='+encodeURIComponent(shortUrl)+encodeURIComponent(' #PAGE_TITLE#'),'sharer','toolbar=0,status=0,width=726,height=436'); \n" .
        "}\n" .
        "</script>\n";
    if ($arParams["~IMAGE_TWITTER_SRC"]) {
        $icon_url_template .= "<a href=\"https://twitter.com/home/?status=#PAGE_URL#+#PAGE_TITLE_ORIG#\"" .
            " onclick=\"return twitter_click_" . $arResult["COUNTER"] . "('#PAGE_URL#');\"target=\"_blank\"title=\"" . $title . "\">" .
            "<img src=\"" . $arParams["~IMAGE_TWITTER_SRC"] . "\"/></a>\n";
    } else {
        $icon_url_template .= "<a href=\"https://twitter.com/home/?status=#PAGE_URL#+#PAGE_TITLE_ORIG#\"" .
            " onclick=\"return twitter_click_" . $arResult["COUNTER"] . "('#PAGE_URL#');\"target=\"_blank\" title=\"" . $title . "\">" .
            "<img src=\"".$arResult['FOLDER_PATH']."/images/twitter.svg\"/></a>\n";
    }
} else {
    $icon_url_template = "<script>\n" .
        "if (__function_exists('twitter_click_" . $arResult["COUNTER"] . "') == false) \n" .
        "{\n" .
        "function twitter_click_" . $arResult["COUNTER"] . "(longUrl) \n" .
        "{ \n" .
        "window.open('https://twitter.com/home/?status='+encodeURIComponent(longUrl)+encodeURIComponent(' #PAGE_TITLE#'),'sharer','toolbar=0,status=0,width=726,height=436'); \n" .
        "return false; \n" .
        "} \n" .
        "}\n" .
        "</script>\n";
    if ($arParams["~IMAGE_TWITTER_SRC"]) {
        $icon_url_template .= "<a href=\"https://twitter.com/home/?status=#PAGE_URL#+#PAGE_TITLE_ORIG#\"" .
            " onclick=\"return twitter_click_" . $arResult["COUNTER"] . "('#PAGE_URL#');\"target=\"_blank\" title=\"" . $title . "\">" .
            "<img src=\"" . $arParams["~IMAGE_TWITTER_SRC"] . "\"/></a>\n";
    } else {
        $icon_url_template .= "<a href=\"https://twitter.com/home/?status=#PAGE_URL#+#PAGE_TITLE_ORIG#\"" .
            " onclick=\"return twitter_click_" . $arResult["COUNTER"] . "('#PAGE_URL#');\"target=\"_blank\" title=\"" . $title . "\">" .
            "<img src=\"".$arResult['FOLDER_PATH']."/images/twitter.svg\"/></a>\n";
    }

    $sort = 200;
}
$charsBack = true;
?>