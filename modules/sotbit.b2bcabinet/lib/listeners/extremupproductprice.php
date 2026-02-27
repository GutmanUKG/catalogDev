<?php

namespace Sotbit\B2BCabinet\Listeners;

use Bitrix\Main;
use Bitrix\Catalog;
use Bitrix\Iblock;
use Bitrix\Main\ORM\Query\Filter;


class ExtremupProductPrice
{
    const MIN_PRICE_CODE = 'MINIMUM_PRICE';
    const MAX_PRICE_CODE = 'MAXIMUM_PRICE';

    public static function updateOrAddProductPrice(Main\Event $e): void
    {
        if (!self::includeModules()) {
            return;
        }

        $productId = $e->getParameter('object')->getProductId();

        if (empty($productId)) {
            $price = Catalog\PriceTable::getById($e->getParameter('object')->getId())->fetchObject();
            $productId = $price->getProductId();
        }

        $iblockElementId = self::getIblockElementId($productId);

        if ($iblockElementId === 0) {
            return;
        }

        $property = self::getExtrmumPriceProperty($productId, $iblockElementId);

        if (count($property) === 0) {
            return;
        }

        $prices = self::getProductPrices($productId);

        self::setNewExtremumPropertyValue($property, $prices->getPriceList(), $productId);
    }

    public static function deleteProductPrice($productId, &$arExceptionIDs): bool
    {
        $iblockElementId = self::getIblockElementId($productId);

        if ($iblockElementId === 0) {
            return true;
        }

        $property = self::getExtrmumPriceProperty($productId, $iblockElementId);

        if (count($property) === 0) {
            return true;
        }

        $prices = self::getProductPrices($productId);

        foreach ($prices as $price) {
            if (!in_array($price->getId(), $arExceptionIDs)) {
                $prices->remove($price);
            }
        }

        self::setNewExtremumPropertyValue($property, $prices->getPriceList(), $productId);

        return true;
    }

    private static function includeModules(): bool
    {
        $catalog = Main\Loader::includeModule('catalog');
        $iblock = Main\Loader::includeModule('iblock');

        if ($catalog && $iblock) {
            return true;
        }

        return false;
    }

    private static function getIblockElementId(int $productId): int
    {
        $iblockId = Iblock\ElementTable::query()
            ->addSelect('IBLOCK_ID')
            ->where('ID', $productId)
            ->fetch()
        ;

        if (!$iblockId) {
            return 0;
        }

        return (int)$iblockId['IBLOCK_ID'];
    }
    
    private static function getProductPrices(int $productId): Catalog\EO_Price_Collection
    {
        /** @var Catalog\EO_Price_Collection */
        $prices = Catalog\PriceTable::query()
            ->setSelect(['PRICE', 'ID'])
            ->where('PRODUCT_ID', $productId)
            ->fetchCollection()
        ;

        return $prices;
    }

    private static function getExtrmumPriceProperty(int $productId, $iblockElementId): array
    {
        $filter = (new Filter\ConditionTree())
            ->logic(Filter\ConditionTree::LOGIC_OR)
            ->where('CODE', self::MIN_PRICE_CODE)
            ->where('CODE', self::MAX_PRICE_CODE)
        ;

        $property = Iblock\PropertyTable::query()
            ->setSelect(['ID', 'CODE'])
            ->where('IBLOCK_ID', $iblockElementId)
            ->where($filter)
            ->fetchAll()
        ;

        return $property;
    }

    private static function setNewExtremumPropertyValue(array $property, array $prices, int $productId): void
    {
        foreach ($property as $key => $i) {
            if ($i['CODE'] === self::MIN_PRICE_CODE && !empty($prices)) {
                $property[$key]['VALUE'] = min($prices);
            } elseif ($i['CODE'] === self::MAX_PRICE_CODE && !empty($prices)) {
                $property[$key]['VALUE'] = max($prices);
            }

            \CIBlockElement::SetPropertyValuesEx(
                $productId,
                false,
                [
                    $i['CODE'] => $property[$key]['VALUE']
                ]
            );
        }
    }
}