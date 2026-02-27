<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
    "PARAMETERS" => [
        "COUNT_DRAFT_PAGE"=> array(
            "NAME" => GetMessage("COUNT_DRAFT_PAGE"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => 10,
            "PARENT" => "ADDITIONAL_SETTINGS",
        )
    ]
);