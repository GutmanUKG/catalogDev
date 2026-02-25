<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/main/utils.js');


global $TEMPLATE_LIST;

$filter = [];
$filterOption = new Bitrix\Main\UI\Filter\Options('TEMPLATE_LIST');
$filterData = $filterOption->getFilter([]);
if($filterData){
    foreach ($filterData as $key => $value)
    {
        if(in_array($key, ['ID','NAME','DATE_CREATE_from', 'DATE_CREATE_to', 'FIND']))
        {
            switch ($key)
            {
                case 'ID':
                    {
                        $TEMPLATE_LIST['ID'] = $value;
                        break;
                    }
                case 'NAME':
                    {
                        $TEMPLATE_LIST['%NAME'] = $value;
                        break;
                    }
                case 'DATE_CREATE_from':
                    {
                        $TEMPLATE_LIST['>=DATE_CREATE'] = $value;
                        break;
                    }
                case 'DATE_CREATE_to':
                    {
                        $TEMPLATE_LIST['<=DATE_CREATE'] = $value;
                        break;
                    }
                case 'FIND':
                    {
                        if($value)
                            $TEMPLATE_LIST['%NAME'] = $value;
                        break;
                    }

                default:
                    {
                        $TEMPLATE_LIST['%NAME'] = $value;
                    }
            }
        }
    }
}

$by = isset($_GET['by']) ?  $_GET['by'] : "ID";
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'DESC';

$APPLICATION->IncludeComponent(
    "sotbit:b2bcabinet.ordertemplate.list",
    ".default",
    array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "LIST_PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
        "COMPONENT_TEMPLATE" => ".default",
        "PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
        "PER_PAGE" => $arParams["PER_PAGE"],
        "SET_TITLE" =>$arParams["SET_TITLE"],
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "FILTER_NAME" => "TEMPLATE_LIST",
        "SORT_BY" => $by,
        "SORT_ORDER" => $order,
    ),
    $component
);
?>
