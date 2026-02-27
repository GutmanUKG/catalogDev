<?php

namespace Sotbit\B2BCabinet\OrderTemplate;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Sotbit\B2BCabinet\Internals\OrderTemplateTable;
use Sotbit\B2BCabinet\Internals\OrderTemplateCompanyTable;
use Sotbit\B2BCabinet\Internals\OrderTemplateProductTable;

class OrderTemplate extends \SotbitB2bCabinet
{
    protected $idSite;
    protected $EXTENDED_VERSION_COMPANIES;

    public function __construct($site = '')
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        if ($site) {
            $this->idSite = $site;
        } else {
            $this->idSite = $context->getSite();
        }
        $this->EXTENDED_VERSION_COMPANIES = Option::get('sotbit.auth', 'EXTENDED_VERSION_COMPANIES', 'N');
        unset($context);
    }

    public function getVersionCompanies()
    {
        return $this->EXTENDED_VERSION_COMPANIES;
    }

    public function add($fields, $products, $companies = [])
    {
        if(is_array($fields) && !empty($fields) && !empty($products)){
            $result = OrderTemplateTable::add([
                "NAME" => $fields["NAME"],
                "USER_ID" => $fields["USER_ID"],
                "SITE_ID" => $fields["SITE_ID"],
                "SAVED" => $fields["SAVED"],
            ]);

            if ($result->isSuccess())
            {
                $idTemplate = $result->getId();
                foreach ($products as $product){
                    OrderTemplateProductTable::add([
                        "ORDER_TEMPLATE_ID" => $idTemplate,
                        "PRODUCT_ID" => $product["ID"],
                        "QUANTITY" => $product["QUANTITY"],
                    ]);
                }

                if(is_array($companies) && !empty($companies)){
                    $this->addCompany($idTemplate, $companies);
                }
                return $idTemplate;
            }
            else{
                return  $errors = $result->getErrorMessages();
            }
        }
        else{
            return false;
        }
    }

    public function getOrderTemplateByID($id, $select = ['*'])
    {
        $orderTemplate = OrderTemplateTable::getList([
            'filter' => ["ID" => $id],
            'select' => $select,
        ])->fetch();

        if($orderTemplate){
            return $orderTemplate;
        }
        else{
            return false;
        }
    }

    public function save($id, $fields, $companies = [])
    {
        $fields["SAVED"] = "Y";
        $result = OrderTemplateTable::update($id, $fields);

        if($result->isSuccess()){
            if(!empty($companies)){
                $this->updateCompany($id, $companies);
            }
            return true;
        }
        else{
            return $result->getErrorMessages();
        }
    }

    public function delete($id)
    {
        $result = OrderTemplateTable::delete($id);

        if(!$result->isSuccess()){
            return $result->getErrorMessages();
        }
        else{
            $this->deleteProducts($id);
            $this->deleteCompany($id);
        }

        return true;
    }

    public function checkRight($templateId, $userId)
    {
        if(OrderTemplateTable::getList([
            'filter' => ['ID' => $templateId, 'USER_ID' => $userId],
            'select' => ['ID']
        ])->fetch()){
            return true;
        }
        else{
            return false;
        }
    }

    public function deleteProducts($templateId)
    {
        $dbRes = OrderTemplateProductTable::getList([
            'filter' => ["ORDER_TEMPLATE_ID" => $templateId],
            'select' => ['ID']
        ]);

        while($res = $dbRes->fetch()){
            OrderTemplateProductTable::delete($res["ID"]);
        }
    }

    public function addCompany($templateId, $companies)
    {
        foreach ($companies as $company){
            OrderTemplateCompanyTable::add([
                "ORDER_TEMPLATE_ID" => $templateId,
                "COMPANY_ID" => $company,
            ]);
        }

        return true;
    }

    public function updateCompany($templateId, $companies)
    {
        if($this->deleteCompany($templateId)){
            return $this->addCompany($templateId, $companies);
        }
    }

    public function deleteCompany($templateId = null, $companyId = null)
    {

        $primary = [];
        if($templateId){
            $primary["ORDER_TEMPLATE_ID"] = $templateId;
        }
        if($companyId){
            $primary["COMPANY_ID"] = $companyId;
        }

        $dbRes = OrderTemplateCompanyTable::getList([
            'filter' => $primary
        ]);
        while ($arRes = $dbRes->fetch()) {
            $result = OrderTemplateCompanyTable::delete([
                'ORDER_TEMPLATE_ID' => $arRes['ORDER_TEMPLATE_ID'],
                'COMPANY_ID' => $arRes['COMPANY_ID'],
            ]);
        }

        if($result && !$result->isSuccess()){
            return $result->getErrorMessages();
        }
        else{
            return true;
        }
    }

    public function getCompanies($filter)
    {
        $result = [];

        $dbCompany = OrderTemplateCompanyTable::getList([
            'filter' => $filter
        ]);

        while($resCompany = $dbCompany->fetch()){
            $result[$resCompany["ORDER_TEMPLATE_ID"]][] = $resCompany["COMPANY_ID"];
        }

        return $result;
    }

    public function addToBasket($id)
    {
        if(\CModule::IncludeModule("sale") && \CModule::IncludeModule("catalog")){
            \CSaleBasket::DeleteAll(\CSaleBasket::GetBasketUserID());

            $dbRes =  OrderTemplateProductTable::getList([
                'filter' => ["ORDER_TEMPLATE_ID"=>$id],
                'select' => ["PRODUCT_ID", "QUANTITY"],
            ]);

            while ($res = $dbRes->fetch()){
                $qnt = $res["QUANTITY"];
                $productInfo = \CCatalogProduct::GetList(
                    [],
                    ["ID" => $res["PRODUCT_ID"]],
                    false,
                    false,
                    ["ID", "QUANTITY"]
                )->fetch();
                if ($productInfo) {
                    $qnt = $productInfo["QUANTITY"] < $qnt ? $productInfo["QUANTITY"] : $qnt;
                }

                Add2BasketByProductID(
                    $res["PRODUCT_ID"],
                    $qnt,
                    array()
                );
            }

            return true;
        }
        else{
            return false;
        }
    }

    public function checkBinding($templateId, $companyId)
    {
        if(!empty($this->getCompanies([
            "ORDER_TEMPLATE_ID" => $templateId,
            "COMPANY_ID" => $companyId,
        ]))){
            return true;
        }
        else{
            return false;
        }
    }
}