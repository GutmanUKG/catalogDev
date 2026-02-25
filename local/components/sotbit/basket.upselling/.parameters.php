<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\CatalogIblockTable;
use Bitrix\Catalog\GroupTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;

Loc::getIncludedFiles(__FILE__);

if (!Loader::includeModule('iblock'))
	return;

$catalogIncluded = Loader::includeModule('catalog');

$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$offersIblock = array();

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);
$arSort['by_price'] = GetMessage('SORT_BY_PRICE');

$arIBlock = array();
$iblockFilter = !empty($arCurrentValues['IBLOCK_TYPE'])
    ? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
    : array('ACTIVE' => 'Y');

$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
    {
        $id = (int)$arr['ID'];
        if (isset($offersIblock[$id]))
            continue;
        $arIBlock[$id] = '['.$id.'] '.$arr['NAME'];
    }
unset($id, $arr, $rsIBlock, $iblockFilter);
unset($offersIblock);

$arAscDesc = array(
    'asc' => GetMessage('IBLOCK_SORT_ASC'),
    'desc' => GetMessage('IBLOCK_SORT_DESC'),
);

$priceType = GroupTable::query()
	->setSelect(['ID', 'NAME'])
	->fetchAll()
;

$priceType = array_column($priceType, 'NAME', 'ID');

if (isset($_REQUEST['src_site']) && $_REQUEST['src_site'] && is_string($_REQUEST['src_site']))
{
	$siteId = $_REQUEST['src_site'];
}
else
{
	$siteId = \CSite::GetDefSite();
}

$arComponentParameters = [
	'GROUPS' => [
        'SORT_SETTINGS' => [
			'NAME' => GetMessage('SORT_SETTINGS'),
			'SORT' => 210
        ],
		'CROSSSELL_SETTINGS' => [
			'NAME' => GetMessage('CROSSSELL_SETTINGS'),
			'SORT' => 220
		]
    ],
	'PARAMETERS' => [
        'IBLOCK_TYPE' => [
			'PARENT' => 'BASE',
			'NAME' => GetMessage('IBLOCK_TYPE'),
			'TYPE' => 'LIST',
			'ADDITIONAL_VALUES' => 'Y',
			'VALUES' => $arIBlockType,
			'REFRESH' => 'Y',
        ],
        'IBLOCK_ID' => [
			'PARENT' => 'BASE',
			'NAME' => GetMessage('IBLOCK_IBLOCK'),
			'TYPE' => 'LIST',
			'ADDITIONAL_VALUES' => 'Y',
			'VALUES' => $arIBlock,
			'REFRESH' => 'Y',
        ],
        'PAGE_ELEMENT_COUNT' => [
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('IBLOCK_PAGE_ELEMENT_COUNT'),
			'TYPE' => 'STRING',
			'HIDDEN' => isset($templateProperties['PRODUCT_ROW_VARIANTS']) ? 'Y' : 'N',
			'DEFAULT' => '18'
        ],
		'TYPE_PRICE' => [
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('UPSELING_TYPE_PRICE'),
			'TYPE' => 'LIST',
			'VALUES' => $priceType,
			"MULTIPLE" => "Y",
		],
        'SHOW_ALL_WO_SECTION' => [
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('CP_BCS_SHOW_ALL_WO_SECTION'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		],
        'ELEMENT_SORT_FIELD' => [
			'PARENT' => 'SORT_SETTINGS',
			'NAME' => GetMessage('IBLOCK_ELEMENT_SORT_FIELD'),
			'TYPE' => 'LIST',
			'VALUES' => $arSort,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'sort',
		],
		'ELEMENT_SORT_ORDER' => [
			'PARENT' => 'SORT_SETTINGS',
			'NAME' => GetMessage('IBLOCK_ELEMENT_SORT_ORDER'),
			'TYPE' => 'LIST',
			'VALUES' => $arAscDesc,
			'DEFAULT' => 'asc',
			'ADDITIONAL_VALUES' => 'Y',
		],
		'ELEMENT_SORT_FIELD2' => [
			'PARENT' => 'SORT_SETTINGS',
			'NAME' => GetMessage('IBLOCK_ELEMENT_SORT_FIELD2'),
			'TYPE' => 'LIST',
			'VALUES' => $arSort,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'id',
		],
		'ELEMENT_SORT_ORDER2' => [
			'PARENT' => 'SORT_SETTINGS',
			'NAME' => GetMessage('IBLOCK_ELEMENT_SORT_ORDER2'),
			'TYPE' => 'LIST',
			'VALUES' => $arAscDesc,
			'DEFAULT' => 'desc',
			'ADDITIONAL_VALUES' => 'Y',
		],
		'CACHE_TIME' => array('DEFAULT' => 432000),
		'CACHE_GROUPS' => array(
			'PARENT' => 'CACHE_SETTINGS',
			'NAME' => GetMessage('CP_BCS_CACHE_GROUPS'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
    ],
];

if (Loader::includeModule('sotbit.privateprice')) {
	$arComponentParameters['PARAMETERS']['PRIVATE_PRICE'] = [
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('UPSELING_USE_PRIVITE_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	];
}

if ($catalogIncluded) {
    $arComponentParameters['PARAMETERS']['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('CP_BCS_HIDE_NOT_AVAILABLE'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'N',
		'VALUES' => array(
			'Y' => GetMessage('CP_BCS_HIDE_NOT_AVAILABLE_HIDE'),
			'L' => GetMessage('CP_BCS_HIDE_NOT_AVAILABLE_LAST'),
			'N' => GetMessage('CP_BCS_HIDE_NOT_AVAILABLE_SHOW')
		),
		'ADDITIONAL_VALUES' => 'N'
	);
}

$use_b_iblock_property_feature = 'Y' === Option::get(
	'iblock', 'property_features_enabled'
);

	$iblocks_bound_up_with_catalog = CatalogIblockTable::query()
	->addSelect('IBLOCK_ID')
	->addSelect(IblockTable::getTableName().'.NAME', 'NAME')
	->registerRuntimeField(IblockTable::getTableName(), [
		'data_type' => IblockTable::class,
		'reference' => ['this.IBLOCK_ID' => 'ref.ID'],
	])
	->fetchAll();

	$property = PropertyTable::query()
		->setSelect(['NAME', 'ID'])
		->addSelect(IblockTable::getTableName().'.NAME', 'iblock_name')
		->whereIn('IBLOCK_ID', array_column($iblocks_bound_up_with_catalog, 'IBLOCK_ID'))
		->registerRuntimeField(IblockTable::getTableName(), [
			'data_type' => IblockTable::class,
			'reference' => ['this.IBLOCK_ID' => 'ref.ID'],
		])
		->fetchAll();

	$display_prop = [];

	foreach ($property as $i) {
		$display_prop[$i['ID']] = "{$i['iblock_name']} =>    {$i['NAME']}";
	}

if (!$use_b_iblock_property_feature) {

	$arComponentParameters["PARAMETERS"]["IN_BASKET"] = [
		"PARENT" => "VISUAL",
		"NAME" => GetMessage('IN_BASKET'),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $display_prop,
		"SIZE" => 10,
	];

	$arComponentParameters["PARAMETERS"]["OFFER_TREE"] = [
		"PARENT" => "VISUAL",
		"NAME" => GetMessage('OFFER_TREE'),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $display_prop,
		"SIZE" => 10,
	];
}

$arComponentParameters["PARAMETERS"]['ARTICLE'] = [
	"PARENT" => "VISUAL",
	"NAME" => GetMessage('ARTICLE_PROPERTY'),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"VALUES" => $display_prop,
	"SIZE" => 10,
];


$arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"] = [];
$arComponentParameters['PARAMETERS']['SEF_MODE'] = [];

$arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["ELEMENT_ID"] = array(
	"NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_ELEMENT_ID"),
	"TEMPLATE" => "#ELEMENT_ID#",
);
$arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SECTION_ID"] = array(
	"NAME" => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_ID"),
	"TEMPLATE" => "#SECTION_ID#",
);

$smartBase = ($arCurrentValues["SEF_URL_TEMPLATES"]["section"]? $arCurrentValues["SEF_URL_TEMPLATES"]["section"]: "#SECTION_ID#/");
$arComponentParameters["PARAMETERS"]["SEF_MODE"]["smart_filter"] = array(
	"NAME" => GetMessage("CP_BC_SEF_MODE_SMART_FILTER"),
	"DEFAULT" => $smartBase."filter/#SMART_FILTER_PATH#/apply/",
	"VARIABLES" => array(
		"SECTION_ID",
		"SECTION_CODE",
		"SECTION_CODE_PATH",
		"SMART_FILTER_PATH",
	),
);

if(Loader::includeModule('sotbit.crosssell') && Option::get("sotbit.crosssell", 'sotbit.crosssell_INC_MODULE', '', $siteId) === 'Y') {
	$sectionMode = array(
		"N" => GetMessage("UPSELING_SECTION_MODE_N"),
		"Y" => GetMessage("UPSELING_SECTION_MODE_Y"),
	);

	$arQuery = \Sotbit\Crosssell\Orm\CrosssellTable::getList(
		array(
			'select' => array('ID', 'NAME'),
			'filter' => array('TYPE_BLOCK' => 'CROSSSELL', 'Active' => 'Y'),
		)
	);
	
	$arRes = array('s0' => GetMessage("UPSELING_NOT_CATEGORIES"));
	while ($ar = $arQuery->fetch()) {
		$arRes['e' . $ar['ID']] = ' . ' . $ar['NAME'];
	}

	$arComponentParameters['PARAMETERS']['CROSSSELL_STATUS'] = [
		'PARENT' => 'CROSSSELL_SETTINGS',
		'NAME' => GetMessage('UPSELING_CROSSSELL_STATUS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
		'DEFAULT' => 'N'
	];

	if ($arCurrentValues['CROSSSELL_STATUS'] === 'Y') {
		$arComponentParameters['PARAMETERS']['SECTION_MODE'] = [
			'PARENT' => 'CROSSSELL_SETTINGS',
			'NAME' => GetMessage('UPSELING_SECTION_MODE'),
			'TYPE' => 'LIST',
			'REFRESH' => 'Y',
			'VALUE' => 'Y',
			'VALUES' => $sectionMode,
		];

		if ($arCurrentValues['SECTION_MODE'] === 'Y') {
			$arComponentParameters['PARAMETERS']['CROSSSELL_LIST'] = array(
				'PARENT' => 'CROSSSELL_SETTINGS',
				'NAME' => GetMessage('UPSELING_CROSSSELL_LIST'),
				'TYPE' => 'LIST',
				'VALUES' => $arRes,
				'MULTIPLE' => 'Y',
				'REFRESH' => 'N',
				'SIZE' => '9',
			);
		}
	}
}