<?php
namespace Onelab;
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/basket.php");
$events_basket =  new BasketEvent; // События корзины

if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "REMOVE_ITEM_BASKET"){
    $events_basket-> removeItemBasket($_POST['ID']);
}

if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "ORDER_BASKET"){
    $events_basket-> orderBasket();
}