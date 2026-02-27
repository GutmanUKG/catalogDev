<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/notifications/pnotify.min.js");
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Pnotify.init("<?=$arResult["ALERT_MESSAGE"]?>");
    });
</script>