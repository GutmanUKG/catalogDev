<?
$aMenuLinks = Array(
	Array(
		"Главная",
		"/",
		Array(),
		Array("ICON_CLASS"=>"ph-house"),
		"\\Bitrix\\Main\\Loader::includeModule('sotbit.b2bcabinet')"
	),
	Array(
		"Персональные данные",
		"/personal/",
		Array(),
		Array(),
		"\\Bitrix\\Main\\Loader::includeModule('sotbit.b2bcabinet')"
	),
	Array(
		"Заказы",
		"/orders/",
		Array(),
		Array(),
		"\\Bitrix\\Main\\Loader::includeModule('sotbit.b2bcabinet')"
	),
	Array(
		"Документы",
		"/documents/",
		Array(),
		Array("ICON_CLASS"=>"icon-stack-text"),
		"\\Bitrix\\Main\\Loader::includeModule('sotbit.b2bcabinet') && \\Sotbit\\B2bCabinet\\Helper\\Document::getIblocks()"
	),
	Array(
		"О нас",
		"/about/",
		Array(),
		Array(),
		"\\Bitrix\\Main\\Loader::includeModule('sotbit.b2bcabinet')"
	),
);
?>