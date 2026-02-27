<?php
namespace Sotbit\UpselingComponent;

use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\PriceTable;
use Bitrix\Main\FileTable as MainFileTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\ElementPropertyTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Config\Option;
use Bitrix\Iblock\PropertyFeatureTable;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Bitrix\Catalog\MeasureRatioTable;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use CIBlock;

class BasketUpsellingModel extends Query
{
    const filds = [
        ElementTable::class => [
            'PREVIEW_PICTURE', 'NAME', 'DETAIL_PICTURE', 'ID', 'IBLOCK_ID',
            'IBLOCK_SECTION_ID', 'CODE', 'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL',
        ],
        ProductTable::class => ['AVAILABLE', 'QUANTITY', 'ID', 'QUANTITY_TRACE'],
        PriceTable::class => ['CURRENCY'],
        MeasureRatioTable::class => ['RATIO'],
    ];

    protected $iblock_id;
    protected $product_id;
    protected $typePrice;
    /*
        $properties keys:
            LIST_PAGE_SHOW
            IN_BASKET
            OFFER_TREE
    */
    protected $properties;
    protected $use_b_iblock_property_feature;
    protected $unpreparedResult;

    public function __construct(int $iblock_id, array $properties, ?array $typePrice)
    {
        $this->typePrice = $typePrice;
        $this->iblock_id = $iblock_id;
        $this->use_b_iblock_property_feature = 'Y' === Option::get(
            'iblock', 'property_features_enabled'
        );
        $this->setDisplayedProperties($properties);

        parent::__construct(ElementTable::getEntity());
        $this
            ->where('IBLOCK_ID', '=', $this->iblock_id)
            ->where('ACTIVE', 'Y');

        if (self::filds[ElementTable::class] !== []) {
            $this->setSelect(array_merge(
                self::filds[ElementTable::class],
            ));
        }

        $this->addProductTable();

        $this->addPriceTable();
    }

    public function fetchAndPostProcessing($cpu = '', $iteRoot = '', $aliases = [])
    {
        $this->unpreparedResult = $this->fetchAll();

        $cunBuyZero = \Bitrix\Main\Config\Option::get('catalog', 'default_can_buy_zero', 'N', SITE_ID);

        if ($cunBuyZero === 'Y') {
            foreach ($this->unpreparedResult as $key => $item) {
                $this->unpreparedResult[$key]["b_catalog_productQUANTITY"] = 9007199254740991;
            }
        }

        if ($this->unpreparedResult === []) {
            return $this;
        }

        $this
            ->getDetalPageUrl($cpu, $iteRoot, $aliases)
            ->getProductId()
            ->addImagePath()
            ->addDiscountNicePrice()
            ->addMeasureRatio()
            ->addPropertyTable();

        return $this;
    }

    public function preparetionForSend(bool $privatPriceEnabled): array
    {
        $this->unsetExtraFields();
        $preparedResult = $this->unpreparedResult;

        if ($privatPriceEnabled) {
            $preparedResult = $this->applyPrivatePrices($preparedResult);
        }

        return $preparedResult;
    }

    public function getUnpreparedResult(): array
    {
        return $this->unpreparedResult;
    }

    protected function applyPrivatePrices(array $preparedResult): array
    {
        if (!PrivatePriceAdapter::privatePriceIsEnabled()) {
            return $preparedResult;
        }
        $productId = array_column($preparedResult, ProductTable::getTableName().'ID');
        $privatePrice = (new PrivatePriceAdapter($productId))->get();

        foreach ($preparedResult as $key => $i) {
            $price = $privatePrice[$i[ProductTable::getTableName().'ID']] ?? $preparedResult[$key]['DISPLAY_PRICE'];
            $preparedResult[$key]['DISPLAY_PRICE'] = $price;
        }

        return $preparedResult;
    }

    protected function getDetalPageUrl($cpu, $siteRoot, $aliases): self
    {
        $useReplace = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
        $replaceValue = null;
        if ($useReplace) {
            $replaceableValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
            $replaceValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);
        }

        foreach ($this->unpreparedResult as $key => $i) {
            if ($cpu === 'Y') {
                $this->unpreparedResult[$key]['DETAIL_PAGE_URL'] = $siteRoot . CIBlock::ReplaceDetailUrl(
                    $i['DETAIL_PAGE_URL'], $i, true, 'E',
                );
            } else {
                $this->unpreparedResult[$key]['DETAIL_PAGE_URL'] =
                    $siteRoot . "/orders/blank_zakaza/?{$aliases['SECTION_ID']}={$i['IBLOCK_SECTION_ID']}&{$aliases['ELEMENT_ID']}={$i['ID']}";
            }

            if ($replaceValue) {
                $this->unpreparedResult[$key]['DETAIL_PAGE_URL'] = str_replace($replaceableValue, $replaceValue, $this->unpreparedResult[$key]['DETAIL_PAGE_URL']);
            }
        }

        return $this;
    }

    protected function getProductId(): self
    {
        $this->product_id = array_column($this->unpreparedResult, ProductTable::getTableName().'ID');
        return $this;
    }

    protected function addMeasureRatio(): self
    {
        $ratio = MeasureRatioTable::query()
            ->addSelect('RATIO')
            ->addSelect('PRODUCT_ID')
            ->whereIn('PRODUCT_ID', $this->product_id)
            ->fetchAll();

        $arRatio = array_column($ratio, 'RATIO', 'PRODUCT_ID');

        $productIdWhithoutRatio = array_diff(
            $this->product_id,
            array_keys($arRatio),
        );

        foreach ($productIdWhithoutRatio as $i) {
            $arRatio[$i] = 1;
        }

        $this->unpreparedResult = array_map(function ($i) use ($arRatio) {
            $i['ratio'] = $arRatio[$i[ProductTable::getTableName().'ID']];
            return $i;
        }, $this->unpreparedResult);

        return $this;
    }

    protected function unsetExtraFields(): self
    {
        $this->unpreparedResult = array_map(function($i) {
            unset($i['DETAIL_PICTURE']);
            unset($i[PriceTable::getTableName().'CURRENCY']);
            unset($i['optimal_price']);
            unset($i['ID'], $i['CODE']);
            unset($i['IBLOCK_SECTION_ID']);
            unset($i[PriceTable::getTableName().'ID']);
            return $i;
        }, $this->unpreparedResult);
        return $this;
    }

    protected function setDisplayedProperties(array $properties): void
    {
        if ($this->use_b_iblock_property_feature) {
            $show_property_id = PropertyFeatureTable::query()
                ->addSelect('PROPERTY_ID')
                ->addSelect('FEATURE_ID')
                ->whereIn('FEATURE_ID', ['IN_BASKET', 'LIST_PAGE_SHOW', 'OFFER_TREE'])
                ->where('IS_ENABLED', 'Y')
                ->fetchAll();

            $this->properties['OFFER_TREE'] = array_column(
                array_filter($show_property_id, function($i){ return $i['FEATURE_ID'] === 'OFFER_TREE'; }),
                'PROPERTY_ID'
            );
        } else {
            $this->properties['IN_BASKET'] = $properties['IN_BASKET'];
            $this->properties['OFFER_TREE'] = $properties['OFFER_TREE'];
        }

        $this->properties['LIST_PAGE_SHOW'] = $properties['LIST_PAGE_SHOW'];
    }

    protected function addPropertyTable(): self
    {
        $unprepared_property = $this->getProperty($this->unpreparedResult);
        if ($unprepared_property === []) {
            return $this;
        }

        $property_all = $this->getValuesPropertyFromTypeList($unprepared_property);

        $property_all = $this->getPropFromHLBlock($property_all);

        $LIST_PAGE_SHOW = [];
        $IN_BASKET = [];
        $OFFER_TREE = [];

        foreach ($property_all as $item) {
            if (in_array($item[PropertyTable::getTableName().'ID'], $this->properties['LIST_PAGE_SHOW'])) {
                $i = $item;
                unset($i['ID'], $i[PropertyTable::getTableName().'ID'], $i[PropertyTable::getTableName().'PROPERTY_TYPE']);
                $LIST_PAGE_SHOW[$i['IBLOCK_ELEMENT_ID']][] = [
                    'NAME' => $i[PropertyTable::getTableName().'NAME'],
                    'VALUE' => $i['user_prop'] ?? $i['VALUE'],
                    'TYPE' => $i[PropertyTable::getTableName().'USER_TYPE_SETTINGS_LIST'] ? $i['user_prop_type'] : 'string',
                ];
            }
            if (in_array($item[PropertyTable::getTableName().'ID'], $this->properties['IN_BASKET'] ?: [])) {
                $i = $item;
                $IN_BASKET[$i['IBLOCK_ELEMENT_ID']][] = [
                    'NAME' => $i[PropertyTable::getTableName().'NAME'],
                    'VALUE' => $i['user_prop'] ?? $i['VALUE'],
                    'TYPE' => $i[PropertyTable::getTableName().'USER_TYPE_SETTINGS_LIST'] ? $i['user_prop_type'] : 'string',
                ];
            }
            if (in_array($item[PropertyTable::getTableName().'ID'], $this->properties['OFFER_TREE'])) {
                $i = $item;
                $OFFER_TREE[$i['IBLOCK_ELEMENT_ID']][] = [
                    'NAME' => $i[PropertyTable::getTableName().'NAME'],
                    'VALUE' => $i['user_prop'] ?? $i['VALUE'],
                    'TYPE' => $i[PropertyTable::getTableName().'USER_TYPE_SETTINGS_LIST'] ? $i['user_prop_type'] : 'string',
                ];
            }
        }

        $this->unpreparedResult = array_map(function($i) use ($LIST_PAGE_SHOW, $IN_BASKET, $OFFER_TREE) {
            $i['LIST_PAGE_SHOW'] = $LIST_PAGE_SHOW[$i[ProductTable::getTableName().'ID']];
            $i['IN_BASKET'] = $IN_BASKET[$i[ProductTable::getTableName().'ID']];
            $i['OFFER_TREE'] = $OFFER_TREE[$i[ProductTable::getTableName().'ID']];
            return $i;
        }, $this->unpreparedResult);

        return $this;
    }

    protected function addImagePath(): self
    {
        $emttyFoto = '/local/templates/b2bcabinet/assets/images/no_photo.svg';

        $image_id = array_map(
            function($i) { return $i['PREVIEW_PICTURE'] ?? $i['DETAIL_PICTURE']; },
            $this->unpreparedResult);

        if ($image_id === []) {
            return $this;
        }

        $image_path_unprepared = MainFileTable::query()
            ->setSelect(['FILE_NAME', 'SUBDIR', 'ID'])
            ->whereIn('ID', $image_id)
            ->fetchAll();

        $image_path = [];

        foreach ($image_path_unprepared as $i) {
            $image_path[$i['ID']] = "/upload/{$i['SUBDIR']}/{$i['FILE_NAME']}";
        }

        $this->unpreparedResult = array_map(function($i) use ($image_path) {
            return array_merge(
                $i,
                ['PREVIEW_PICTURE' => $image_path[$i['PREVIEW_PICTURE'] ?? $i['DETAIL_PICTURE']]],
            );
        },
            $this->unpreparedResult,
        );
        return $this;
    }

    protected function addDiscountNicePrice(): self
    {
        $priceIdDbFilds = PriceTable::query()
            ->setSelect(['ID', 'PRODUCT_ID'])
            ->whereIn('PRICE', array_column($this->unpreparedResult, 'optimal_price'))
            ->whereIn('PRODUCT_ID', array_column($this->unpreparedResult, ProductTable::getTableName().'ID'))
            ->fetchAll()
        ;

        $priceId = array_column($priceIdDbFilds, 'ID', 'PRODUCT_ID');

        $this->unpreparedResult = array_map(
            function($i) use($priceId) {
                $arDiscounts = \CCatalogDiscount::GetDiscountByPrice(
                    $priceId[$i[ProductTable::getTableName().'ID']],
                    $GLOBALS['USER']->GetUserGroupArray(),
                    "N",
                    SITE_ID
                );
                $discountsPrice = \CCatalogProduct::CountPriceWithDiscount(
                    $i['optimal_price'],
                    $i[PriceTable::getTableName().'CURRENCY'],
                    $arDiscounts,
                );

                $i['DISPLAY_PRICE'] = \CCurrencyLang::CurrencyFormat(
                    $discountsPrice ?: $i['optimal_price'],
                    $i[PriceTable::getTableName().'CURRENCY'],
                );

                if ($discountsPrice && round($discountsPrice, 2) !== round((float)$i['optimal_price'], 2)) {
                    $i['DISPLAY_PRICE_WHITHOUT_DISCOND'] = \CCurrencyLang::CurrencyFormat(
                        $i['optimal_price'],
                        $i[PriceTable::getTableName().'CURRENCY'],
                    );
                }

                return $i;
            },
            $this->unpreparedResult,
        );

        return $this;
    }

    protected function addProductTable()
    {

        if (self::filds[ProductTable::class] !== []) {
            $this
                ->registerRuntimeField(ProductTable::class, [
                    'data_type' => ProductTable::class,
                    'reference' => ['=this.ID' => 'ref.ID']
                ]);
            foreach (self::filds[ProductTable::class] as $fild) {
                $this->addSelect(ProductTable::class . '.' . $fild, ProductTable::getTableName().$fild);
            }
        }
    }

    protected function addPriceTable()
    {

        $filter = Join::on('this.ID', "ref.PRODUCT_ID");

        if (is_array($this->typePrice) && count($this->typePrice) > 0) {
            $filter->whereIn('ref.CATALOG_GROUP_ID', $this->typePrice);
        }

        $reference = new Reference(PriceTable::class, PriceTable::class, $filter);

        if (self::filds[PriceTable::class] !== []) {
            $this->registerRuntimeField($reference);

            foreach (self::filds[PriceTable::class] as $fild) {
                $this->addSelect(PriceTable::class . '.' . $fild, PriceTable::getTableName().$fild);
            }
            $minimalPrice = new ExpressionField('optimal_price', 'MIN(%s)', PriceTable::class.'.PRICE');
            $this->addSelect($minimalPrice);
        }
    }

    protected function getProperty(array &$unpreparedResult): array
    {
        $displayProps = array_merge(
            $this->properties['LIST_PAGE_SHOW'] ?? [],
            $this->properties['IN_BASKET'] ?? [],
            $this->properties['OFFER_TREE'] ?? [],
        );
        if (empty($displayProps) || $this->product_id === []) {
            return [];
        }

        return ElementPropertyTable::query()
            ->addSelect('ID')
            ->addSelect('VALUE')
            ->addSelect('IBLOCK_ELEMENT_ID')
            ->addSelect(PropertyTable::class.'.'.'ID', PropertyTable::getTableName().'ID')
            ->addSelect(PropertyTable::class.'.'.'NAME', PropertyTable::getTableName().'NAME')
            ->addSelect(PropertyTable::class.'.'.'PROPERTY_TYPE', PropertyTable::getTableName().'PROPERTY_TYPE')
            ->addSelect(PropertyTable::class.'.'.'USER_TYPE_SETTINGS_LIST', PropertyTable::getTableName().'USER_TYPE_SETTINGS_LIST')
            ->whereIn('IBLOCK_ELEMENT_ID', $this->product_id)
            ->registerRuntimeField(PropertyTable::class, [
                'data_type' => PropertyTable::class,
                'reference' => ['=this.IBLOCK_PROPERTY_ID' => 'ref.ID'],
            ])
            ->whereIn(PropertyTable::class.'.ID', $displayProps)
            ->fetchAll();
    }

    protected function getValuesPropertyFromTypeList(array $unprepared_property): array
    {
        $property_type_list = array_filter(
            $unprepared_property,
            function($i) {return $i[PropertyTable::getTableName().'PROPERTY_TYPE'] === 'L';},
        );

        $property_type_other = array_diff_key($unprepared_property, $property_type_list);

        if ($property_type_list === []) {
            return $property_type_other;
        }

        $values_from_list = array_column(
            PropertyEnumerationTable::query()
                ->addSelect('ID')
                ->addSelect('VALUE')
                ->whereIn('ID', array_column($property_type_list, 'VALUE'))
                ->fetchAll(),
            'VALUE',
            'ID',
        );

        $property_type_list = array_map(function($i) use ($values_from_list) {
            $i['VALUE'] = $values_from_list[$i['VALUE']];
            return $i;
        }, $property_type_list);

        return array_merge($property_type_list, $property_type_other);
    }

    protected function getPropFromHLBlock(array $property_all): array
    {
        $user_prop_key = PropertyTable::getTableName().'USER_TYPE_SETTINGS_LIST';

        $prop_with_HL = array_filter(
            $property_all,
            function($i) use ($user_prop_key) {return is_array($i[$user_prop_key]);},
        );

        if ($prop_with_HL === []) {
            return $property_all;
        }

        $prop_other = array_diff_key($property_all, $prop_with_HL);

        $prop_with_HL_normalize = [];
        foreach ($prop_with_HL as $i) {
            $prop_with_HL_normalize[$i[$user_prop_key]['TABLE_NAME']][$i['ID']] = $i['VALUE'];
        }

        $usedHL = HL::query()
            ->addSelect('*')
            ->whereIn('TABLE_NAME', array_keys($prop_with_HL_normalize))
            ->fetchAll();
        $HLEntityS = [];
        foreach ($usedHL as $i) {
            $HLEntityS[$i['TABLE_NAME']] = HL::compileEntity($i);
        }

        $HL_Data = [];

        foreach ($HLEntityS as $key => $HLEntity) {
            $HL_Data[$key] = (new Query($HLEntity))
                ->addSelect('UF_NAME')
                ->addSelect('UF_FILE')
                ->addSelect('UF_XML_ID')
                ->whereIn('UF_XML_ID', array_values($prop_with_HL_normalize[$key]))
                ->registerRuntimeField(MainFileTable::class, [
                    'data_type' => MainFileTable::class,
                    'reference' => ['=this.UF_FILE' => 'ref.ID']
                ])
                ->addSelect(MainFileTable::class.'.FILE_NAME', MainFileTable::getTableName().'FILE_NAME')
                ->addSelect(MainFileTable::class.'.SUBDIR', MainFileTable::getTableName().'SUBDIR')
                ->fetchAll();
        }

        $result = array_map(function ($i) use ($HL_Data) {

            $HL_TABLE_NAME = $i[PropertyTable::getTableName().'USER_TYPE_SETTINGS_LIST']['TABLE_NAME'];
            $fild_file = MainFileTable::getTableName().'FILE_NAME';
            $subdir_file = MainFileTable::getTableName().'SUBDIR';

            $hl_elem = array_values(
                array_filter(
                    $HL_Data[$HL_TABLE_NAME] ?: [],
                    function($j) use($i) {return $j['UF_XML_ID'] === $i['VALUE'];}
            ))[0];

            $i['user_prop_type'] = !!$hl_elem['UF_FILE'] ? 'img' : 'string';
            $i['user_prop'] = !!$hl_elem['UF_FILE']
                ? "/upload/{$hl_elem[$subdir_file]}/{$hl_elem[$fild_file]}"
                : $hl_elem['UF_NAME'];

            return $i;
        }, $prop_with_HL);

        return array_merge($prop_other, $result);
    }
}
