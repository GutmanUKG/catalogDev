<?php

namespace Sotbit\B2BCabinet\Draft;

use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Sotbit\B2BCabinet\Internals\DraftTable;
use Sotbit\B2BCabinet\Internals\DraftProductTable;

class Draft extends \SotbitB2bCabinet
{
    protected $idSite;

    public function __construct($site = '')
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        if ($site) {
            $this->idSite = $site;
        } else {
            $this->idSite = $context->getSite();
        }
        unset($context);
    }

    public function add($name, $productFields, $userId = false)
    {
        global $USER;
        $resAddDraft = DraftTable::add([
            "NAME" => $name,
            "USER_ID" => $userId ?: $USER->getID(),
            "SITE_ID" => $this->idSite
        ]);
        if ($resAddDraft->isSuccess())
        {
            $idDraft = $resAddDraft->getId();
            foreach ($productFields as $product){
                DraftProductTable::add([
                    "DRAFT_ID" => $idDraft,
                    "PRODUCT_ID" => $product["PRODUCT_ID"],
                    "QUANTITY" => $product["QUANTITY"],
                ]);
            }
            return $idDraft;
        }
        else{
            return  $errors = $resAddDraft->getErrorMessages();
        }
    }

    public function getDraftsByUser($userID, $order = [])
    {
       $result = DraftTable::getList([
            'filter' => ["USER_ID" => $userID],
            'order' => $order
        ])->fetchAll();

       return $result;
    }

    public function getDrafts($filter = [], $select = [], $order = [])
    {
        $result = DraftTable::getList([
            'filter' => $filter,
            'select' => $select?:['*'],
            'order' => $order
        ])->fetchAll();

        return $result;
    }

    public function getAllDraftProductsByUser($userID, $order = [])
    {
        $dbResult = DraftProductTable::getList([
            'filter' => ['DRAFT.USER_ID' => $userID],
            'order' => $order,
        ]);

        while($result = $dbResult->fetch()){
           $products[$result["DRAFT_ID"]][] = $result;
        }

        return $products;
    }

    public function removeDraft($draftId)
    {
        $result = DraftTable::delete($draftId);

        if($result->isSuccess()){
            return self::removeDraftProducts($draftId);
        }
        else{
            return $result->getErrorMessages();
        }
    }

    public function removeDraftProducts($draftId)
    {
        $dbResult = DraftProductTable::getList([
            'filter' => ['DRAFT_ID' => $draftId],
            'select' => ['ID']
        ]);

        while($result = $dbResult->fetch()){
            DraftProductTable::delete($result["ID"]);
        }

        return true;
    }

    public function formBasket($draftId)
    {
        if(\CModule::IncludeModule("sale") && \CModule::IncludeModule("catalog")){
            \CSaleBasket::DeleteAll(\CSaleBasket::GetBasketUserID());

            $dbRes =  DraftProductTable::getList([
                'filter' => ["DRAFT_ID"=>$draftId],
                'select' => ["PRODUCT_ID", "QUANTITY"],
            ]);

            while ($res = $dbRes->fetch()){
                Add2BasketByProductID(
                    $res["PRODUCT_ID"],
                    $res["QUANTITY"],
                    array()
                );
            }
            $return = self::removeDraft($draftId);
            return $return;
        }
        else{
            return false;
        }
    }
}