<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


require __DIR__ . '/autoload/autoload.php';

use \Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Context;
use Bitrix\Main\Config\Option;
use Bitrix\Iblock\SectionElementTable;
use Sotbit\UpselingComponent\UpsellingModelFactory;
use Bitrix\Main\Loader;
use Sotbit\Regions\Internals\OptionsTable;

class BasketUpselling extends \CBitrixComponent implements Controllerable
{

    public function configureActions(): array
    {
        if (!CModule::IncludeModule('iblock')) {
            throw new \Exception('Module missing iblock');
        }
        if (!CModule::IncludeModule('catalog')) {
            throw new \Exception('Module missing catalog');
        }

        return [];
    }

    public function onPrepareComponentParams($arParams): array
    {
        if ($arParams) {
            $context = \Bitrix\Main\Application::getInstance()->getContext();
            if (Loader::includeModule("sotbit.regions") && isset($_SESSION["SOTBIT_REGIONS"]) && isset($_SESSION["SOTBIT_REGIONS"]["PRICE_CODE"])) {
                $valEnableRegions = OptionsTable::GetList(["select" => ["VALUE"], "filter" => ["SITE_ID" => $context->getsite(), "CODE" => "ENABLE"]])->Fetch();
                if ($valEnableRegions['VALUE'] == "Y") {
                    $arParams["TYPE_PRICE"] = array_column(\Bitrix\Catalog\GroupTable::query()
                        ->addSelect('ID')
                        ->whereIn('XML_ID', $_SESSION["SOTBIT_REGIONS"]["PRICE_CODE"])
                        ->fetchAll(), 'ID');
                }
            }
            $accessPrice = array_column(\Bitrix\Catalog\GroupAccessTable::getList([
                'select' => ['CATALOG_GROUP_ID'],
                'filter' => [
                    '@GROUP_ID' => \Bitrix\Main\UserTable::getUserGroupIds($GLOBALS["USER"]->GetID()),
                    '=ACCESS' => \Bitrix\Catalog\GroupAccessTable::ACCESS_BUY
                ]
            ])->fetchAll(), 'CATALOG_GROUP_ID');
            $arParams["TYPE_PRICE"] = array_uintersect($arParams["TYPE_PRICE"], $accessPrice, "strcasecmp");

            $arParams['IN_BASKET'] = $this->getBasketProduct();

            return $arParams;
        } else {
            $values = $this->request->getValues();
            return $values['arParams'];
        }
    }

    public function getProdeuctsAction($sections = [], $productName = '', int $CURRENT_PAGE): array
    {
        return $this->getProdeucts($sections, $productName, $CURRENT_PAGE);
    }

    public function executeComponent()
    {
        $this->arResult['CURRENT_PAGE'] = 1;

        $this->arResult['CROSSSELL'] = $this->getCrosssell();

        $this->arParams['SECTIONS'] = $this->getSections();

        $this->arParams['SITE_ROOT'] = $this->getSiteRoot();

        try {
            $page_params = $this->getProdeucts([], '', $this->arResult['CURRENT_PAGE']);
        } catch (\Throwable $e) {
            echo 'I have a small problem <br>';
            global $USER;
            if ($USER->IsAdmin()) {
                print_r(sprintf('my problem: %s', $e));
            }
        }
        $this->arResult['PORODUCTS'] = $page_params['PORODUCTS'];
        $this->arResult['NUMBER_OF_PAGES'] = $page_params['NUMBER_OF_PAGES'];

        $this->includeComponentTemplate();
    }

    protected function getAccessibleProducts()
    {
        return array_keys(CIBlockElementRights::GetUserOperations(
            array_column(UpsellingModelFactory::getModel(
                $this->arParams['IBLOCK_ID'],
                [],
                [],
            )->fetchAll(), 'ID'),
            $GLOBALS["USER"]->GetId()
        ));
    }

    protected function getProdeucts($sections = [], $productName = '', int $CURRENT_PAGE): array
    {
        $child_section = $this->getChildSections($sections);

        $pagination = $this->getPagination($CURRENT_PAGE, $child_section, $productName);

        $resIblock = CIBlock::GetByID($this->arParams['IBLOCK_ID'])->fetch();
        if ($resIblock['RIGHTS_MODE'] == "S") {
            $PORODUCTS_preparation = UpsellingModelFactory::getModel(
                $this->arParams['IBLOCK_ID'],
                [
                    'LIST_PAGE_SHOW' => is_array($this->arParams['ARTICLE']) ? $this->arParams['ARTICLE'] : [],
                    'IN_BASKET' => is_array($this->arParams['IN_BASKET']) ? $this->arParams['IN_BASKET'] : [],
                    'OFFER_TREE' => is_array($this->arParams['OFFER_TREE']) ? $this->arParams['OFFER_TREE'] : [],
                ],
                $this->arParams['TYPE_PRICE'],
            )
                ->setLimit($pagination['limit'])
                ->setOffset($pagination['offset']);
        } else {
            $PORODUCTS_preparation = UpsellingModelFactory::getModel(
                $this->arParams['IBLOCK_ID'],
                [
                    'LIST_PAGE_SHOW' => is_array($this->arParams['ARTICLE']) ? $this->arParams['ARTICLE'] : [],
                    'IN_BASKET' => is_array($this->arParams['IN_BASKET']) ? $this->arParams['IN_BASKET'] : [],
                    'OFFER_TREE' => is_array($this->arParams['OFFER_TREE']) ? $this->arParams['OFFER_TREE'] : [],
                ],
                $this->arParams['TYPE_PRICE'],
            )
                ->whereIn('ID', $this->getAccessibleProducts() ?: [0])
                ->setLimit($pagination['limit'])
                ->setOffset($pagination['offset']);
        }

        if ($productName !== '') {
            $PORODUCTS_preparation->addFilter('%SEARCHABLE_CONTENT', $productName);
        }

        //TODO
        if (!isset($this->arResult['CROSSSELL']) && empty($this->arResult['CROSSSELL'])) {
            $this->arResult['CROSSSELL'] = $this->getCrosssell();
        }

        if (is_array($this->arResult['CROSSSELL']['CROSSSELL_ARRAY'])) {
            $productIds = [];
            foreach ($this->arResult['CROSSSELL']['CROSSSELL_ARRAY'] as $id => $crosssell) {
                foreach ($crosssell['FILTER'] as $id => $filter) {
                    $productIds = array_merge($productIds, $filter);
                }
            }
            $PORODUCTS_preparation->addFilter($id, $productIds);
        } elseif (is_array($this->arResult['CROSSSELL']['FILTER'][0][2])) {
            foreach ($this->arResult['CROSSSELL']['FILTER'][0][2] as $id => $filter) {
                $PORODUCTS_preparation->addFilter($id, $filter);
            }
        }

        $this->conditionAvalibe($PORODUCTS_preparation);
        $this->addOrder($PORODUCTS_preparation, true);
        $this->addOrder($PORODUCTS_preparation, false);
        $this->setSections($PORODUCTS_preparation, $child_section);

        $PORODUCTS = $PORODUCTS_preparation
            ->fetchAndPostProcessing(
                $this->arParams['SEF_MODE'],
                '',
                $this->arParams['VARIABLE_ALIASES'],
            )
            ->preparetionForSend($this->arParams['PRIVATE_PRICE'] === 'Y');

        $NUMBER_OF_PAGES = $pagination['number_of_pages'];

        return compact('PORODUCTS', 'NUMBER_OF_PAGES');
    }

    protected function getPagination(int $currentPage, array $secions = [], $productName = ''): array
    {
        $prepare_element_count = UpsellingModelFactory::getModel(
            $this->arParams['IBLOCK_ID'],
            [],
            $this->arParams['TYPE_PRICE']
        );

        if ($productName !== '') {
            $prepare_element_count->addFilter('%NAME', $productName);
        }

        //TODO
        if (!isset($this->arResult['CROSSSELL']) && empty($this->arResult['CROSSSELL'])) {
            $this->arResult['CROSSSELL'] = $this->getCrosssell();
        }

        if (is_array($this->arResult['CROSSSELL']['CROSSSELL_ARRAY'])) {
            $productIds = [];
            foreach ($this->arResult['CROSSSELL']['CROSSSELL_ARRAY'] as $id => $crosssell) {
                foreach ($crosssell['FILTER'] as $id => $filter) {
                    $productIds = array_merge($productIds, $filter);
                }
            }
            $prepare_element_count->addFilter($id, $productIds);
        } elseif (is_array($this->arResult['CROSSSELL']['FILTER'][0][2])) {
            foreach ($this->arResult['CROSSSELL']['FILTER'][0][2] as $id => $filter) {
                $prepare_element_count->addFilter($id, $filter);
            }
        }

        $this->conditionAvalibe($prepare_element_count);
        $this->setSections($prepare_element_count, $secions);
        $element_count = $prepare_element_count->queryCountTotal();
        $limit = $this->arParams['PAGE_ELEMENT_COUNT'];
        $offset = $limit * ($currentPage - 1);
        $number_of_pages = ceil($element_count / $limit);
        return compact('limit', 'offset', 'number_of_pages');
    }

    protected function conditionAvalibe(Query &$preparation): void
    {
        if ($preparation->getOrder() !== []) {
            throw new LogicException(
                'dont call $this->addOrder method before the call $ this->conditionAvalibe'
            );
        }
        switch ($this->arParams["HIDE_NOT_AVAILABLE"]) {
            case 'Y':
                $preparation->where(ProductTable::getTableName() . 'AVAILABLE', '=', 'Y');
                return;

            case 'N':
                return;

            case 'L':
                $preparation->addOrder(ProductTable::getTableName() . 'AVAILABLE', 'DESC');
                return;
        }
    }

    protected function getCrosssell(): array
    {
        if (
            !Loader::includeModule('sotbit.crosssell') || 
            empty($this->arParams['CROSSSELL_STATUS']) || 
            $this->arParams['CROSSSELL_STATUS'] === 'N' || 
            Option::get("sotbit.crosssell", 'sotbit.crosssell_INC_MODULE', '', SITE_ID) !== 'Y'
        ) {
            return [];
        }
        global $APPLICATION;

        if (!empty($this->arParams['IN_BASKET'])) {
            $arProducts = CCatalogSKU::getProductList($this->arParams['IN_BASKET']);
            foreach ($arProducts as $product) {
                $this->arParams['IN_BASKET'][] = $product['ID'];
            }
        }

        $result = $APPLICATION->IncludeComponent(
            'sotbit:crosssell.crosssell.list',
            '', 
            [
                "CACHE_TIME" => $this->arParams['CACHE_TIME'],
                "CACHE_TYPE" => $this->arParams['CACHE_TYPE'],
                "IBLOCK_ID" => $this->arParams['IBLOCK_ID'],
                "SECTION_MODE" => $this->arParams['SECTION_MODE'],
                "CROSSSELL_LIST" => $this->arParams['CROSSSELL_LIST'],
                "PRODUCT_ID" => is_array($this->arParams['IN_BASKET']) ? $this->arParams['IN_BASKET'] : [],
                "INTERRUPT_MODE" => "N",
                "USE_TEMPLATE" => "N",
            ], 
            false
        );

        return $result;
    }

    protected function getSections(): array
    {
        $result = [];
        $sectionFilter = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'CHECK_PERMISSIONS' => 'Y',
            'PERMISSIONS_BY' => $GLOBALS["USER"]->GetID()
        ];

        if ($this->arResult['CROSSSELL']['SAFE']) {
            if (is_array($this->arResult['CROSSSELL']['CROSSSELL_ARRAY'])) {
                foreach ($this->arResult['CROSSSELL']['CROSSSELL_ARRAY'] as $id => $crosssell) {
                    foreach ($crosssell['FILTER'] as $id => $filter) {
                        $obSections = CIBlockElement::GetElementGroups($filter, true, ['ID', 'NAME']);
                        while ($arSection = $obSections->fetch()) {
                            if(!in_array($arSection, $result)) {
                                $result[] = $arSection;
                            }
                        }
                    }
                }
            } elseif (is_array($this->arResult['CROSSSELL']['FILTER'])) {
                $obSections = CIBlockElement::GetElementGroups($this->arResult['CROSSSELL']['FILTER'][0][2]['ID'], true, ['ID', 'NAME']);
                while ($arSection = $obSections->fetch()) {
                    if(!in_array($arSection, $result)) {
                        $result[] = $arSection;
                    }
                }
            }

            return $result;
        }

        if ($this->arParams["HIDE_NOT_AVAILABLE"] !== 'Y') {
            $dbResult = \CIBlockSection::GetList(
                [],
                $sectionFilter,
                false,
                ['ID', 'NAME']
            );

            while ($arRes = $dbResult->fetch()) {
                $result[] = $arRes;
            }
            return $result;
        }

        $sectionsIds = ProductTable::query()
            ->addSelect('forSections.IBLOCK_SECTION_ID', 'SECTION_ID')
            ->where('AVAILABLE', 'Y')
            ->registerRuntimeField('forSections', [
                'data_type' => SectionElementTable::class,
                'reference' => ['this.ID' => 'ref.IBLOCK_ELEMENT_ID']
            ])
            ->fetchAll();

        $childSectionsIds = array_filter(array_unique(array_column($sectionsIds, 'SECTION_ID')));
        $allSectionId = $childSectionsIds;

        while (true) {
            $parents = SectionTable::query()
                ->setSelect(['IBLOCK_SECTION_ID'])
                ->where('IBLOCK_ID', $this->arParams['IBLOCK_ID'])
                ->whereIn('ID', $childSectionsIds)
                ->fetchAll();

            $parentsId = array_filter(array_unique(array_column($parents, 'IBLOCK_SECTION_ID')));

            if (count($parentsId) === 0) {
                break;
            }

            $allSectionId = array_merge($allSectionId, $parentsId);
            $childSectionsIds = $parentsId;
        }

        $sectionFilter['ID'] = $allSectionId;
        $dbResult = \CIBlockSection::GetList(
            [],
            $sectionFilter,
            false,
            ['ID', 'NAME']
        );

        while ($arRes = $dbResult->fetch()) {
            $result[] = $arRes;
        }

        return $result;
    }

    protected function getChildSections(array $secions): array
    {
        if ($secions === []) {
            return [];
        }

        $parent_sections = SectionTable::query()
            ->addFilter('=IBLOCK_ID', $this->arParams['IBLOCK_ID'])
            ->addFilter('=ID', array_merge(['OR'], $secions))
            ->addSelect('LEFT_MARGIN')
            ->addSelect('RIGHT_MARGIN')
            ->exec()
            ->fetchAll();

        $parent_sections_margin = array_map(function ($i) {
            return [
                ['LEFT_MARGIN', '>=', $i['LEFT_MARGIN']],
                ['RIGHT_MARGIN', '<=', $i['RIGHT_MARGIN']],
            ];
        },
            $parent_sections,
        );

        $for_query = Query::filter()
            ->logic('or');
        foreach ($parent_sections_margin as $item_margin) {
            $for_query->where(Query::filter()
                ->where($item_margin)
            );
        }

        $child_sections = array_map(
            function ($i) {
                return $i['ID'];
            },
            SectionTable::query()
                ->addSelect('ID')
                ->addFilter('=IBLOCK_ID', $this->arParams['IBLOCK_ID'])
                ->where($for_query)
                ->exec()
                ->fetchAll()
        );
        return $child_sections;
    }

    protected function setSections(Query &$preparation, $secions = []): void
    {
        if ($secions !== []) {
            $preparation->addFilter('=IBLOCK_SECTION_ID', array_merge(['OR'], $secions));
            return;
        }

        switch ($this->arParams["SHOW_ALL_WO_SECTION"]) {
            case 'Y':
                return;

            case 'N':
                $preparation->addFilter('=IBLOCK_SECTION_ID', ['OR']);
                return;
        }

    }

    protected function addOrder(Query &$preparation, bool $isFirst): void
    {
        $key = $isFirst ? 'ELEMENT_SORT_FIELD' : 'ELEMENT_SORT_FIELD2';
        $sort = $isFirst ? 'ELEMENT_SORT_ORDER' : 'ELEMENT_SORT_ORDER2';
        if ($this->arParams[$key] === '') {
            return;
        }
        $sortFild = [
            'shows' => 'SHOW_COUNTER',
            'sort' => 'SORT',
            'timestamp_x' => 'TIMESTAMP_X',
            'id' => 'ID',
            'active_from' => 'ACTIVE_FROM',
            'active_to' => 'ACTIVE_TO',
            'by_price' => 'optimal_price',
            'name' => 'NAME',
        ];
        $preparation->addOrder(
            $sortFild[$this->arParams[$key]],
            $this->arParams[$sort],
        );
    }

    protected function getSiteRoot(): string
    {
        $path = Context::getCurrent()->getRequest()->getRequestUri();
        $matches = null;
        preg_match('/^\/b2bcabinet\//', $path, $matches);
        $result = count($matches) > 0 ? '/b2bcabinet' : '/';
        return $result;
    }

    protected function getBasketProduct(): array
    {
        $result = [];
        $basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        foreach ($basket as $basketItem) {
            $result[] = $basketItem->getField("PRODUCT_ID");
        }

        return $result;
    }
}