<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main,
    Sotbit\B2BCabinet\OrderTemplate\OrderTemplate,
    Sotbit\B2BCabinet\Internals\OrderTemplateTable,
    Sotbit\B2BCabinet\Internals\OrderTemplateProductTable,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class OrderTemplatesList extends CBitrixComponent implements Controllerable
{
    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'OrderTemplates' => [
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
        $this->errorCollection = new Main\ErrorCollection();

        $params["PATH_TO_DETAIL"] = trim($params["PATH_TO_DETAIL"]);

        if ($params["PATH_TO_DETAIL"] == '')
        {
            $params["PATH_TO_DETAIL"] = htmlspecialcharsbx(Main\Context::getCurrent()->getRequest()->getRequestedPage()."?ID=#ID#");
        }

        $params["PER_PAGE"] = ((int)($params["PER_PAGE"]) <= 0 ? 20 : (int)($params["PER_PAGE"]));

        return $params;
    }

    function executeComponent()
    {

        global $USER, ${$this->arParams['FILTER_NAME']};
        $USER_ID = $USER->GetID();
        $filter = ["USER_ID" => $USER_ID];
        if(!empty(${$this->arParams['FILTER_NAME']})){
            $filter = array_merge($filter, ${$this->arParams['FILTER_NAME']});
        }

        $orderTemplate = new OrderTemplate(SITE_ID);

        if(Loader::includeModule("sotbit.auth") && $orderTemplate->getVersionCompanies() == "Y" && isset($_SESSION["AUTH_COMPANY_CURRENT_ID"])){
            $companies = $orderTemplate->getCompanies(["COMPANY_ID" => $_SESSION["AUTH_COMPANY_CURRENT_ID"]]);
            $object = new \Sotbit\Auth\Company\Company(SITE_ID);
            if($object->isUserAdmin($USER_ID, $_SESSION["AUTH_COMPANY_CURRENT_ID"])){
                $this->arResult["IS_ADMIN"] = "Y";
            }
            if($companies){
                unset($filter["USER_ID"]);
                $filter[] = ["LOGIC" => "OR", ["ID" => array_keys($companies)], ["USER_ID" => $USER_ID]];
            }
        }

        $templateList = OrderTemplateTable::getList([
            'filter' => $filter,
            'select' => ['*', "USER_LOGIN"=>'USER.LOGIN', "USER_NAME"=>'USER.NAME', "USER_LAST_NAME"=>'USER.LAST_NAME'],
            'order' => [$this->arParams["SORT_BY"]=>$this->arParams["SORT_ORDER"]],
        ])->fetchAll();

        $this->arResult['TOTAL_ROWS_COUNT'] = count($templateList);

        $rs = new CDBResult;
        $rs->InitFromArray($templateList);
        $rs->NavStart($this->arParams["PER_PAGE"]);
        $this->arResult["NAV_STRING"] = $rs->GetPageNavString(GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATE_PAGE_NAV_STRING"));
        $this->arResult["PROFILES"] = Array();
        while($template = $rs->GetNext()) {
            $templateId[] = $template["ID"];
            $this->arResult["TEMPLATES"][] = $template;
        }
        $dbProductList = OrderTemplateProductTable::getList([
            'filter' => ['ORDER_TEMPLATE_ID'=>$templateId],
            'select' => ['PRODUCT_ID', 'QUANTITY', 'ORDER_TEMPLATE_ID']
        ]);

        while($product = $dbProductList->fetch()){
            $productList[$product["ORDER_TEMPLATE_ID"]][]=$product["PRODUCT_ID"];
            $this->arResult["QUANTITY"][$product["ORDER_TEMPLATE_ID"]][$product["PRODUCT_ID"]] = $product["QUANTITY"];
        }

        $this->arResult["PRODUCTS"] = $productList;

        $this->includeComponentTemplate();
    }

}