<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
    "NAME" => GetMessage("SC_CSQ_CATALOG_STORES_QUANTITY"),
    "DESCRIPTION" => GetMessage("SC_CSQ_CATALOG_STORES_QUANTITY_DESCRIPTION"),
    "CACHE_PATH" => "Y",
    "COMPLEX" => "N",
    "PATH" => array(
        "ID" => "sotbit",
        "CHILD" => array(
            "ID" => "catalog-store",
            "NAME" => GetMessage("SC_CSQ_CATALOG_STORE_STORE_SECTION"),
            "SORT" => 500,
        )
    )
);
?>