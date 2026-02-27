<?
use Bitrix\Main\Loader;

$aMenuLinks = [
	[
		"Каталог",
        "/b2bcabinet/orders/blank_zakaza/",
		[],
		[
            'ICON_CLASS' => 'ph-list-bullets',
            'IS_CATALOG' => 'Y'
        ],
		""
	],
    [
        "Шаблоны заказов",
        "/b2bcabinet/orders/templates/",
        [

        ],
        [
            'ICON_CLASS' => 'ph-circles-four'
        ],
        ""
    ],
	[
		"Мои заказы",
        "/b2bcabinet/orders/index.php",
		[
            "/b2bcabinet/orders/detail/"
        ],
		[
            'ICON_CLASS' => 'ph-shopping-bag-open'
        ],
		""
	],
	[
		"Корзина",
        "/b2bcabinet/orders/make/",
		[
		    "/b2bcabinet/orders/make/make.php"
        ],
		[
            'ICON_CLASS' => 'ph-shopping-cart-simple'
        ],
		""
	],
];
?>