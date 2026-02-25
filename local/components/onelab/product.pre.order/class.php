<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Error;
use Bitrix\Main\Result;

class OnelabProductPreOrder extends \CBitrixComponent implements \Bitrix\Main\Engine\Contract\Controllerable
{
    public function configureActions()
    {
        return [
            'send' => [
                'prefilters' => [
                    new \Bitrix\Main\Engine\ActionFilter\Cors(),
                ],
            ],
        ];
    }

    public function executeComponent()
    {
        CJSCore::Init(['masked_input']);
        
        $this->includeComponentTemplate();
    }

    public function sendAction()
    {
        $FORM_ID = 3;

        \Bitrix\Main\Loader::includeModule("form");
        \Bitrix\Main\Loader::includeModule("iblock");

        $phone = normalizePhone($this->request->getPost('PHONE'));
        $productId = intval($this->request->getPost('PRODUCT_ID'));
        $name = $this->request->getPost('NAME');

        if ($productId < 1) {
            $result = new Result();
            $result->addError(new Error('Error'));
            return AjaxJson::createError($result->getErrorCollection());
        }

        $rsElement = CIBlockElement::GetList(
            $arOrder  = array("SORT" => "ASC"),
            $arFilter = array(
                "ACTIVE"    => "Y",
                'IBLOCK_ID' => IBLOCK_CATALOG_ID,
                'ID'        => $productId,
            ),
            false,
            false,
            $arSelectFields = array("ID", "IBLOCK_ID", "NAME")
        );
        if (!($arElement = $rsElement->fetch())) {
            $result = new Result();
            $result->addError(new Error('Error'));
            return AjaxJson::createError($result->getErrorCollection());
        }

        $arValues = [
            'form_text_7'     => $name,
            'form_text_8'     => $phone,
            'form_text_9'     => $arElement['NAME'],
        ];

        $error = \CForm::Check($FORM_ID, $arValues);

        if ($error) {
            $result = new Result();
            $result->addError(new Error($error));
            return AjaxJson::createError($result->getErrorCollection());
        }

        if (!($RESULT_ID = \CFormResult::Add($FORM_ID, $arValues, 'N'))) {
            $result = new Result();
            $result->addError(new Error('Error'));
            return AjaxJson::createError($result->getErrorCollection());
        }

        \CFormCRM::onResultAdded($FORM_ID, $RESULT_ID);
        \CFormResult::SetEvent($RESULT_ID);
        \CFormResult::Mail($RESULT_ID);
    }
}