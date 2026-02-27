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
    $productsFilter = array(
		'CAN_BUY' => 'Y',
		'DELAY' => 'N',
		'SUBSCRIBE' => 'N'
	);
    $imgProp = array(
		'width' => 70,
		'height' => 70,
		'resize' => BX_RESIZE_IMAGE_PROPORTIONAL,
        'noPhoto' => SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg'
	);

    $Items = new \Sotbit\B2BCabinet\Shop\BasketItems($productsFilter, $imgProp);
    ?>

    <div class="widget_content widget_links widget-pending">
        <?if ($useMultibasket):?>
            <div data-multibasketInclud="true" style="display: none;"></div>
        <?endif;?>
        <span class="quantity-products"><?= $Items->getQnt() ?></span>
        <span><?= \Sotbit\B2bCabinet\Element::num2word(
                $Items->getQnt(),
                array(
                    Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_1'),
                    Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_2'),
                    Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_3')
                ));
            ?></span>
        <span class="total-price"><?= $Items->getSum() ?></span>
        <div class="widget-pending-goods">
            <? foreach ($Items->getItems() as $item)
            {
                $img = $item->getElement()->getImg();
                ?>
                <div class="block-cart-img">
                    <img class="h-100 w-100" src="<?=( !empty($img['src']) ? $img['src'] : '' )?>">
                </div>
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
    ?>
    <script>
        BX.message({
            'BASKET_PRODUCTS_1': '<?=Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_1')?>',
            'BASKET_PRODUCTS_2': '<?=Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_2')?>',
            'BASKET_PRODUCTS_3': '<?=Loc::getMessage('GD_SOTBIT_CABINET_BASKET_PRODUCTS_3')?>',
        });

        function num_word(value, words){  
            value = Math.abs(value) % 100; 
            var num = value % 10;
            if(value > 10 && value < 20) return words[2]; 
            if(num > 1 && num < 5) return words[1];
            if(num == 1) return words[0]; 
            return words[2];
        }

        if (typeof nodeGadget === 'undefined'){
            var nodeGadget = document.getElementById('t<?=$arGadget["ID"]?>');
            BX.addCustomEvent(window, 'OnBasketChange', function() {
                BX.showWait(nodeGadget);
                BX.ajax.runAction('sotbit:b2bcabinet.basket.getBasketItems',{
                    data: {
                        arFields: {
                            'PRODUCTS_FILTER': <?=CUtil::PhpToJSObject($productsFilter)?>,
                            'IMG_PROP': <?=CUtil::PhpToJSObject($imgProp)?>
                        }
                    }
                })
                .then(
                    function(result) {
                        BX.closeWait(nodeGadget);

                        if (!result.errors.length) {
                            const wrapperProduct = nodeGadget.querySelector('.widget-pending-goods');

                            nodeGadget.querySelector('.total-price').innerHTML = result.data.print_price;
                            nodeGadget.querySelector('.quantity-products').innerHTML = result.data.quantity;
                            nodeGadget.querySelector('.quantity-products + span').innerHTML = num_word(result.data.quantity, [
                                BX.message('BASKET_PRODUCTS_1'),
                                BX.message('BASKET_PRODUCTS_2'),
                                BX.message('BASKET_PRODUCTS_3')
                            ])
                            wrapperProduct.innerHTML = result.data.products.reduce((accHTML, product) => {
                                return accHTML + `<div class="block-cart-img"><img class="h-100 w-100" src="${product.img.src}"></div>`
                            }, '');
                        }
                    }
                )
            });
        }
    </script>
    <?
}
?>

<?if ($useMultibasket):?>
    <script>
        BX.addCustomEvent(
            window,
            'sotbitMultibasketInitialized',
            function() {
                const multbaksetForGadget = document.querySelector('div[data-multibasketInclud="true"]');
                const parent = multbaksetForGadget.closest('.sotbit-cabinet-gadget-basket')
                const gadgetTitle = parent.querySelector('.card-header h5');

                if (parent.querySelector('[data-type="multibasket-color"]')) {
                    return;
                }

                const multibasketColor = document.createElement('div');
                multibasketColor.setAttribute('data-type', 'multibasket-color');
                const color = BX.Sotbit.MultibasketComponent.instance.baskets.filter(i => i.CURRENT_BASKET)[0].COLOR;
                multibasketColor.style = `position:absolute;inset:auto 1rem .5rem;height: 2px; background-color: #${color};`;
                gadgetTitle.after(multibasketColor);
            }.bind(this),
        );
    </script>
<?endif;?>
