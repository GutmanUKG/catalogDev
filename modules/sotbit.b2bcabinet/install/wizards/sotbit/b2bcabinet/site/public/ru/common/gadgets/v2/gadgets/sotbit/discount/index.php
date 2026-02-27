<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Sotbit\B2BCabinet\Shop\Discount;

Loc::loadMessages(__FILE__);

Asset::getInstance()->addCss($arGadget['PATH_SITEROOT'].'/styles.css');

    $discount = Discount::new();
    foreach ($discount  as $disc):
	?>
    <div class="widget_content widget_links widget_discount">
        <div class="widget_discount-img">
            <img src="<?=$arGadget['PATH_SITEROOT']?>/img/gift.png">
        </div>
        <div class="test_menu">
            <span><?=$disc['TITLE']?></span>
        </div>
    </div>
	<?
    endforeach;
?>