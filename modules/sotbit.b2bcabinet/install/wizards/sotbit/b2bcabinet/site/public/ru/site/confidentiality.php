<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Конфиденциальность");
$APPLICATION->SetTitle("Конфиденциальность");
?>
<div class="card p-3">
    <?=Bitrix\Main\Localization\Loc::getMessage('sotbit.b2bcabinet_CONFIDENTIALITY')?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>