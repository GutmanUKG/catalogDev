<?php
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Sotbit\Auth\Company\Company;
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class B2BCabinetDraftAdd extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'adddraft' => [
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
        return $params;
    }

    protected function listKeysSignedParameters()
    {
        return [
            "DELETE_BASKET",
        ];
    }

    public function addDraftAction($name)
    {
       if(!$name)
           return [
               'error' => true,
               'errorMessage' => GetMessage("BDA_TITLE_ERROR_NO_NAME")
           ];

       if(\CModule::IncludeModule("sale")){
            global $USER;
            $dbBasketItems = \CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "USER_ID" => $USER->GetID(),
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL",
                ),
                false,
                false,
                array("ID", "PRODUCT_ID", "QUANTITY")
            );
            while ($arItems = $dbBasketItems->Fetch())
            {
                $productFields[] = $arItems;
            }

            if($productFields){
                $draft = new \Sotbit\B2BCabinet\Draft\Draft(SITE_ID);
                if(is_numeric($draft->add($name, $productFields, $USER->GetID()))){
                    if($this->arParams["DELETE_BASKET"] == "Y"){
                        \CSaleBasket::DeleteAll(\CSaleBasket::GetBasketUserID());
                    }

                    return [
                        'error' => false,
                        'message' => GetMessage("BDA_TITLE_SUCCESS", ["#DRAFT_NAME#" => $name])
                    ];
                }
            }
       }
    }

	public function executeComponent()
	{
		$this->includeComponentTemplate();
	}
}