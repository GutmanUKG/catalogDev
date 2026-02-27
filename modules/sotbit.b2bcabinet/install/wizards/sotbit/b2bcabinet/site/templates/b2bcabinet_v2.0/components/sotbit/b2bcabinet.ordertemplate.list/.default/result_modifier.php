<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
global $USER;
$USER_ID = $USER->GetID();
$noSave = '';
$arResult["TABLE_HEADER"] = [
    "NAME" => GetMessage("TABLE_HEADER_NAME"),
    "ID" => "ID",
    "QUANTITY" => GetMessage("TABLE_HEADER_QUANTITY"),
];

if ($arParams["IBLOCK_ID"]){
    $arResult["FILTER_DOCUMENT"] = ["IBLOCK_ID"=>$arParams["IBLOCK_ID"]];

    if($arParams["LIST_PROPERTY_CODE"]){
        foreach ($arParams["LIST_PROPERTY_CODE"] as $code){
            $rsProperty = CIBlockProperty::GetList(array(), ["CODE"=>$code, "IBLOCK_ID"=>$arParams["IBLOCK_ID"]]);

            if($property = $rsProperty->Fetch())
            {
                $arResult["TABLE_HEADER"][$property["CODE"]] = $property["NAME"];
            }
        }

    }
}

if($arResult["TEMPLATES"]){
    foreach ($arResult["TEMPLATES"] as $key=> $template){
        $noSave = '';
        if($template["SAVED"] == "N" && $template["USER_ID"] != $USER_ID){
            continue;
        }
        elseif($template["SAVED"] == "N"){
            $noSave = '<span class="template-not-save">'. GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_NAME_NO_SAVE") . '</span>';
        }

        $pathDetail = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_DETAIL"], Array("ID" => $template["ID"]));
        $arResult["ROWS"][$key]["data"]["ID"] = $template["ID"];
        $arResult["ROWS"][$key]["data"]["NAME"] = $template["NAME"].$noSave;
        $arResult["ROWS"][$key]["data"]["DATE_CREATE"] =  $template["DATE_CREATE"]->toString();


        if($template["USER_NAME"] || $template["USER_LAST_NAME"]){
            $userName = $template["USER_LAST_NAME"]."&nbsp;".$template["USER_NAME"];
        }
        else{
            $userName = $template["USER_LOGIN"];
        }

        $arResult["ROWS"][$key]["data"]["USER"] =  $userName;

        if($template["SAVED"] != "N"){
            $arResult["ROWS"][$key]['actions'][] = [
                "TEXT" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_ACTION_CREATE_ORDER"),
                "ONCLICK" => "showFormAddBasket({$template["ID"]}, '{$template["NAME"]}')",
                "ICON" => "ph-plus"
            ];
            $arResult["ROWS"][$key]['actions'][] = [
                "TEXT" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_ACTION_EXCEL"),
                "ONCLICK" => "exportExcelTemplate({$template["ID"]}, '{$template["NAME"]}')",
                "ICON" => "ph-arrow-line-up"
            ];

            $arResult["ROWS"][$key]['actions']['eye'] = [
                "TEXT" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_ACTION_SHOW_DETAIL"),
                "ONCLICK" => "location.assign('".$pathDetail."')",
                "DEFAULT" => true,
            ];

        }

        if($template["USER_ID"] == $USER->GetID()){
            $arResult["ROWS"][$key]['actions']['eye'] = [
                "TEXT" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_ACTION_SHOW_DETAIL"),
                "ONCLICK" => "location.assign('".$pathDetail."')",
                "DEFAULT" => true
            ];
            
            $arResult["ROWS"][$key]['actions'][] = [
                "TEXT" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_ACTION_DELETE"),
                "ONCLICK" => "showRemoveForm(".$template["ID"].", '".$template["NAME"]."')",
                "ICON" => "ph-trash"
            ];
        }

        if($template["USER_ID"] != $USER->GetID() && $arResult["IS_ADMIN"]){
            $arResult["ROWS"][$key]['actions'][] = [
                "TEXT" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_ACTION_DELETE"),
                "ONCLICK" => "showRemoveForm(".$template["ID"].", '".$template["NAME"]."')",
            ];
        }
    }
}

?>