<?php
use Bitrix\Main\Loader;
$aMenuLinks = [
	[
		"Каталог",
        "/b2bcabinet/orders/blank_zakaza/",
		[],
		[
            'ICON_CLASS' => 'icon-clipboard3',
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
            'ICON_CLASS' => 'icon-grid52'
        ],
        ""
    ],
	[
		"Мои заказы",
        "/b2bcabinet/orders/index.php",
		[
            "/b2bcabinet/order/detail/"
        ],
		[
            'ICON_CLASS' => 'icon-loop3'
        ],
		""
	],
	[
		"Корзина",
        "/b2bcabinet/orders/make/index.php",
		[
		    "/b2bcabinet/orders/make/make.php"
        ],
		[
            'ICON_CLASS' => 'icon-cart4'
        ],
		""
	],
];
?>