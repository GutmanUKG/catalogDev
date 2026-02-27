<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

$APPLICATION->SetTitle("Оплата заказа");
?>
<?
    if(Loader::includeModule('sotbit.auth') && \Bitrix\Main\Config\Option::get("sotbit.auth", "EXTENDED_VERSION_COMPANIES", "N") == "Y"){
        $APPLICATION->IncludeComponent(
            "sotbit:auth.order.payment",
            "",
            Array(
            )
        );
    }
    else{
        $APPLICATION->IncludeComponent(
            "bitrix:sale.order.payment",
            "",
            Array(
            )
        );
    }
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>