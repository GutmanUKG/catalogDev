<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


class B2BExcelImportResult extends \CBitrixComponent
{
    public function onPrepareComponentParams($params)
    {
        $this->arResult = $params["RESULT"] ?: [];
        return $params;
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}