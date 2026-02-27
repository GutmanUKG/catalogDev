<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\{PersonTypeTable, OrderPropsTable};
use Bitrix\Main\GroupTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Sotbit\B2bCabinet\Helper\{Request, Config, Document};

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $APPLICATION;

Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight("main") === "D") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

$request = Request::getInstance();
$siteID = htmlspecialcharsbx($request->get('site'));
$isB2BRegister = !Loader::includeModule('sotbit.auth');

if (empty($siteID)) {
    Config::checkUriSite();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . SotbitB2bCabinet::MODULE_ID . '/classes/CModuleOptions.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . SotbitB2bCabinet::MODULE_ID . "/include.php");

$groups = [];
$access = [
    'REFERENCE_ID' => ['M', 'S'],
    'REFERENCE' => [
        Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_ACCESS_M'),
        Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_ACCESS_S')
    ]
];

$rs = GroupTable::getList();

while ($group = $rs->fetch()) {
    $groups['REFERENCE_ID'][] = $group['ID'];
    $groups['REFERENCE'][] = '[' . $group['ID'] . '] ' . $group['NAME'];
}

$priceGroup = [];
if (Loader::includeModule('catalog')) {
    $result = \Bitrix\Catalog\GroupTable::getList(array(
        'select' => array('ID', 'NAME'),
    ));
    while ($row = $result->fetch()) {
        $priceGroup['REFERENCE_ID'][] = $row['NAME'];
        $priceGroup['REFERENCE'][] = '[' . $row['ID'] . '] ' . $row['NAME'];
    }
}

/**
 * Documents
 */
$arCurrentValues[Document::IBLOCKS_TYPE] = !empty($request->get(Document::IBLOCKS_TYPE)) ?
    htmlspecialcharsbx($request->get(Document::IBLOCKS_TYPE)) :
    Config::get(Document::IBLOCKS_TYPE, $siteID);

$arCurrentValues[Document::IBLOCKS_ID] = !empty($request->get(Document::IBLOCKS_ID)) ?
    intval($request->get(Document::IBLOCKS_ID)) :
    Config::get(Document::IBLOCKS_ID, $siteID);

$arCurrentValues["BANNERS_IBLOCKS_TYPE"] = !empty($request->get("BANNERS_IBLOCKS_TYPE")) ?
    htmlspecialcharsbx($request->get("BANNERS_IBLOCKS_TYPE")) :
    Config::get("BANNERS_IBLOCKS_TYPE", $siteID);

$arCurrentValues["CATALOG_IBLOCK_TYPE"] = !empty($request->get("CATALOG_IBLOCK_TYPE")) ?
    htmlspecialcharsbx($request->get("CATALOG_IBLOCK_TYPE")) :
    Config::get("CATALOG_IBLOCK_TYPE", $siteID);

$arCurrentValues["VERSION_TEMPLATE"] = !empty($request->get("VERSION_TEMPLATE")) ?
    htmlspecialcharsbx($request->get("VERSION_TEMPLATE")) :
    Config::get("VERSION_TEMPLATE", $siteID);

// Infoblock types
$arIBlockType = Config::getIblockTypes();
if (!empty($arIBlockType)) {
    foreach ($arIBlockType as $code => $val) {
        $arIBlockTypeSel["REFERENCE_ID"][] = $code;
        $arIBlockTypeSel["REFERENCE"][] = $val;
    }
}

// Infoblocks

function getIblockCodeList($iblockType)
{
    $arIBlockSel = [];

    if (!empty($iblockType)) {
        $rsIBlock = CIBlock::GetList([
            "sort" => "asc"
        ], [
            "=TYPE" => $iblockType,
            "ACTIVE" => "Y"
        ]);
        while ($arr = $rsIBlock->Fetch()) {
            if (!empty($arr)) {
                $arIBlockSel["REFERENCE_ID"][] = $arr["ID"];
                $arIBlockSel["REFERENCE"][] = "[" . $arr["ID"] . "] " . $arr["NAME"];
            }
        }
    } else {
        $arIBlockSel["REFERENCE_ID"][] = '';
        $arIBlockSel["REFERENCE"][] = '';
    }

    return $arIBlockSel;
}


$orderFields = [];
$orderFieldsIds = [];
$rs = OrderPropsTable::getList([
    'filter' => [
        'ACTIVE' => 'Y',
    ],
    'select' => [
        'ID',
        'CODE',
        'NAME'
    ]
]);
while ($property = $rs->fetch()) {
    $orderFields['REFERENCE_ID'][$property['CODE']] = $property['CODE'];
    $orderFields['REFERENCE'][$property['CODE']] = "[" . $property['CODE'] . "] " . $property['NAME'];

    $orderFieldsIds['REFERENCE_ID'][$property['ID']] = $property['ID'];
    $orderFieldsIds['REFERENCE'][$property['ID']] = "[" . $property['ID'] . "][" . $property['CODE'] . "] " . $property['NAME'];
}

$personalTypes = array();
$rs = PersonTypeTable::getList(
    array(
        'filter' => array(
            'ACTIVE' => 'Y',
            array(
                'LOGIC' => 'OR',
                array('LID' => $siteID),
                array('PERSON_TYPE_SITE.SITE_ID' => $siteID),
            ),
        ),
        'select' => array(
            'ID',
            'NAME'
        )
    )
);

$personalTypes['REFERENCE_ID'] = [];

while ($personalType = $rs->fetch()) {
    if (!in_array($personalType['ID'], $personalTypes['REFERENCE_ID'])) {
        $personalTypes['REFERENCE_ID'][] = $personalType['ID'];
        $personalTypes['REFERENCE'][] = '[' . $personalType['ID'] . '] ' . $personalType['NAME'];
    }
}

$orderProps = ['REFERENCE_ID' => [], 'REFERENCE' => []];

$rs = OrderPropsTable::getList();
while ($prop = $rs->fetch()) {
    $orderProps['REFERENCE_ID'][] = $prop['ID'];
    $orderProps['REFERENCE'][] = '[' . $prop['ID'] . ']' . '[' . $prop['CODE'] . '] ' . $prop['NAME'];
}


$headerPrintList = ['REFERENCE_ID' => [], 'REFERENCE' => []];
array_walk(Config::getHeaderList(), function ($item) use (&$headerPrintList) {
    $headerPrintList['REFERENCE_ID'][] = $item['ID'];
    $headerPrintList['REFERENCE'][] = $item['NAME'];
});

// Tabs
$arTabs = array(
    // Main
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit1'),
        'ICON' => '',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit1'),
        'SORT' => '10'
    ),
    // Documents
    array(
        'DIV' => 'edit2',
        'TAB' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit2'),
        'ICON' => '',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit2'),
        'SORT' => '10'
    ),
    //Banners
    array(
        'DIV' => 'edit3',
        'TAB' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit3'),
        'ICON' => '',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit3'),
        'SORT' => '10'
    ),
    //Catalog
    array(
        'DIV' => 'edit4',
        'TAB' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit4'),
        'ICON' => '',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit4'),
        'SORT' => '10'
    ),
);

if ($isB2BRegister) {
    $arTabs[] = [
        'DIV' => 'edit_register_profiles',
        'TAB' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit_register_profiles'),
        'ICON' => '',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_edit_register_profiles_title'),
        'SORT' => '10'
    ];
}

// Groups
$arGroups = array(
    // Main
    'OPTION_5' => array(
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_5'),
        'TAB' => 1
    ),
    'OPTION_15' => array(
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_15'),
        'TAB' => 1
    ),
    'OPTION_PAGE_ADDRESS' => array(
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_PAGE_ADDRESS'),
        'TAB' => 1
    ),

    // Documents
    'OPTION_20' => array(
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_20'),
        'TAB' => 2
    ),

    //Banners
    'OPTION_25' => array(
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_25'),
        'TAB' => 3
    ),
    //catalog
    'OPTION_30' => array(
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_30'),
        'TAB' => 4
    )
);


$arOptions = array(
    'VERSION_TEMPLATE' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_VERSION_TEMPLATE'),
        'TYPE' => 'SELECT',
        'REFRESH' => 'Y',
        'SORT' => '1',
        'VALUES' => [
            'REFERENCE' => [
                Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_VERSION_TEMPLATE_V1'),
                Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_VERSION_TEMPLATE_V2'),
            ],
            'REFERENCE_ID' => [
                "v1",
                "v2",
            ]
        ],
        'NOTES' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_VERSION_TEMPLATE_NOTES'),
    ),
    'LOGO' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_LOGO'),
        'TYPE' => 'IMG',
        'REFRESH' => 'N',
        'MAX_COUNT' => 1,
        'DESCRIPTION' => 'N',
        'SORT' => '10',
    ),
    'LINK_FROM_LOGO' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_LINK_FROM_LOGO'),
        'TYPE' => 'STRING',
        'DEFAULT' => '/',
        'SORT' => '20',
    ),
    'OPT_BLANK_GROUPS' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => GetMessage(SotbitB2bCabinet::MODULE_ID . '_OPT_BLANK_GROUPS'),
        'TYPE' => 'MSELECT',
        'SORT' => '22',
        'VALUES' => $groups
    ),
    'OPT_ACCESS_GROUPS' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => GetMessage(SotbitB2bCabinet::MODULE_ID . '_OPT_ACCESS_GROUPS'),
        'TYPE' => 'SELECT',
        'SORT' => '23',
        'VALUES' => $access
    ),
    'BUYER_PERSONAL_TYPE' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_BUYER_PERSONAL_TYPE'),
        'TYPE' => 'MSELECT',
        'REFRESH' => 'Y',
        'SORT' => '30',
        'VALUES' => $personalTypes
    ),
    'INPUT_MASK' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_INPUT_MASK'),
        'TYPE' => 'STRING',
        'SORT' => '35',
        'DEFAULT' => '+7 999 999 99 99'
    ),
    'PRICE_FOR_NOT_AUTHORIZED_USER' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_PRICE_FOR_NOT_AUTHORIZED_USER'),
        'TYPE' => 'SELECT',
        'SORT' => '40',
        'VALUES' => $priceGroup
    ),
    'SHOW_MAX_QUANTITY_FOR_NOT_AUTHORIZED_USER' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_SHOW_MAX_QUANTITY_FOR_NOT_AUTHORIZED_USER'),
        'TYPE' => 'SELECT',
        'SORT' => '40',
        'VALUES' => [
            'REFERENCE' => [
                Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_SHOW_MAX_QUANTITY_N'),
                Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_SHOW_MAX_QUANTITY_Y'),
                Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_SHOW_MAX_QUANTITY_M'),
            ],
            'REFERENCE_ID' => [
                "N",
                "Y",
                "M",
            ]
        ]
    ),
    'ALERT_FOR_NOT_AUTHORIZED_USER' => array(
        'GROUP' => 'OPTION_5',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_ALERT_FOR_NOT_AUTHORIZED_USER'),
        'TYPE' => 'TEXT',
        'SORT' => '40',
    ),
    'PROFILE_ORG_INN' => array(
        'GROUP' => 'OPTION_15',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_PROFILE_ORG_INN'),
        'TYPE' => 'MSELECT',
        'SORT' => '30',
        'VALUES' => $orderFieldsIds
    ),
    'PROFILE_ORG_NAME' => array(
        'GROUP' => 'OPTION_15',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_PROFILE_ORG_NAME'),
        'TYPE' => 'MSELECT',
        'SORT' => '30',
        'VALUES' => $orderFieldsIds
    ),
    'BASKET_URL' => array(
        'GROUP' => 'OPTION_PAGE_ADDRESS',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_BASKET_URL'),
        'TYPE' => 'STRING',
        'SORT' => '30',
        'SIZE' => '50',
        'DEFAULT' => '/b2bcabinet/orders/make/'
    ),
    'ADDRESS_COMPANY' => array(
        'GROUP' => 'OPTION_PAGE_ADDRESS',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_ADDRESS_COMPANY'),
        'TYPE' => 'STRING',
        'SORT' => '40',
        'SIZE' => '50',
    ),
    'ADDRESS_ORDER' => array(
        'GROUP' => 'OPTION_PAGE_ADDRESS',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_ADDRESS_ORDER'),
        'TYPE' => 'STRING',
        'SORT' => '50',
        'SIZE' => '50',
    ),

    // Documents
    'DOCUMENT_IBLOCKS_TYPE' => array(
        'GROUP' => 'OPTION_20',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_' . Document::IBLOCKS_TYPE),
        'TYPE' => 'SELECT',
        'REFRESH' => 'Y',
        'SORT' => '10',
        'VALUES' => $arIBlockTypeSel
    ),

    // Banners
    'BANNERS_IBLOCKS_TYPE' => array(
        'GROUP' => 'OPTION_25',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPT_IBLOCK_TYPE'),
        'TYPE' => 'SELECT',
        'REFRESH' => 'Y',
        'SORT' => '10',
        'VALUES' => $arIBlockTypeSel
    ),

    'CATALOG_IBLOCK_TYPE' => array(
        'GROUP' => 'OPTION_30',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_IBLOCK_TYPE'),
        'TYPE' => 'SELECT',
        'REFRESH' => 'Y',
        'SORT' => '10',
        'VALUES' => $arIBlockTypeSel
    ),

    'CATALOG_IBLOCK_ID' => array(
        'GROUP' => 'OPTION_30',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_IBLOCK_ID'),
        'TYPE' => 'SELECT',
        'SORT' => '15',
        'VALUES' => getIblockCodeList($arCurrentValues["CATALOG_IBLOCK_TYPE"])
    ),

    'CATALOG_SECTION_ROOT_TEMPLATE' => array(
        'GROUP' => 'OPTION_30',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_SECTION_ROOT_TEMPLATE'),
        'TYPE' => 'SELECT',
        'VALUES' => [
            'REFERENCE' => [
                Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_SECTION_ROOT_TEMPLATE_SECTIONS_LIST'),
                Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_SECTION_ROOT_TEMPLATE_PRODUCTS_LIST'),
            ],
            'REFERENCE_ID' => [
                "SECTIONS_LIST",
                "PRODUCTS_LIST",
            ],
        ],
        'SORT' => '20',
    ),

    'CATALOG_FILE_STORAGE_TIME' => array(
        'GROUP' => 'OPTION_30',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_FILE_STORAGE_TIME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '86400',
        'SORT' => '25',
    ),

    'CATALOG_REPLACE_LINKS' => array(
        'GROUP' => 'OPTION_30',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_REPLACE_LINKS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => '86400',
        'SORT' => '30',
        'NOTES' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_REPLACE_LINKS_NOTES'),
    ),
    'CATALOG_REPLACEABLE_LINKS_VALUE' => array(
        'GROUP' => 'OPTION_30',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . 'CATALOG_REPLACEABLE_LINKS_VALUE'),
        'TYPE' => 'STRING',
        'DEFAULT' => "/catalog/",
        'SORT' => '30',
    ),
    'CATALOG_REPLACE_LINKS_VALUE' => array(
        'GROUP' => 'OPTION_30',
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_REPLACE_LINKS_VALUE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Config::getMethodInstall($siteID) == 'AS_TEMPLATE' ? "/b2bcabinet/orders/blank_zakaza/" : "/orders/blank_zakaza/",
        'SORT' => '30',
    ),    
);

if ($isB2BRegister) {
    //register profiles settings
    $arGroups['OPTION_REGISTER'] = [
        'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_REGISTER'),
        'TAB' => 5,
        'FILE' => '/admin/b2bcabinet_register_settings.php'
    ];

    $arPersonType = unserialize(Config::get('BUYER_PERSONAL_TYPE', $siteID));
    if ($arPersonType) {
        foreach ($arPersonType as $personId) {
            $arPersonRegisterOptions = [
                'GROUP_FIELDS_' . $personId => [
                    'TYPE' => 'MSELECT',
                    'FROM_FILE' => 'Y'
                ],
                'USER_DOP_FIELDS_' . $personId => [
                    'TYPE' => 'MSELECT',
                    'FROM_FILE' => 'Y'
                ],
                'GROUP_REQUIRED_FIELDS_' . $personId => [
                    'TYPE' => 'MSELECT',
                    'FROM_FILE' => 'Y'
                ],
                'GROUP_ORDER_FIELDS_' . $personId => [
                    'TYPE' => 'MSELECT',
                    'FROM_FILE' => 'Y'
                ],
                'GROUP_UNIQUE_FIELDS_' . $personId => [
                    'TYPE' => 'SELECT',
                    'FROM_FILE' => 'Y'
                ],
            ];

            $arOptions = array_merge($arOptions, $arPersonRegisterOptions);
        }
    }
}


if (Config::getMethodInstall($siteID) == 'AS_TEMPLATE') {
    $arOptions = array_merge(
        $arOptions,
        [
            'PATH' => [
                'GROUP' => 'OPTION_5',
                'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_PATH'),
                'TYPE' => 'STRING',
                'SORT' => '5',
            ],
        ]
    );
}

if ($arCurrentValues['VERSION_TEMPLATE'] !== 'v2') {
    $arGroups = array_merge(
        $arGroups,
        [
            'OPTION_HEADER' => array(
                'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_HEADER'),
                'TAB' => 1
            ),
            'OPTION_MENU' => array(
                'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPTION_MENU'),
                'TAB' => 1
            ),
        ]
    );

    $arOptions = array_merge(
        $arOptions,
        [
            'HEADER_TYPE' => array(
                'GROUP' => 'OPTION_HEADER',
                'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_HEADER_TYPE'),
                'TYPE' => 'SELECT',
                'SORT' => '55',
                'VALUES' => $headerPrintList
            ),
            'MENU_POSITION' => array(
                'GROUP' => 'OPTION_MENU',
                'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_MENU_POSITION'),
                'TYPE' => 'SELECT',
                'VALUES' => [
                    'REFERENCE' => [
                        Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_MENU_POSITION_LEFT'),
                        Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_MENU_POSITION_RIGHT'),
                    ],
                    'REFERENCE_ID' => [
                        "LEFT",
                        "RIGHT",
                    ],
                ]
            ),
        ]
    );
} else {
    $arGroups = array_merge(
        $arGroups,
        [
            'OPTION_OFFERS' => array(
                'TITLE' => Loc::getMessage( SotbitB2bCabinet::MODULE_ID . '_OPTION_OFFERS' ),
                'TAB' => 4
            ),
        ]
    );

    $arOptions = array_merge(
        $arOptions,
        [
            'CATALOG_VIEW_OFFERS_VALUE' => array(
                'GROUP' => 'OPTION_OFFERS',
                'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_VIEW_OFFERS_VALUE'),
                'TYPE' => 'SELECT',
                'VALUES' => [
                    'REFERENCE' => [
                        Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_VIEW_OFFERS_TEMPLATE_BLOCK'),
                        Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_VIEW_OFFERS_TEMPLATE_LIST'),
                        Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_CATALOG_VIEW_OFFERS_TEMPLATE_COMBINED'),
                    ],
                    'REFERENCE_ID' => [
                        "BLOCK",
                        "LIST",
                        "COMBINED",
                    ],
                ],
                'SORT' => '10',
            )
        ]
    );
}

// Documents
if ($arCurrentValues[Document::IBLOCKS_TYPE]) {
    $arOptions = array_merge($arOptions, [
        'DOCUMENT_IBLOCKS_ID' => array(
            'GROUP' => 'OPTION_20',
            'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_' . Document::IBLOCKS_ID),
            'TYPE' => 'MSELECT',
            'SORT' => '20',
            'VALUES' => getIblockCodeList($arCurrentValues[Document::IBLOCKS_TYPE])
        ),
    ]);
}

// Banners
if ($arCurrentValues["BANNERS_IBLOCKS_TYPE"]) {
    $arOptions = array_merge($arOptions, [
        'BANNERS_IBLOCKS_ID' => array(
            'GROUP' => 'OPTION_25',
            'TITLE' => Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_OPT_IBLOCK_ID'),
            'TYPE' => 'SELECT',
            'SORT' => '20',
            'VALUES' => getIblockCodeList($arCurrentValues["BANNERS_IBLOCKS_TYPE"])
        ),
    ]);
}

if (SotbitB2bCabinet::returnDemo() == 2) {
    ?>
    <div class="adm-info-message-wrap adm-info-message-red">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?= Loc::getMessage(SotbitB2bCabinet::MODULE_ID . "_MS_DEMO") ?></div>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
    <?
}

if (SotbitB2bCabinet::returnDemo() == 3 || SotbitB2bCabinet::returnDemo() == 0) {
    ?>
    <div class="adm-info-message-wrap adm-info-message-red">
        <div class="adm-info-message">
            <div class="adm-info-message-title"><?= Loc::getMessage(SotbitB2bCabinet::MODULE_ID . "_MS_DEMO_END") ?></div>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
    <?
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
    return '';
}

$RIGHT = $APPLICATION->GetGroupRight(SotbitB2bCabinet::MODULE_ID);

if ($RIGHT != "D") {


    $showRightsTab = false;
    $opt = new CModuleOptions(SotbitB2bCabinet::MODULE_ID, $arTabs, $arGroups, $arOptions, $showRightsTab);

    $opt->ShowHTML();


}

$APPLICATION->SetTitle(Loc::getMessage(SotbitB2bCabinet::MODULE_ID . '_TITLE_SETTINGS'));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");