<?php

use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}


class SotbitPersonalManager extends CBitrixComponent
{
    private $managerId = 0;

    public function onPrepareComponentParams($params)
    {
        return $params;
    }

    public function executeComponent()
    {
        $this->managerId = $this->getManagerId();

        if (!$this->managerId) {
            return;
        }

        $this->prepareResult();
        $this->includeComponentTemplate();
    }

    private function getManagerId()
    {
        if (Loader::includeModule('sotbit.auth') && defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES === "Y") {
            return \Sotbit\Auth\Company\Company::getCurrentManager();
        }

        if (isset($this->arParams['MANAGER_ID']) && !empty($this->arParams['MANAGER_ID'])) {
            return $this->arParams['MANAGER_ID'];
        }

        global $USER;
        if (is_object($USER) && $USER->IsAuthorized()) {
            $userID = $USER->GetID();
            $resUser = CUser::GetByID($userID)->fetch();
            return $resUser['UF_P_MANAGER_ID'] ?? 0;
        }
    }

    private function prepareResult()
    {
        global $USER;
        $arManager = CUser::GetByID($this->managerId)->fetch();

        if (!empty($arManager) && !empty($this->arParams['SHOW_FIELDS'])) {
            $this->arResult["NAME_FORMATTED"] = CUser::FormatName($this->arParams['NAME_TEMPLATE'], $arManager);
            foreach ($this->arParams['SHOW_FIELDS'] as $field) {
                $this->arResult[$field] = $arManager[$field];
            }
        }


        if (
            (isset($this->arParams['USER_PROPERTY']) && !empty($this->arParams['USER_PROPERTY'])) &&
            (is_object($USER) && $USER->IsAuthorized())
        ) {
            $order = array('sort' => 'asc');
            $by = array('sort');
            $arUserFields = $this->arParams['USER_PROPERTY'];

            $rsUser = CUser::GetList(
                $order,
                $by,
                array('ID' => $USER->GetID()),
                array('SELECT' => $arUserFields)
            );

            $res = $rsUser->fetch();

            foreach ($arUserFields as $arUserField) {
                if (isset($res[$arUserField]) && !empty($res[$arUserField])) {
                    $this->arResult['USER_PROPERTY'][$arUserField] = $res[$arUserField];
                }
            }
        }
    }
}