<?php
use Bitrix\Main\Loader;
$aMenuLinks = [
    [
        "Каталог",
        "/orders/blank_zakaza/",
        [],
        [
            'ICON_CLASS' => 'icon-clipboard3',
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
            'ICON_CLASS' => 'icon-grid52'
        ],
        ""
    ],
    [
        "Мои заказы",
        "/orders/index.php",
        [
            "/order/detail/"
        ],
        [
            'ICON_CLASS' => 'icon-loop3'
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
            'ICON_CLASS' => 'icon-cart4'
        ],
        ""
    ],
];
?>