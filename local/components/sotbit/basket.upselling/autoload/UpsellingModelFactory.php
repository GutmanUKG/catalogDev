<?php

namespace Sotbit\UpselingComponent;

use Sotbit\UpselingComponent\BasketUpsellingOffersModel;
use Sotbit\UpselingComponent\BasketUpsellingModel;
use Bitrix\Catalog\CatalogIblockTable;

class UpsellingModelFactory
{
    static function getModel(int $iblock_id, array $properties, ?array $typePrice)
    {
        $offerRef = CatalogIblockTable::query()
            ->addSelect('IBLOCK_ID')
            ->addSelect('SKU_PROPERTY_ID')
            ->where('PRODUCT_IBLOCK_ID', $iblock_id)
            ->fetch();

        if (empty($offerRef)) {
            return new BasketUpsellingModel($iblock_id, $properties, $typePrice);
        } else {
            return new BasketUpsellingOffersModel(
                $iblock_id,
                $properties,
                $offerRef['IBLOCK_ID'],
                $offerRef['SKU_PROPERTY_ID'],
                $typePrice,
            );
        }
    }
}