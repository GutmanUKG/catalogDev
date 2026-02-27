<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/fullcalendar5.11.3/lib/main.min.js");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/fullcalendar5.11.3/lib/main.css");

?>
<div class="b2bcabinet-mainpage__wrapper">
    <div class="b2bcabinet-mainpage__calendar">
        <div class="fullcalendar__spinner">
            <i class="spinner-grow"></i>
            <p>
            <?=Loc::getMessage("CALENDAR_LOADER_TEXT")?>
            </p>
        </div>
    </div>
</div>

<script>
    BX.message({
        'BTN_PREV': '<?=Loc::getMessage('CALENDAR_BTN_PREV')?>',
        'BTN_NEXT': '<?=Loc::getMessage('CALENDAR_BTN_NEXT')?>',
        'BTN_TODAY': '<?=Loc::getMessage('CALENDAR_BTN_TODAY')?>',
        'BTN_MONTH': '<?=Loc::getMessage('CALENDAR_BTN_MONTH')?>',
        'BTN_WEEK': '<?=Loc::getMessage('CALENDAR_BTN_WEEK')?>',
        'BTN_DAY': '<?=Loc::getMessage('CALENDAR_BTN_DAY')?>',
        'BTN_LIST': '<?=Loc::getMessage('CALENDAR_BTN_LIST')?>',
        'BTN_WEEK_SHORT': '<?=Loc::getMessage('CALENDAR_BTN_WEEK_SHORT')?>',
        'BTN_ALL_DAY': '<?=Loc::getMessage('CALENDAR_BTN_ALL_DAY')?>',
        'BTN_MORE_LINK': '<?=Loc::getMessage('CALENDAR_BTN_MORE_LINK')?>',
        'BTN_NO_EVENTS': '<?=Loc::getMessage('CALENDAR_BTN_NO_EVENTS')?>',
    })
</script>