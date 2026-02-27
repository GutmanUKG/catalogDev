<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $ORDER_TEMPLATE_DETAIL;

$filter = [];
$filterOption = new Bitrix\Main\UI\Filter\Options('ORDER_TEMPLATE_DETAIL');
$filterData = $filterOption->getFilter([]);
if($filterData){
    foreach ($filterData as $key => $value)
    {
        if(in_array($key, ['ID','NAME','FIND']))
        {
            switch ($key)
            {
                case 'ID':
                    {
                        $ORDER_TEMPLATE_DETAIL['ID'] = $value;
                        break;
                    }
                case 'NAME':
                    {
                        $ORDER_TEMPLATE_DETAIL['%NAME'] = $value;
                        break;
                    }
                case 'FIND':
                    {
                        if($value)
                            $ORDER_TEMPLATE_DETAIL['%NAME'] = $value;
                        break;
                    }

                default:
                    {
                        $ORDER_TEMPLATE_DETAIL['%NAME'] = $value;
                    }
            }
        }
    }
}

$APPLICATION->IncludeComponent(
	"sotbit:b2bcabinet.ordertemplate.detail",
	"",
	array(
		"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
		"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
		"ADD_CHAIN" =>$arParams["ADD_CHAIN"],
		"USE_AJAX_LOCATIONS" => $arParams['USE_AJAX_LOCATIONS'],
		"ID" => $arResult["VARIABLES"]["ID"],
		"FILTER_NAME" => "ORDER_TEMPLATE_DETAIL",
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "LIST_PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "SEF_MODE" => "N",
		"SEF_FOLDER" => "/b2bcabinet/orders/make/",
		"PRODUCTS_DETAIL_PATH" => $arParams["PRODUCTS_DETAIL_PATH"],
		"VARIABLE_ALIASES" => array(
			"ELEMENT_ID" => "ELEMENT_ID",
			"SECTION_ID" => "SECTION_ID",
        ),
	),
	$component
);
?>
