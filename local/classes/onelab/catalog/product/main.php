<?php

namespace Onelab\Catalog\Product;

class Main
{
    public static function isProductPreOrder($item)
    {
        return $item['CATALOG_QUANTITY'] == 888888888;
    }
}