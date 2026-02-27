<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
$mainId = $arParams['ELEMENT_ID'];
$obName = 'ob_store_item_' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$itemIds = [
    'ID' => $mainId,
    'GENERAL_QUANTITY' => $mainId . '_generalQuantity',
    'STORES' => []
];

if (is_set($arResult['STORE_AMOUNT'])) {
    foreach ($arResult['STORE_AMOUNT'] as $key => $value) {
        $itemIds['STORES'][$key] = $mainId . '_store_' . $key;
    }
}

$generalQuantity = 0;
$quantityIcon = '';
$initStore = false;

if (isset($arParams['SHOW_MAX_QUANTITY']) && $arParams['SHOW_MAX_QUANTITY'] === 'M') {
    if (empty($arResult['QUANTITY'])) {
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

if ($arParams["USE_STORE"] == 'Y' && ($generalQuantity > 0 || ($quantityIcon !== 'empty' && $arParams['SHOW_MAX_QUANTITY'] === 'M')) && is_array($arResult['STORE_AMOUNT']) && count($arResult['STORE_AMOUNT']) > 0) {
    $initStore = true;
}
?>
<? if ($arParams['OFFERS_VIEW'] !== 'BLOCK'): ?>
    <div id="<?= $obName ?>" class="item-quantity">
        <span class="item-quantity__general" data-icon="<?= $quantityIcon ?>">
            <? if ($initStore): ?>
                <span class="item-quantity__value"><?= $generalQuantity ?></span>
            <? else: ?>
                <?= $generalQuantity ?>
            <? endif; ?>
        </span>
    </div>
<? endif; ?>

<? if ($initStore === true): ?>
    <?
        $popup = '<ul class="item-quantity__store-list" id="' . $itemIds['STORE_LIST'] . '">';
        foreach ($arResult['STORE_AMOUNT'] as $storeId => $itemQuantity) {
            if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                $quantityStoreIcon = '';
                if (empty($itemQuantity)) {
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
            $popup .= '<li class="item-quantity__store-item" id="' . $itemIds['STORES'][$storeId] . '"><span class="item-quantity__store-name" data-store-selector="name">' . ($arResult['STORES'][$storeId]['TITLE']['VALUE'] ?: "Name not found") . '</span><span class="item-quantity__store-quantity" data-store-selector="quantity" data-icon="' . $quantityStoreIcon . '"> ' . $itemQuantity . '</span></li>';
        }
        $popup .= '</ul>';
    ?>
    <script type="text/javascript">
        BX.message({
            RELATIVE_QUANTITY_EMPTY: '<?=CUtil::JSEscape($arParams['MESS_NOT_AVAILABLE'])?>',
            RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
            RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
        })

        <?=$obName?> = new window.JCSotbitStoreAmount({
            obName: '<?=$arParams['CONTAINER_ID'] ?: $obName?>',
            html: '<?=$popup?>'
        })

        <?=$obName?>.init();

        if (typeof closeAllPopup === 'undefined'){
            function closeAllPopup (event) {
                if (!event.target.closest('.ph-question')) {
                    const allPopups = document.querySelectorAll('.item-quantity__store-list__wrap');
                    
                    if (allPopups.length !== 0) {
                        allPopups.forEach((item) => {
                            item.style.display = 'none';
                        })
                    }
                }
            }
        }

        document.addEventListener('click', closeAllPopup);
    </script>
<? endif; ?>