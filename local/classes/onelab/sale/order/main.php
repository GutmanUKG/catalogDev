<?php

namespace Onelab\Sale\Order;

class Main
{
    public static function onSavedHandler(\Bitrix\Sale\Order $order, bool $isNew)
    {
        $propertyCollection = $order->getPropertyCollection();

        if ($isNew) {
            foreach ($propertyCollection as $property) {
                
            }
        }
    }
}