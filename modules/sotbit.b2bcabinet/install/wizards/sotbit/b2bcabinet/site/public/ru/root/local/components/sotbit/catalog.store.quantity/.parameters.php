<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var string $componentPath
 * @var string $componentName
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 */

use Bitrix\Main,
    Bitrix\Main\Loader;

if (!Loader::includeModule('catalog'))
    return;

global $USER_FIELD_MANAGER;

$arStore = array();
$storeIterator = CCatalogStore::GetList(
    array(),
    array('ISSUING_CENTER' => 'Y'),
    false,
    false,
    array('ID', 'TITLE')
);

while ($store = $storeIterator->Fetch())
    $arStore[$store['ID']] = "[" . $store['ID'] . "] " . $store['TITLE'];
unset($store, $storeIterator);

$propertyUF = array();
$userFields = $USER_FIELD_MANAGER->GetUserFields('CAT_STORE', 0, LANGUAGE_ID);


foreach ($userFields as $fieldName => $userField)
    $propertyUF[$fieldName] = $userField["LIST_COLUMN_LABEL"] ? $userField["LIST_COLUMN_LABEL"] : $fieldName;

$arComponentParameters = array(
    'GROUPS' => array(
        'STORE' => array(
            'NAME' => GetMessage('SC_CSQ_GROUP_STORE')
        )
    ),
    'PARAMETERS' => array(
        'ELEMENT_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('SC_CSQ_PARAMS_ELEMENT_ID'),
            'TYPE' => 'STRING',
        ),
        'USE_STORE' => array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('SC_CSQ_PARAMS_USE_STORE'),
            'TYPE' => 'CHECKBOX',
            'REFRESH' => 'Y',
            'DEFAULT' => "N",
        ),
        'SHOW_MAX_QUANTITY' => array(
            'PARENT' => 'VISUAL',
            'NAME' => GetMessage('SC_CSQ_PARAMS_SHOW_MAX_QUANTITY'),
            'TYPE' => 'LIST',
            'REFRESH' => 'Y',
            'MULTIPLE' => 'N',
            'VALUES' => array(
                'Y' => GetMessage('SC_CSQ_SHOW_MAX_QUANTITY_Y'),
                'M' => GetMessage('SC_CSQ_SHOW_MAX_QUANTITY_M')
            ),
            'DEFAULT' => array('N'),
        ),
        'MESS_SHOW_MAX_QUANTITY' => array(
            'PARENT' => 'VISUAL',
            'NAME' => GetMessage('SC_CSQ_PARAMS_MESS_SHOW_MAX_QUANTITY'),
            'TYPE' => 'STRING',
            'DEFAULT' => GetMessage('SC_CSQ_SHOW_MAX_QUANTITY_DEFAULT')
        ),
        "CACHE_TIME" => array("DEFAULT" => 36000000),
    )
);

if (isset($arCurrentValues['SHOW_MAX_QUANTITY'])) {
    if ($arCurrentValues['SHOW_MAX_QUANTITY'] === 'M') {
        $arComponentParameters['PARAMETERS']['RELATIVE_QUANTITY_FACTOR'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => GetMessage('SC_CSQ_PARAMS_RELATIVE_QUANTITY_FACTOR'),
            'TYPE' => 'STRING',
            'DEFAULT' => '5'
        );
        $arComponentParameters['PARAMETERS']['MESS_RELATIVE_QUANTITY_MANY'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => GetMessage('SC_CSQ_PARAMS_RELATIVE_QUANTITY_MANY'),
            'TYPE' => 'STRING',
            'DEFAULT' => GetMessage('SC_CSQ_MESS_RELATIVE_QUANTITY_MANY_DEFAULT')
        );
        $arComponentParameters['PARAMETERS']['MESS_RELATIVE_QUANTITY_FEW'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => GetMessage('SC_CSQ_PARAMS_MESS_RELATIVE_QUANTITY_FEW'),
            'TYPE' => 'STRING',
            'DEFAULT' => GetMessage('SC_CSQ_MESS_RELATIVE_QUANTITY_FEW_DEFAULT')
        );
    }
}

$arComponentParameters['PARAMETERS']['MESS_NOT_AVAILABLE'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => GetMessage('SC_CSQ_PARAMS_MESS_NOT_AVAILABLE'),
    'TYPE' => 'STRING',
    'DEFAULT' => GetMessage('SC_CSQ_PARAMS_MESS_NOT_AVAILABLE_DEFAULT')
);

if ($arCurrentValues['USE_STORE'] === 'Y') {

    $arComponentParameters['PARAMETERS']['STORES'] = array(
        'PARENT' => 'STORE',
        'NAME' => GetMessage('SC_CSQ_PARAMS_STORE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arStore,
        'ADDITIONAL_VALUES' => 'Y'
    );
    $arComponentParameters['PARAMETERS']['STORE_FIELDS'] = array(
        'NAME' => GetMessage('SC_CSQ_PARAM_STORE_FIELDS'),
        'PARENT' => 'STORE',
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => array(
            'TITLE' => GetMessage('SC_CSQ_STORE_TITLE'),
            'ADDRESS' => GetMessage('SC_CSQ_STORE_ADDRESS'),
            'DESCRIPTION' => GetMessage('SC_CSQ_STORE_DESCRIPTION'),
            'PHONE' => GetMessage('SC_CSQ_STORE_PHONE'),
            'EMAIL' => GetMessage('SC_CSQ_STORE_EMAIL'),
            'IMAGE_ID' => GetMessage('SC_CSQ_STORE_IMAGE_ID'),
            'COORDINATES' => GetMessage('SC_CSQ_STORE_COORDINATES'),
            'SCHEDULE' => GetMessage('SC_CSQ_STORE_SCHEDULE')
        )
    );
    $arComponentParameters['PARAMETERS']['STORE_PROPERTIES'] = array(
        "PARENT" => "STORE",
        "NAME" => GetMessage("SC_CSQ_PARAM_STORE_PROPERTIES"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "ADDITIONAL_VALUES" => "Y",
        "VALUES" => $propertyUF,
    );
}