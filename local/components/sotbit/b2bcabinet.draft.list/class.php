<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Sotbit\B2BCabinet\Internals\DraftTable;
use Sotbit\B2BCabinet\Internals\DraftProductTable;
use Sotbit\B2BCabinet\Draft\Draft;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class DraftList extends CBitrixComponent implements Controllerable
{
    protected $items = [];
    protected $draftProducts = [];

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'DraftList' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ]
        ];
    }


    public function onPrepareComponentParams($params)
    {
        $params["COUNT_DRAFT_PAGE"] = ((int)($params["COUNT_DRAFT_PAGE"]) <= 0 ? 10 : (int)($params["COUNT_DRAFT_PAGE"]));
        return $params;
    }

    public function executeComponent()
    {
        global $USER;
        global ${$this->arParams['FILTER_NAME']};

        $filterDraft = ["USER_ID"=>$USER->GetID(), "SITE_ID"=>SITE_ID];
        if(${$this->arParams['FILTER_NAME']}){
            $filterDraft = array_merge($filterDraft, ${$this->arParams['FILTER_NAME']});
        }

        $orderDraft = [$this->arParams["SORT_BY"] => $this->arParams["SORT_ORDER"]];

        $draft = new Draft(SITE_ID);
        $draftList = $draft->getDrafts($filterDraft, [], $orderDraft);


        $rs = new CDBResult;
        $rs->InitFromArray($draftList);
        $rs->NavStart($this->arParams["COUNT_DRAFT_PAGE"]);
        $this->arResult["NAV_STRING"] = $rs->GetPageNavString(GetMessage("SOTBIT_B2BCABINET_DRAFT_LIST_PAGE_NAV_STRING"));
        $this->arResult["ITEMS_COUNT"] = count($draftList);
        $this->arResult["ITEMS"] = Array();
        while($result = $rs->GetNext())
        {
            $this->items[$result["ID"]] = $result;
        }
        $this->getProducts();
        $this->getTotalPrice();
        $this->prepareResult();
        $this->includeComponentTemplate();
    }

    public function prepareResult()
    {
        $this->arResult["ITEMS"] = array_values($this->items);
    }
    
    public function getProducts()
    {
        if(!$this->items){
            return;
        }
        $dbResult = DraftProductTable::getList([
            'filter' => ['DRAFT_ID' => array_keys($this->items)]
        ]);

        while($result = $dbResult->fetch()){
           $this->draftProducts[$result["DRAFT_ID"]][] = $result;
        }
    }

    public function getTotalPrice()
    {
        if(!$this->draftProducts){
            return;
        }

        global $USER;
        foreach ($this->draftProducts as $draftId => $products){
            $totalSum = 0;
            foreach ($products as $product) {
                $quantity = $product["QUANTITY"];
                $arPrice = CCatalogProduct::GetOptimalPrice($product["PRODUCT_ID"], $quantity,
                    $USER->GetUserGroupArray());
                if (!$arPrice || count($arPrice) <= 0) {
                    if ($nearestQuantity = CCatalogProduct::GetNearestQuantityPrice($product["PRODUCT_ID"],
                        $product["QUANTITY"], $USER->GetUserGroupArray())) {
                        $quantity = $nearestQuantity;
                        $arPrice = CCatalogProduct::GetOptimalPrice($product["PRODUCT_ID"], $quantity,
                            $USER->GetUserGroupArray());
                    }
                }

                $totalSum += $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"] * $quantity;
                $currency = $arPrice["PRICE"]["CURRENCY"];
            }

            $this->items[$draftId]["TOTAL_PRICE"] = CurrencyFormat($totalSum, $currency);
        }
    }

    public function removeDraftAction($draftId)
    {
        if(is_numeric($draftId)){
            $draft = new Draft(SITE_ID);
            if($draft->removeDraft($draftId)){
                return [
                    'error' => false,
                ];
            }
        }
    }

    public function createOrderAction($draftId)
    {
        if(is_numeric($draftId)){
            $draft = new Draft(SITE_ID);
            return $draft->formBasket($draftId);
        }
        else{
            return false;
        }
    }

    public function createOrdertemplateAction($draftId)
    {
        if(!$draftId || !is_numeric($draftId)){
            return false;
        }

        global $USER;

        $dbProducts = DraftProductTable::getList([
            'filter' => ['DRAFT_ID' => $draftId],
            'select' => [ 'QUANTITY', 'PRODUCT_ID', 'NAME' => 'DRAFT.NAME']
        ]);

        while($result = $dbProducts->fetch()){
            $draftName = $result["NAME"];
            $products[] = ['ID' => $result["PRODUCT_ID"], 'QUANTITY' => $result["QUANTITY"]];
        }

        if(isset($products)){
            $fields = [
                "NAME" => $draftName,
                "USER_ID" => $USER->getId(),
                "SITE_ID" => SITE_ID,
                "SAVED" => 'N',
            ];

            $orderTemplate = new \Sotbit\B2BCabinet\OrderTemplate\OrderTemplate(SITE_ID);
            $id = $orderTemplate->add($fields, $products);
            $this->removeDraftAction($draftId);
            return $id;
        }

        return false;
    }
}