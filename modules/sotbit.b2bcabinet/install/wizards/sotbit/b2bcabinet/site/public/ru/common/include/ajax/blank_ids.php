<?php
use Bitrix\Main\Loader;

define('STOP_STATISTICS', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

Loader::includeModule('currency');

$id = htmlspecialchars($_POST['id']);
$qnt = htmlspecialchars($_POST['qnt']);
$maxQnt = htmlspecialchars($_POST['maxQnt']);

if(!empty($maxQnt) && ($qnt > $maxQnt)){
    $qnt = $maxQnt;
}
//$iblock = htmlspecialchars($_POST['iblock']);
$properties = $_POST['props'];

foreach ($properties as $key => $props){
    $properties[$key]["NAME"] = mb_convert_encoding($props["NAME"], LANG_CHARSET, mb_detect_encoding($props["NAME"]));
    $properties[$key]["VALUE"] = mb_convert_encoding($props["VALUE"], LANG_CHARSET, mb_detect_encoding($props["NAME"]));
}
$prices = $_POST['prices'];

$minPrice = array_shift($prices);
$currency = (!empty($_POST['baseCurrency']) ? $_POST['baseCurrency'] : '');
$minPrice = $minPrice['VALUE'];

foreach ($prices as $price)
{
    if($minPrice > $price['VALUE'])
        $minPrice = $price['VALUE'];
}

if($id > 0)
{
    if($id == 0 || $qnt <= 0)
    {
        unset($_SESSION['BLANK_IDS'][$id]);
        if(count($_SESSION['BLANK_IDS']) == 2 && isset($_SESSION['BLANK_IDS']['TOTAL_PRICE']) && isset($_SESSION['BLANK_IDS']['TOTAL_COUNT']))
        {
            unset($_SESSION['BLANK_IDS']['TOTAL_PRICE']);
            unset($_SESSION['BLANK_IDS']['TOTAL_COUNT']);
        }
    }
    else
    {
        $_SESSION['BLANK_IDS'][$id] = [
            'QNT' => $qnt,
            'PROPS' => $properties,
            'MIN_PRICE' => $minPrice,
            'CURRENCY' => $currency,
        ];
    }

    $total = 0;
    $totalCount = 0;

    foreach ($_SESSION['BLANK_IDS'] as $key => $product)
    {
        if($key !== 'TOTAL_PRICE' && $key !== 'TOTAL_COUNT')
        {
            $total += $product['QNT'] * $product['MIN_PRICE'];
            $totalCount++;
        }
    }

    if($total > 0)
    {
        $_SESSION['BLANK_IDS']['TOTAL_PRICE'] = CurrencyFormat($total, $currency);
        $_SESSION['BLANK_IDS']['TOTAL_COUNT'] = $totalCount;
    }
    echo \Bitrix\Main\Web\Json::encode($_SESSION['BLANK_IDS']);
    return;
}
?>