<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Sotbit\B2BCabinet\OrderTemplate\OrderTemplate;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Sotbit\B2BCabinet\Internals\OrderTemplateProductTable;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class OrderTemplatesDetail extends CBitrixComponent implements Controllerable
{
    protected $errorCollection;

    protected $idTemplate;
    protected $templateData;
    protected $company = [];

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'delete' => [
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
        global $APPLICATION;

        $this->errorCollection = new Main\ErrorCollection();

        $this->idTemplate = 0;

        if (isset($params['ID']) && $params['ID'] > 0)
        {
            $this->idTemplate = (int)$params['ID'];
        }
        else
        {
            $request = Main\Application::getInstance()->getContext()->getRequest();
            $this->idTemplate = (int)($request->get('ID'));
        }

        if (isset($params['PATH_TO_LIST']))
        {
            $params['PATH_TO_LIST'] = trim($params['PATH_TO_LIST']);
        }
        elseif ($this->idTemplate)
        {
            $params['PATH_TO_LIST'] = htmlspecialcharsbx($APPLICATION->GetCurPage());
        }
        else
        {
            return false;
        }

        if ($params["PATH_TO_DETAIL"] !== '')
        {
            $params["PATH_TO_DETAIL"] = trim($params["PATH_TO_DETAIL"]);
        }
        else
        {
            $params["PATH_TO_DETAIL"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?ID=#ID#");
        }

        return $params;
    }

    function executeComponent()
    {
        global $USER, $APPLICATION, ${$this->arParams['FILTER_NAME']};

        $request = Main\Application::getInstance()->getContext()->getRequest();

        if ($this->idTemplate <= 0 || $request->get('reset'))
        {
            if (!empty($this->arParams["PATH_TO_LIST"]))
            {
                LocalRedirect($this->arParams["PATH_TO_LIST"]);
            }
            else
            {
                $this->errorCollection->setError(new Main\Error(Loc::getMessage("B2B_CABINET_NO_ORDERTEMPLATE")));
            }
        }

        if($_SERVER["REQUEST_METHOD"] == "POST" && $request->get("action") == "save"){
            $this->saveTemplate();
        }


        $template = new OrderTemplate(SITE_ID);
        $orderTemplate = $template->getOrderTemplateByID($this->idTemplate);

        if(($this->arResult["EXTENDED_VERSION_COMPANIES"] = $template->getVersionCompanies()) == "Y"){
            $this->company = $template->getCompanies(["ORDER_TEMPLATE_ID" => $this->idTemplate]);
        }

        if(!$orderTemplate
            || ($orderTemplate["SAVED"]=="N" && $orderTemplate["USER_ID"]!=$USER->GetID())
            || $orderTemplate["SITE_ID"] != SITE_ID
        )
        {
            $this->errorCollection->setError(new Main\Error(Loc::getMessage("B2B_CABINET_NO_ORDERTEMPLATE")));
        }
        elseif ($this->company && !in_array($_SESSION["AUTH_COMPANY_CURRENT_ID"], $this->company[$this->idTemplate]) && $orderTemplate["USER_ID"]!=$USER->GetID()){
            $this->errorCollection->setError(new Main\Error(Loc::getMessage("B2B_CABINET_ACCESS_DENIED")));
        }
        elseif ($request->get("edit") == 'Y' &&  $orderTemplate["USER_ID"]!=$USER->GetID()){
            $this->errorCollection->setError(new Main\Error(Loc::getMessage("B2B_CABINET_EDIT_ACCESS_DENIED")));
        }
        else{

            $dbProducts = OrderTemplateProductTable::getList([
                'filter' => ["ORDER_TEMPLATE_ID"=>$this->idTemplate],
                'select' => ["PRODUCT_ID", "QUANTITY"]
            ]);
            while ($resultProduct = $dbProducts->fetch()){
                $productsId[] = $resultProduct["PRODUCT_ID"];
                $productsList[$resultProduct["PRODUCT_ID"]] = $resultProduct;
            }

            if($productsList){
                $this->arResult["PRODUCTS_ID"] = $productsId;
                $arSelect = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "PREVIEW_PICTURE", "ACTIVE", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID", "TYPE", "CATALOG_QUANTITY"];
                $arFilter = Array("ID"=>$productsId);

                if(${$this->arParams['FILTER_NAME']}){
                    $arFilter = array_merge($arFilter, ${$this->arParams['FILTER_NAME']});
                }
                $res = CIBlockElement::GetList(["ACTIVE"=>'desc'], $arFilter, false, false, $arSelect);
                while($ob = $res->GetNext()) {
                    if($productsList[$ob["ID"]]){
                        $productsList[$ob["ID"]] += $ob;
 
                        $imgId = false;
                        if($ob["PREVIEW_PICTURE"]){
                            $imgId = $ob["PREVIEW_PICTURE"];
                        }
                        elseif ($ob["DETAIL_PICTURE"]){
                            $imgId = $ob["PREVIEW_PICTURE"];
                        }
                        if($imgId){
                            $productsList[$ob["ID"]]["PICTURE"] = CFile::GetFileArray($imgId);
                        }
                        $available = CCatalogProduct::GetList([],["ID"=>$ob["ID"]],false,false,["AVAILABLE"])->fetch();
                        if($available["AVAILABLE"]=="Y"){
                            CCatalogProduct::setUsedCurrency(CSaleLang::GetLangCurrency(SITE_ID));
                            $arPrice = CCatalogProduct::GetOptimalPrice($ob["ID"], $productsList[$ob["ID"]]["QUANTITY"], $USER->GetUserGroupArray());
                            $arPrice ? $productsList[$ob["ID"]]["PRICE"] = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"] * $productsList[$ob["ID"]]["QUANTITY"]: $productsList[$ob["ID"]]["PRICE"] = false;
                            if ($productsList[$ob["ID"]]['QUANTITY'] > $productsList[$ob["ID"]]['CATALOG_QUANTITY'])
                                $arPrice ? $productsList[$ob["ID"]]["PRICE_AVAILABLE"] = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"] * $productsList[$ob["ID"]]["CATALOG_QUANTITY"]: $productsList[$ob["ID"]]["PRICE"] = false;
                            $arPrice["PRICE"]["CURRENCY"] ?  $productsList[$ob["ID"]]["CURRENCY"] = $arPrice["PRICE"]["CURRENCY"] :  $productsList[$ob["ID"]]["CURRENCY"] = false;
                        }
                        else{
                            $productsList[$ob["ID"]]["PRICE"] = false;
                        }
                    }

                }
                $this->arResult["ORDER_TEMPLATE"] = $orderTemplate;
                $this->arResult["PRODUCTS"] = $productsList;
                if(Loader::includeModule("sotbit.auth") && $this->arResult["EXTENDED_VERSION_COMPANIES"] == "Y"){
                    $company = new \Sotbit\Auth\Company\Company(SITE_ID);
                    $this->arResult["USER_COMPANY"] = $company->getCompaniesByUserID($USER->GetID(), ["COMPANY_NAME"=>"asc"]);
                    $this->arResult["COMPANIES"] = $this->company;
                }

                if($orderTemplate["SAVED"] == "N" || $request->get("edit") == "Y"){
                    $this->arResult["EDIT"] = "Y";
                }
                unset($orderTemplate);
                unset($productsList);
            }
            else{
                $this->errorCollection->setError(new Main\Error(Loc::getMessage("B2B_CABINET_NO_ORDERTEMPLATE")));
            }
        }


        $this->setTitle();
        $this->addChainItem();

        $this->formatResultErrors();
        $this->includeComponentTemplate();
    }

    public function setTitle()
    {
        global $APPLICATION;
        if ($this->arParams["SET_TITLE"] === 'Y' && $this->errorCollection->isEmpty())
        {
            $APPLICATION->SetTitle($this->arResult["ORDER_TEMPLATE"]["NAME"]);
        }
    }

    public function addChainItem()
    {
        global $APPLICATION;
        if($this->arParams["ADD_CHAIN"] === 'Y' && $this->errorCollection->isEmpty()){
            $APPLICATION->AddChainItem($this->arResult["ORDER_TEMPLATE"]["NAME"], "", true);
        }
    }

    protected function formatResultErrors()
    {
        if (!$this->errorCollection->isEmpty())
        {
            /** @var Main\Error $error */
            foreach ($this->errorCollection->toArray() as $error)
            {
                $this->arResult['ERROR_MESSAGE'] .= $error->getMessage();

                if ($error->getCode())
                    $this->arResult['ERRORS'][$error->getCode()] = $error->getMessage();
                else
                    $this->arResult['ERRORS'][] = $error->getMessage();
            }
        }
    }

    public function deleteTemplateAction ($templateId)
    {
        global $USER;
        $template = new OrderTemplate(SITE_ID);
        if($template->checkRight($templateId, $USER->GetID())){
            return $template->delete($templateId);
        }
        elseif(isset($_SESSION["AUTH_COMPANY_CURRENT_ID"]) && $template->checkBinding($templateId, $_SESSION["AUTH_COMPANY_CURRENT_ID"])){
            return $template->deleteCompany($templateId, $_SESSION["AUTH_COMPANY_CURRENT_ID"]);
        }
    }

    public function saveTemplate ()
    {
        $template = new OrderTemplate(SITE_ID);
        $request = Main\Application::getInstance()->getContext()->getRequest();
        if($request->get("TEMPLATE_NAME")){
            $fields["NAME"] = $request->get("TEMPLATE_NAME");
        }
        $companies = [];
        if($request->get("COMPANY")){
            $companies = $request->get("COMPANY");
        }

        return $template->save($this->idTemplate, $fields, $companies);
    }

    public function addToBasketAction ($templateId)
    {
        $template = new OrderTemplate(SITE_ID);
        return $template->addToBasket($templateId);
    }

}