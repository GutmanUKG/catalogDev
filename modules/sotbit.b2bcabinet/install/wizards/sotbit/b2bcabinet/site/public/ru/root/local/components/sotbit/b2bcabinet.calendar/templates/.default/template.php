<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/fullcalendar5.7.2/lib/main.js");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/fullcalendar5.7.2/lib/main.css");

?>

<div class="b2bcabinet-mainpage__calendar">
    <div class="fullcalendar__spinner">
        <i class="icon-spinner6 spinner"></i>
        <p>
           <?=Loc::getMessage("CALENDAR_LOADER_TEXT")?>
        </p>
    </div>
</div>
