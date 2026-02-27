<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\UI\Filter;
use Sotbit\B2bCabinet\Helper;
use Bitrix\Main\Localization\Loc;
use Sotbit\B2BCabinet\Controller\FileController;

$PROPERTY_ARTICLE = 'PROPERTY_' . ($arParams['ARTICLE_PROPERTY_CODE'] ?: 'CML2_ARTICLE') . '_VALUE';
$arResult["PROPERTY_ARTICLE"] = $PROPERTY_ARTICLE;

$db_propsGroup = CSaleOrderPropsGroup::GetList(
    [
        "SORT" => "ASC"
    ],
    [
        "PERSON_TYPE_ID" => $arResult["PERSON_TYPE_ID"]
    ],
    false,
    false,
    [
        "ID",
        "NAME",
        "SORT"
    ]
);

while ($propsGroup = $db_propsGroup->Fetch()) {
    $arPropsGroup[$propsGroup["ID"]] = $propsGroup;
}

$arResult["FULL_QUANTITY"] = 0;
$docIblockId = Helper\Document::getIblocks();

foreach ($arResult['ORDER_PROPS'] as $key => $ORDER_PROP) {
    $arPropsGroup[$ORDER_PROP["PROPS_GROUP_ID"]]["PROPS"][] = $ORDER_PROP;
    $arResult['ORDER_PROPS'][$ORDER_PROP['CODE']] = $ORDER_PROP;
    unset($arResult['ORDER_PROPS'][$key]);
}

$arResult["PRINT_ORDER_PROPS"] = $arPropsGroup;

$propsCompanyName = \Bitrix\Main\Config\Option::get("sotbit.auth",
    "COMPANY_PROPS_NAME_FIELD_" . $arResult["PERSON_TYPE_ID"], "", SITE_ID);

if (isset($arResult['ORDER_PROPS'][$propsCompanyName]) && !empty($arResult['ORDER_PROPS'][$propsCompanyName])) {
    $companyName = $arResult['ORDER_PROPS'][$propsCompanyName]["VALUE"];
} else {
    $companyName = (isset($arResult['ORDER_PROPS']['FIO']) && !empty($arResult['ORDER_PROPS']['FIO']) ?
        $arResult['ORDER_PROPS']['FIO']['VALUE'] : $arResult['ORDER_PROPS']['COMPANY']['VALUE']);
}

// ----- PRODUCTS
if (!empty($arResult['BASKET'])) {
    $filterOption = new Filter\Options('PRODUCT_LIST');
    $filterData = array();
    $filterData = $filterOption->getFilter([]);

    $productFilter = [
        'ID',
        'NAME',
        'ARTICLE',
        'QUANTITY',
        'PRICE',
        'SUM',
        'FIND'
    ];
    $filter = [];

    $useReplace = \Bitrix\Main\Config\Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
    if ($useReplace) {
        $replaceableValue = \Bitrix\Main\Config\Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
        $replaceValue = \Bitrix\Main\Config\Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);
    }

    foreach ($arResult['BASKET'] as $key => &$item) {
        $arResult["FULL_QUANTITY"] += $item['QUANTITY'];
        $item['DETAIL_PAGE_URL'] = $useReplace ?  str_replace($replaceableValue, $replaceValue, $item['DETAIL_PAGE_URL']) : $item['DETAIL_PAGE_URL'];

        if ($filterData) {
            foreach ($filterData as $key => $value) {
                if (in_array($key, $productFilter)) {
                    $filter[$key] = $value;
                }
            }
        }
        $needContinue = false;
        foreach ($filter as $key => $value) {
            $sum = $item['QUANTITY'] * $item['PRICE'];
            $sum = (string)$sum;

            if ($key == 'SUM' && $sum != $value) {
                if (strpos($sum, $value) === false) {
                    $needContinue = true;
                }
            }
            if ($key == 'PRICE') {
                if (strpos((string)$item['BASE_PRICE'], $value) === false) {
                    $needContinue = true;
                }
            } elseif ($key == 'NAME' || ($key == 'FIND' && !empty($value))) {
                if (strpos(strtolower($item['NAME']), strtolower($value)) === false) {
                    $needContinue = true;
                }
            } elseif ($key == 'ARTICLE') {
                if (strpos($item[$PROPERTY_ARTICLE], $value) === false) {
                    $needContinue = true;
                }
            } elseif ($key != 'SUM' && $item[$key] != $value) {
                $needContinue = true;
                break;
            }
        }

        if ($needContinue) {
            continue;
        }

        $productCols = [
            'ID' => $item['ID'],
            'ARTICLE' => $item[$PROPERTY_ARTICLE],
            'NAME' => $item['NAME'],
            'QUANTITY' => $item['QUANTITY'],
            'DISCOUNT' => $item['DISCOUNT_PRICE_PERCENT_FORMATED'],
            'PRICE' => $item['BASE_PRICE_FORMATED'],
            'SUM' => $item['FORMATED_SUM'],
        ];

        $arResult['PRODUCT_ROWS'][] = [
            'data' => [
                'ID' => $item['ID'],
                'ARTICLE' => $item[$PROPERTY_ARTICLE],
                'NAME' => $item['NAME'],
                'QUANTITY' => $item['QUANTITY'],
                'DISCOUNT' => $item['DISCOUNT_PRICE_PERCENT_FORMATED'],
                'PRICE' => $item['BASE_PRICE_FORMATED'],
                'SUM' => $item['FORMATED_SUM'],
            ],
            'actions' => [],
            'COLUMNS' => $productCols,
            'editable' => true,
        ];
    }
}
// ----- PRODUCTS_2
if (!empty($arResult['BASKET'])) {
    $filterOption = new Bitrix\Main\UI\Filter\Options('PRODUCT_LIST_2');
    $filterData = $filterOption->getFilter([]);

    $productFilter = [
        'ID',
        'NAME',
        'ARTICLE',
        'QUANTITY',
        'SUM',
        'FIND'
    ];
    $filter = [];
    foreach ($arResult['BASKET'] as $key => &$item) {

        if ($filterData) {
            foreach ($filterData as $key => $value) {
                if (in_array($key, $productFilter)) {
                    $filter[$key] = $value;
                }
            }
        }
        $needContinue = false;

        foreach ($filter as $key => $value) {
            $sum = $item['QUANTITY'] * $item['PRICE'];
            $sum = (string)$sum;

            if ($key == 'SUM' && $sum != $value) {
                if (strpos($sum, $value) === false) {
                    $needContinue = true;
                }
            } elseif ($key == 'NAME' || ($key == 'FIND' && !empty($value))) {
                if (strpos(strtolower($item['NAME']), strtolower($value)) === false) {
                    $needContinue = true;
                }
            } elseif ($key == 'ARTICLE') {
                if (strpos($item[$PROPERTY_ARTICLE], $value) === false) {
                    $needContinue = true;
                }
            } elseif ($key != 'SUM' && $item[$key] != $value) {
                $needContinue = true;
                break;
            }
        }

        if ($needContinue) {
            continue;
        }

        $productCols = [
            'ID' => $item['ID'],
            'ARTICLE' => $item[$PROPERTY_ARTICLE],
            'NAME' => $item['NAME'],
            'QUANTITY' => $item['QUANTITY'],
            'SUM' => $item['QUANTITY'] * $item['PRICE']
        ];

        $arResult['PRODUCT_2_ROWS'][] = [
            'data' => [
                'ID' => $item['ID'],
                'ARTICLE' => $item[$PROPERTY_ARTICLE],
                'NAME' => $item['NAME'],
                'QUANTITY' => $item['QUANTITY'],
                'SUM' => $item['FORMATED_SUM']
            ],
            'actions' => [],
            'COLUMNS' => $productCols,
            'editable' => true,
        ];
    }
}

// ----- DOCS
if (!empty($docIblockId) && !empty($arResult['ID'])) {
    $filterOption = new Filter\Options('DOCUMENTS_LIST');
    $filterData = array();
    $filterData = $filterOption->getFilter([]);

    $productFilter = [
        'NUMBER',
        'DOC',
        'DATE_CREATED_from',
        'DATE_CREATED_to',
        'DATE_UPDATED_from',
        'DATE_UPDATED_to',
        'DATE_UPDATE',
        'ORGANIZATION',
        'FIND'
    ];
    $filter = [];

    $arRes = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => $docIblockId, 'PROPERTY_ORDER' => $arResult['ACCOUNT_NUMBER']],
        false,
        false,
        ['ID', 'NAME', 'DATE_CREATE', 'TIMESTAMP_X', 'DETAIL_TEXT', 'PROPERTY_DOCUMENT']
    );
    while ($doc = $arRes->Fetch()) {
        if ($filterData) {
            foreach ($filterData as $key => $value) {
                if (in_array($key, $productFilter)) {
                    $filter[$key] = $value;
                }
            }
        }
        $needContinue = false;

        foreach ($filter as $key => $value) {
            if ($key == 'NUMBER' && $doc['ID'] != $value) {
                $needContinue = true;
            } elseif ($key == 'DOC' || $key == 'FIND') {
                if (strpos($doc['NAME'], $value) === false) {
                    $needContinue = true;
                }
            } elseif (in_array(strtolower($key), ['date_created_from', 'date_created_to'])) {
                $date = strtotime($doc['DATE_CREATE']);

                $start = $filter['DATE_CREATED_from'];
                $end = $filter['DATE_CREATED_to'];
                if ($date < strtotime($start) || $date > strtotime($end)) {
                    $needContinue = true;
                    break;
                }
            } elseif (in_array(strtolower($key), ['date_updated_from', 'date_updated_to'])) {
                $date = strtotime($doc['TIMESTAMP_X']);

                $start = $filter['DATE_UPDATED_from'];
                $end = $filter['DATE_UPDATED_to'];
                if ($date < strtotime($start) || $date > strtotime($end)) {
                    $needContinue = true;
                    break;
                }
            } elseif ($item[$key] != $value) {
                $needContinue = true;
                break;
            }
        }

        if ($needContinue) {
            continue;
        }

        // Url document file
        if (!empty($doc['PROPERTY_DOCUMENT_VALUE'])) {
            $doc['PROPERTY_DOCUMENT_VALUE'] = Bitrix\Main\FileTable::getById($doc['PROPERTY_DOCUMENT_VALUE'])->fetch();
        }

        $docUrl = FileController::urlGenerate(
            'fileDownload',
            [
                'fileId' => $doc['PROPERTY_DOCUMENT_VALUE']['ID'],
                'fileName' => addslashes($doc['NAME']),
            ],
        );
        $docsCols = [
            'ID' => $doc['ID'],
            'NUMBER' => $doc['ID'],
            'DOC' => '<a href="' . $docUrl . '">' . $doc['NAME'] . '</a>',
            'DATE_CREATED' => $doc['DATE_CREATE'],
            'DATE_UPDATED' => $doc['TIMESTAMP_X'],
            'ORGANIZATION' => $doc['DETAIL_TEXT']
        ];

        $arResult['DOCS_ROWS'][] = [
            'data' => [
                'ID' => $doc['ID'],
                'NUMBER' => $doc['ID'],
                'DOC' => '<a href="' . $docUrl . '">' . $doc['NAME'] . '</a>',
                'DATE_CREATED' => $doc['DATE_CREATE'],
                'DATE_UPDATED' => $doc['TIMESTAMP_X'],
            ],
            'actions' => [
                array(
                    "TEXT" => (!empty($doc['PROPERTY_DOCUMENT_VALUE']) ? Loc::getMessage('SPOD_DOWNLOAD_DOC') : Loc::getMessage('SPOD_DOWNLOAD_DOC_NOT_FOUND')),
                    "ONCLICK" => "window.location.href='" . $docUrl . "'",
                    "DEFAULT" => true,
                ),
            ],
            'COLUMNS' => $docsCols,
            'editable' => true,
        ];
    }
}

// ----- PAYMENT_SYSTEMS
if (!empty($arResult['PAYMENT'])) {
    $filterOption = new Filter\Options('PAY_SYSTEMS_LIST');
    $filterData = array();
    $filterData = $filterOption->getFilter([]);

    $productFilter = [
        'NUMBER',
        'DOC',
        'DATE_CREATED_from',
        'DATE_CREATED_to',
        'DATE_UPDATED_from',
        'DATE_UPDATED_to',
        'DATE_UPDATE',
        'ORGANIZATION',
        'FIND'
    ];
    $filter = [];

    if ($filterData) {
        foreach ($filterData as $key => $value) {
            if (in_array($key, $productFilter)) {
                $filter[$key] = $value;
            }
        }
    }
    $needContinue = false;


    foreach ($arResult['PAYMENT'] as $payment) {
        foreach ($filter as $key => $value) {
            if ($key == 'NUMBER' && $payment['ID'] != $value) {
                $needContinue = true;
            } elseif ($key == 'DOC' || $key == 'FIND') {
                if (strpos($payment['PAY_SYSTEM']['NAME'], $value) === false) {
                    $needContinue = true;
                }
            } elseif (in_array(strtolower($key), ['date_created_from', 'date_created_to'])) {
                $date = strtotime($payment['DATE_BILL']);

                $start = $filter['DATE_CREATED_from'];
                $end = $filter['DATE_CREATED_to'];
                if ($date < strtotime($start) || $date > strtotime($end)) {
                    $needContinue = true;
                    break;
                }
            } elseif (in_array(strtolower($key), ['date_updated_from', 'date_updated_to'])) {
                $date = strtotime($payment['DATE_BILL']);

                $start = $filter['DATE_UPDATED_from'];
                $end = $filter['DATE_UPDATED_to'];
                if ($date < strtotime($start) || $date > strtotime($end)) {
                    $needContinue = true;
                    break;
                }
            } elseif ($payment[$key] != $value) {
                $needContinue = true;
                break;
            }
        }

        $pSystemCols = [
            'ID' => $payment['ID'],
            'NUMBER' => $payment['ACCOUNT_NUMBER'],
            'NAME' => $payment['NAME'],
            'DATE_CREATED' => $arResult['DATE_INSERT_FORMATED'],
            //'DATE_UPDATED' => $payment['DATE_UPDATE_FORMATED'],
            'SUM' => $payment['PRICE_FORMATED'],
            'IS_PAID' => ($payment['PAID'] && $payment['PAID'] == "Y" ? GetMessage("SPOL_PAYMENT_IS_PAID_Y")
                : GetMessage("SPOL_PAYMENT_IS_PAID_N")),
            'ORGANIZATION' => (
            isset($arResult['ORDER_PROPS']['FIO']) && !empty($arResult['ORDER_PROPS']['FIO']) ?
                $arResult['ORDER_PROPS']['FIO']['NAME'] : $arResult['ORDER_PROPS']['COMPANY']['NAME']
            )
        ];

        $arResult['PAY_SYSTEM_ROWS'][] = [
            'data' => [
                'ID' => $payment['ID'],
                'NUMBER' => $payment['ACCOUNT_NUMBER'],
                'NAME' => $payment['PAY_SYSTEM']['NAME'],
                'DATE_CREATED' => $arResult['DATE_INSERT_FORMATED'],
                //'DATE_UPDATED' => $payment['DATE_UPDATE_FORMATED'],
                'SUM' => $payment['PRICE_FORMATED'],
                'IS_PAID' => ($payment['PAID'] && $payment['PAID'] == "Y" ? GetMessage("SPOL_PAYMENT_IS_PAID_Y")
                    : GetMessage("SPOL_PAYMENT_IS_PAID_N")),
                'ORGANIZATION' => $companyName
            ],
            'actions' => [],
            'COLUMNS' => $pSystemCols,
            'editable' => true,
        ];
    }
}

if (Loader::includeModule('support')) {
    $tickets = CTicket::GetList(
        $by = "ID",
        $order = "asc",
        array('UF_ORDER' => $arResult['ID'], 'CREATED_BY' => $USER->GetID()),
        $isFiltered,
        "Y",
        "Y",
        "Y",
        SITE_ID,
        array()
    );

    if (!empty($tickets)) {
        $arResult['TICKET'] = $tickets->fetch();
    }
}

if (Loader::includeModule('sotbit.complaints') &&
    Option::get('sotbit.complaints', 'COMPLAINTS_WITH_ORDER', '', SITE_ID) == "ORDER" &&
    $complaintsIblock = Option::get('sotbit.complaints', 'IBLOCK_COMPLAINTS_ID', '', SITE_ID)) {
    $complaintsPath = Option::get('sotbit.complaints', 'COMPLAINTS_PATH', '', SITE_ID);

    $arSelect = Array("ID", "NAME", "PROPERTY_COMPLAINT_STATUS", "PROPERTY_ORDER_ID");
    $arFilter = Array("IBLOCK_ID"=>$complaintsIblock, "ACTIVE"=>"Y", "PROPERTY_ORDER_ID"=> $arResult['ID']);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    while($val = $res->fetch())
    {
        $arResult['COMPLAINTS_ROW'][] = [
            'data' => [
                'ID' => $val['ID'],
                'NAME' => $val['NAME'],
                'STATUS' => $val['PROPERTY_COMPLAINT_STATUS_VALUE']
            ],
            'actions' => [
                [
                    'TEXT'    => Loc::getMessage('COMPLAINTS_OPEN'),
                    'ONCLICK' => 'document.location.href="'. $complaintsPath .'detail/'. $val['ID'] .'/"',
                ],
            ]
        ];

    }


}
?>