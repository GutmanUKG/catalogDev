<?php

namespace Sotbit\UpselingComponent;

use Bitrix\Catalog\ProductTable;
use Bitrix\Iblock\ElementPropertyTable;

class BasketUpsellingOffersModel extends BasketUpsellingModel
{
    protected $parentProducts;
    protected $sku_property_id;
    protected $offers_iblock_id;
    protected $offerID_productID;

    function __construct(
        int $iblock_id,
        array $properties,
        int $offers_iblock_id,
        int $sku_property_id,
        ?array $typePrice
    )
    {
        $this->sku_property_id = $sku_property_id;
        $this->offers_iblock_id = $offers_iblock_id;
        parent::__construct($iblock_id, $properties, $typePrice);
    }

    public function fetchAndPostProcessing($cpu = '', $iteRoot = '', $aliases = [])
    {
        $this->unpreparedResult = $this->fetchAll();

        $this
            ->getProductId()
            ->addImagePath()
            ->getDetalPageUrl($cpu, $iteRoot, $aliases);

        if ($this->unpreparedResult === []) {
            return $this;
        }

        $this->offerID_productID = array_column(
            ElementPropertyTable::query()
                ->addSelect('IBLOCK_ELEMENT_ID')
                ->addSelect('VALUE')
                ->where('IBLOCK_PROPERTY_ID', $this->sku_property_id)
                ->whereIn('VALUE', $this->product_id)
                ->fetchAll(),
                'VALUE',
                'IBLOCK_ELEMENT_ID',
        );

        foreach ($this->unpreparedResult as $key => $value) {
            if (in_array($value['ID'], $this->offerID_productID)) {
                $this->parentProducts[] = $value;
                unset($this->unpreparedResult[$key]);
            }
        }

        if (count($this->unpreparedResult) > 0) {
            $this
                ->addDiscountNicePrice()
                ->addMeasureRatio()
                ->addPropertyTable();
        }

        if (count($this->offerID_productID) === 0) {
            return $this;
        }

        $productsWithoutOffers = parent::getUnpreparedResult();

        $this->unpreparedResult = (new BasketUpsellingModel(
            $this->offers_iblock_id,
            $this->properties,
            $this->typePrice,
        ))
            ->whereIn(
                ProductTable::getTableName().'ID',
                count($this->offerID_productID) ? array_keys($this->offerID_productID) : '',
            )
            ->fetchAndPostProcessing()
            ->getUnpreparedResult();

        $this
            ->setDetalPageUrlforOffers()
            ->addOffersImagePath();

        $this->unpreparedResult = array_merge($this->unpreparedResult, $productsWithoutOffers);

        return $this;
    }

    public function preparetionForSend(bool $privatPriceInabl): array
    {
        $this->unsetExtraFields();
        $preparedResult = $this->unpreparedResult;

        if ($privatPriceInabl) {
            $preparedResult = $this->applyPrivatePrices($preparedResult);
        }

        return $preparedResult;
    }

    protected function addOffersImagePath(): self
    {
        foreach ($this->unpreparedResult as $key => $i) {
            if (empty($i['PREVIEW_PICTURE'])) {
                $offer_id = $this->offerID_productID[$i[ProductTable::getTableName().'ID']];
                $image = $this->getProductByofferID($offer_id)['PREVIEW_PICTURE'];
                $this->unpreparedResult[$key]['PREVIEW_PICTURE'] = $image;
            }
        }

        return $this;
    }

    protected function getProductByofferID($offer_id): array
    {
        $parentProducts_key = array_search(
            $offer_id,
            array_column($this->parentProducts, ProductTable::getTableName().'ID'),
        );
        return $this->parentProducts[$parentProducts_key];
    }

    protected function setDetalPageUrlforOffers(): self
    {
        foreach  ($this->unpreparedResult as $key => $i) {
            $offer_id = $this->offerID_productID[$i[ProductTable::getTableName().'ID']];
            $url = $this->getProductByofferID($offer_id)['DETAIL_PAGE_URL'];
            $this->unpreparedResult[$key]['DETAIL_PAGE_URL'] = $url;
        }

        return $this;
    }

}