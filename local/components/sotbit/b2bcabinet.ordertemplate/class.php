<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option,
    Bitrix\Main\Loader;

class OrderTemplate extends CBitrixComponent
{

    function onPrepareComponentParams($params)
    {
        if (!$GLOBALS["USER"]->IsAuthorized()) {
            $params["PRICE_CODE"] = [Option::get('sotbit.b2bcabinet', 'PRICE_FOR_NOT_AUTHORIZED_USER', null, SITE_ID)];
            return $params;
        }

        if (Loader::includeModule("sotbit.regions") && isset($_SESSION["SOTBIT_REGIONS"]) && isset($_SESSION["SOTBIT_REGIONS"]["PRICE_CODE"])) {
            $params["PRICE_CODE"] = $_SESSION["SOTBIT_REGIONS"]["PRICE_CODE"];
        }

        return $params;
    }

    public function executeComponent()
    {
        $arDefaultUrlTemplates404 = array(
            "list" => "ordertemplate_list.php",
            "detail" => "ordertemplate_detail.php?ID=#ID#",
        );

        $arDefaultVariableAliases404 = array();

        $arComponentVariables = array("ID", "del_id");

        $arVariables = array();

        $this->setFrameMode(false);

        if ($this->arParams["SEF_MODE"] == "Y")
        {
            $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

            $componentPage = CComponentEngine::parseComponentPath(
                $this->arParams["SEF_FOLDER"],
                $arUrlTemplates,
                $arVariables
            );

            CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

            foreach ($arUrlTemplates as $url => $value)
                $this->arResult["PATH_TO_".ToUpper($url)] = $this->arParams["SEF_FOLDER"].$value;

            if ($componentPage != "detail")
                $componentPage = "list";

            $this->arResult = array_merge(
                Array(
                    "SEF_FOLDER" => $this->arParams["SEF_FOLDER"],
                    "URL_TEMPLATES" => $arUrlTemplates,
                    "VARIABLES" => $arVariables,
                    "ALIASES" => $arVariableAliases,
                ),
                $this->arResult
            );
        }
        else
        {
            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);
            CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

            if ((int)($_REQUEST["ID"]) > 0)
                $componentPage = "detail";
            else
                $componentPage = "list";

            $this->arResult = array(
                "VARIABLES" => $arVariables,
                "ALIASES" => $arVariableAliases
            );
        }
        $this->includeComponentTemplate($componentPage);

    }

}