<?
use Bitrix\Main\Loader;
$aMenuLinks = [
    [
        "Каталог",
        "/orders/blank_zakaza/",
        [],
        [
            'ICON_CLASS' => 'ph-list-bullets',
            'IS_CATALOG' => 'Y'
        ],
        ""
    ],
    [
        "Шаблоны заказов",
        "/orders/templates/",
        [

        ],
        [
            'ICON_CLASS' => 'ph-circles-four'
        ],
        ""
    ],
	[
		"Мои заказы",
        "/orders/index.php",
		[
            "/orders/detail/"
        ],
        [
            'ICON_CLASS' => 'ph-shopping-bag-open'
        ],
        ""
    ],
    [
        "Корзина",
        "/orders/make/index.php",
        [
            "/orders/make/make.php"
        ],
        [
            'ICON_CLASS' => 'ph-shopping-cart-simple'
        ],
        ""
    ],
];
?>