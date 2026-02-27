<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/notifications/noty.min.js");
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        NotyAlerts.init("<?=$arResult["ALERT_MESSAGE"]?>");
    });
</script>