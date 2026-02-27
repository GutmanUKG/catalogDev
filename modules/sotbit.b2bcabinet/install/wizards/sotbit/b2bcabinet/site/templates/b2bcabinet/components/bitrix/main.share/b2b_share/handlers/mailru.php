<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/mailru.php");
$name = "mailru";
$title = GetMessage("BOOKMARK_HANDLER_MAILRU");
$icon_url_template = "<script>\n".
	"if (__function_exists('mailru_click') == false) \n".
	"{\n".
		"function mailru_click(url) \n".
		"{ \n".
			"window.open('https://connect.mail.ru/share?share_url='+encodeURIComponent(url),'sharer','toolbar=0,status=0,resizable=1,scrollbars=1,width=626,height=436'); \n".
			"return false; \n".
		"} \n".
	"}\n".
	"</script>\n";
if($arParams["~IMAGE_MAILRU_SRC"]) {
    $icon_url_template .= "<a href=\"https://connect.mail.ru/share?share_url=#PAGE_URL#\"".
        " onclick=\"return mailru_click('#PAGE_URL#');\" target=\"_blank\" title=\"" . $title . "\" >".
    "<img src=\"" . $arParams["~IMAGE_MAILRU_SRC"] . "\"/></a>\n";
}else{
    $icon_url_template.="<a href=\"https://connect.mail.ru/share?share_url=#PAGE_URL#\"".
        " onclick=\"return mailru_click('#PAGE_URL#');\" target=\"_blank\"  title=\"".$title."\">".
        "<img src=\"/include/share/Mail.png\"/></a>\n";
}
$sort = 600;
?>