<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);
$mainId = $arParams['ELEMENT_ID'];
$obName = 'ob_store_item_'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$itemIds = [
	'ID' => $mainId,
	'GENERAL_QUANTITY' => $mainId . '_generalQuantity',
	'STORES' => []
];

if (is_set($arResult['STORE_AMOUNT'])) {
	foreach ($arResult['STORE_AMOUNT'] as $key => $value)
	{
		$itemIds['STORES'][$key] = $mainId . '_store_' . $key;
	}
}

$generalQuantity = 0;
$quantityIcon = '';

if (isset($arParams['SHOW_MAX_QUANTITY']) && $arParams['SHOW_MAX_QUANTITY'] === 'M')
{
	if (empty($arResult['QUANTITY']))
	{
		$generalQuantity = $arParams['MESS_NOT_AVAILABLE'];
		$quantityIcon = 'empty';
	} else {
		$generalQuantity = $arResult['QUANTITY'] > $arParams['RELATIVE_QUANTITY_FACTOR']
			? $arParams['MESS_RELATIVE_QUANTITY_MANY']
			: $arParams['MESS_RELATIVE_QUANTITY_FEW'];

		$quantityIcon = $arResult['QUANTITY'] > $arParams['RELATIVE_QUANTITY_FACTOR']
			? "many"
			: "few";
	}
} else {
	$generalQuantity = $arResult['QUANTITY'];
}
?><div id="<?=$obName?>" class="item-quantity">
<?php
$html = '';
?>
            <? if ($arParams["USE_STORE"] == 'Y' && ($generalQuantity > 0 || ($quantityIcon !== 'empty' && $arParams['SHOW_MAX_QUANTITY'] === 'M')) && is_array($arResult['STORE_AMOUNT']) && count($arResult['STORE_AMOUNT']) > 0): ?>
     <?php
    $html = "<ul class='item-quantity__store-list_tooltip ' id='" . $itemIds["STORE_LIST"] . "'>";
    ?>
    <? foreach ($arResult['STORE_AMOUNT'] as $storeId => $itemQuantity): ?>
		<? if ($arParams['SHOW_MAX_QUANTITY'] === 'M')
		{
			$quantityStoreIcon = '';
			if (empty($itemQuantity))
			{
				$itemQuantity = $arParams['MESS_NOT_AVAILABLE'];
				$quantityStoreIcon = 'empty';
			} else {
				$quantityStoreIcon = $itemQuantity > $arParams['RELATIVE_QUANTITY_FACTOR']
					? "many"
					: "few";
				$itemQuantity = $itemQuantity > $arParams['RELATIVE_QUANTITY_FACTOR']
					? $arParams['MESS_RELATIVE_QUANTITY_MANY']
					: $arParams['MESS_RELATIVE_QUANTITY_FEW'];
			}
		}
		?>

        <?php
        $name = $arResult['STORES'][$storeId]['TITLE']['VALUE']?:"Name not found";
        $html = $html ."<li class='item-quantity__store-item' id='". $itemIds['STORES'][$storeId]. "'>
                <span class='item-quantity__store-name' data-store-selector='name'>". $name ."</span>
                <span class='item-quantity__store-quantity' data-store-selector='quantity' data-icon='" . $quantityStoreIcon ."'>" . $itemQuantity."</span></li>";
        endforeach;
   $html = $html . "</ul>";
 endif; ?>




	<span class="item-quantity__general" data-icon="<?=$quantityIcon?>">
		<?=$generalQuantity ? $generalQuantity : "0" ?>
            <? if ($arParams["USE_STORE"] == "Y" && ($generalQuantity > 0 || ($quantityIcon !== "empty" && $arParams['SHOW_MAX_QUANTITY'] === 'M')) && is_array($arResult['STORE_AMOUNT']) && count($arResult['STORE_AMOUNT']) > 0): ?>
                <i id="tooltip-show"  type="button" class="icon-question3 mr-1" data-placement="right" data-popup="tooltip" title="" data-html="true" data-original-title="<?= $html?>"></i>
           <?endif;?>
			</span>
</div> <?// item-quantity ?>

<script type="text/javascript">
	// BX.bind(BX('update-button'), 'click', function(){BX.onCustomEvent('updateItemsStoreData')}); store refresh call example
	BX.message({
		RELATIVE_QUANTITY_EMPTY: '<?=CUtil::JSEscape($arParams['MESS_NOT_AVAILABLE'])?>',
		RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
		RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
	})

    <?=$obName?> = new window.JCSotbitStoreAmount({
        signedParameters: "<?= $this->getComponent()->getSignedParameters() ?>",
		itemIds: <?=CUtil::PhpToJSObject($itemIds)?>,
		arParams: <?=CUtil::PhpToJSObject($arParams)?>,
		arResult: <?=CUtil::PhpToJSObject($arResult)?>
	}).init()
</script>