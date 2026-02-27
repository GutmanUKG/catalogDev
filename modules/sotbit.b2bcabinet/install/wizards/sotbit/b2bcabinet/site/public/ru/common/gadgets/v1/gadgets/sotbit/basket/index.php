<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Sotbit\Multibasket\Helpers\Config;
use Bitrix\Main\Context;

Loc::loadMessages(__FILE__);

Asset::getInstance()->addCss($arGadget['PATH_SITEROOT'].'/styles.css');
$idUser = intval($USER->GetID());

$useMultibasket = Loader::includeModule('sotbit.multibasket')
    && Config::moduleIsEnabled(Context::getCurrent()->getSite());


if(Loader::includeModule('sotbit.b2bcabinet') && $idUser > 0)
{
    $Items = new \Sotbit\B2BCabinet\Shop\BasketItems(array(
		'CAN_BUY' => 'Y',
		'DELAY' => 'N',
		'SUBSCRIBE' => 'N'
	), array(
		'width' => 70,
		'height' => 70,
		'resize' => BX_RESIZE_IMAGE_PROPORTIONAL,
        'noPhoto' => SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg'
	));
    ?>

    <div class="widget_content widget_links widget-pending">
        <?if ($useMultibasket):?>
            <div data-multibasketInclud="true" style="display: none;"></div>
        <?endif;?>
        <span><?= $Items->getQnt() ?></span>
        <span><?= \Sotbit\B2bCabinet\Element::num2word(
                $Items->getQnt(),
                array(
                    Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_1'),
                    Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_2'),
                    Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_3')
                ));
            ?></span>
        <span><?= $Items->getSum() ?></span>
        <div class="widget-pending-goods">
            <? foreach ($Items->getItems() as $item)
            {
                $img = $item->getElement()->getImg();
                ?>
                <div class="block-cart-img"  style="background-image: url('<?=( !empty($img['src']) ? $img['src'] : '' )?>');"></div>
            <? } ?>
        </div>
    </div>
	<?php
	if($arParams['G_BASKET_PATH_TO_BASKET'])
	{
		?>
		<div class="">
			<a href="<?= $arParams['G_BASKET_PATH_TO_BASKET'] ?>" class="main-link b2b-main-link">
				<?= Loc::getMessage('GD_SOTBIT_CABINET_BASKET_MORE') ?>
			</a>
		</div>
		<?
	}
}
?>

<?if ($useMultibasket):?>
    <script>
        BX.addCustomEvent(
            window,
            'sotbitMultibasketInitialized',
            function() {
                // this.multibasket.instance.setAdditionsBasketListRoot(multibaksetListWrapper);
                const multbaksetForGadget = document.querySelector('div[data-multibasketInclud="true"]');

                const getParent = function(node, parentTag) {
                    const parent = node.parentNode;

                    if (parent.tagName === parentTag) {
                        return parent;
                    }

                    return getParent(parent, parentTag);
                };
                const parent = getParent(multbaksetForGadget, 'TD');
                const gadgetTitle = parent.querySelector('h5.card-title');
                gadgetTitle.style = 'margin-right: 5px;'

                if (parent.querySelector('[data-type="multibasket-color"]')) {
                    return;
                }

                const multibasketColor = document.createElement('div');
                multibasketColor.setAttribute('data-type', 'multibasket-color');
                const color = BX.Sotbit.MultibasketComponent.instance.baskets.filter(i => i.CURRENT_BASKET)[0].COLOR;
                multibasketColor.style = `width: 23px;height: 18px; background-color: #${color}; border-radius: 4px; margin-right: auto;`;
                gadgetTitle.after(multibasketColor);
            }.bind(this),
        );
    </script>
<?endif;?>
