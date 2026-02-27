<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/xml.php');

//$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/bitrix/probki/styles.css');

if($arGadgetParams["CITY"]!='')
	$url = 'yasoft=barff&region='.substr($arGadgetParams["CITY"], 1).'&ts='.time();
else
	$url = 'ts='.time();

$cache = new CPageCache();
if($arGadgetParams["CACHE_TIME"]>0 && !$cache->StartDataCache($arGadgetParams["CACHE_TIME"], 'c'.$arGadgetParams["CITY"], "gdprobki"))
	return;

$http = new \Bitrix\Main\Web\HttpClient();
$http->setTimeout(10);
$res = $http->get("https://export.yandex.ru/bar/reginfo.xml?".$url);

$res = str_replace("\xE2\x88\x92", "-", $res);
$res = $APPLICATION->ConvertCharset($res, 'UTF-8', SITE_CHARSET);

$xml = new CDataXML();
$xml->LoadString($res);

$node = $xml->SelectNodes('/info/traffic/title');
?>
<div class="widget_content widget_links">
    <h6 class="fw-bold"><?=$node->content?></h6>
    <div class="congestion_content">
        <div class="congestion_content-text">
            <?$node = $xml->SelectNodes('/info/traffic/region/hint');?>
            <span class="display_block"><?=$node->content?></span>
            <?$node = $xml->SelectNodes('/info/traffic/region/length');?>
            <span class="display_block">Протяженность: <?=$node->content?> м</span>
            <?$node = $xml->SelectNodes('/info/traffic/region/time');?>

            <span class="display_block">Последнее обновление: <?=$node->content?></span>
        </div>
        <div class="congestion_content-rate">
            <?
            $node = $xml->SelectNodes('/info/traffic/region/level');
            $t = Intval($node->content);
            ?>
            <?=$t?>
        </div>
    </div>
</div>

<?if($arGadgetParams["SHOW_URL"]=="Y"):?>
<?$node = $xml->SelectNodes('/info/traffic/region/url');?>
<a target="_blank" href="<?=htmlspecialcharsbx($node->content)?>" class="d-block mt-2 main-link b2b-main-link">Подробнее</a>
<?endif?>
<?$cache->EndDataCache();?>


