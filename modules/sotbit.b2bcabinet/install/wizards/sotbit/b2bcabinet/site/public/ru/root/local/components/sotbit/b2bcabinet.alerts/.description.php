<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
    "NAME" => GetMessage("C_B2BN_NAME"),
    "DESCRIPTION" => GetMessage("C_B2BN_DESCRIPTION"),
    "CACHE_PATH" => "Y",
    "COMPLEX" => "N",
    "PATH" => array(
        "ID" => "sotbit",
        "CHILD" => array(
            "ID" => "b2bcabinet-notifications",
            "NAME" => GetMessage("C_B2BN_CHILD_NAME"),
            "SORT" => 500,
        )
    )
);
?>