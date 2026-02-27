<?php
use Bitrix\Main\Loader;

$aMenuLinks = [
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
];
?>