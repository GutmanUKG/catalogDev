<?php

namespace Sotbit\B2BCabinet\CalendarEvent;

use Sotbit\B2BCabinet\Internals\CalendarEventTable,
    Bitrix\Sale\Order;

class Eventhandlers
{

    public static function OnSaleStatusOrder($orderID, $status)
    {
        if (!$orderID) {
            return;
        }

        $order = Order::load($orderID);

        CalendarEventTable::add(
            [
                "CODE" => "ORDER_STATUS_CHANGE",
                "VALUES" => [
                    "ORDER_ID" => $orderID,
                    "ACCOUNT_NUMBER" => $order->getField("ACCOUNT_NUMBER"),
                    "STATUS" => $status,
                ],
                "USER_ID" => $order->getField("USER_ID")
            ]
        );
    }

    public static function OnOrderAdd($orderID, $arFields)
    {
        CalendarEventTable::add(
            [
                "CODE" => "ORDER_ADD",
                "VALUES" => [
                    "ORDER_ID" => $orderID,
                    "ACCOUNT_NUMBER" => $arFields["ACCOUNT_NUMBER"],
                ],
                "USER_ID" => $arFields["USER_ID"]
            ]
        );
    }

    public static function OnSaleCancelOrder($orderId, $value, $description)
    {
        $code = $value == "Y" ? "ORDER_CANCELLED" : "CANCELLATION_CANCELED";
        $order = Order::load($orderId);

        CalendarEventTable::add(
            [
                "CODE" => $code,
                "VALUES" => [
                    "ORDER_ID" => $orderId,
                    "ACCOUNT_NUMBER" => $order->getField("ACCOUNT_NUMBER"),
                    "DESCRIPTION" => $description,
                ],
                "USER_ID" => $order->getField("USER_ID")
            ]
        );
    }

    public static function OnSalePayOrder($orderId, $value)
    {
        $code = $value == "Y" ? "ORDER_PAID" : "PAYMENT_CANCELED";
        self::addDefaultData($code, $orderId);
    }

    public static function OnSaleDeliveryOrder($orderId, $value)
    {
        $code = $value == "Y" ? "ORDER_SHIPPED" : "SHIPMENT_CANCELED";
        self::addDefaultData($code, $orderId);
    }

    static function addDefaultData($eventCode, $orderId)
    {
        $order = Order::load($orderId);

        CalendarEventTable::add(
            [
                "CODE" => $eventCode,
                "VALUES" => [
                    "ORDER_ID" => $orderId,
                    "ACCOUNT_NUMBER" => $order->getField("ACCOUNT_NUMBER"),
                ],
                "USER_ID" => $order->getField("USER_ID")
            ]
        );
    }
}