<?
if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true )
    die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

use Bitrix\Sale\Internals\PersonTypeTable;

Loc::loadMessages(__FILE__);

$module = 'sotbit.b2bcabinet';
CModule::includeModule('sale');
CModule::includeModule($module);
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$testUserId = Option::get($module,'TEST_USER_ID', '', WIZARD_SITE_ID);
$methodInstall = Option::get($module,'method_install', '', WIZARD_SITE_ID);

// Documents
$documentIblock = unserialize(Option::get($module, 'DOCUMENT_IBLOCKS_ID', 'a:0:{}', WIZARD_SITE_ID), ['allowed_classes' => false]);
if(!empty($documentIblock)) {
    $docs = CIBlockElement::GetList(
        Array("SORT"=>"ASC"),
        Array('IBLOCK_ID' => $documentIblock, 'SITE_ID' => WIZARD_SITE_ID),
        false,
        false,
        Array('PROPERTY_USER', 'ID', 'IBLOCK_ID')
    );
    while ($doc = $docs->fetch())
    {
        CIBlockElement::SetPropertyValuesEx($doc['ID'], $doc['IBLOCK_ID'], array('USER' => $testUserId));
    }
}


$APPLICATION->SetGroupRight('support', 6, 'R', false);

$logoFilePath = $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sotbit.b2bcabinet/install/wizards/sotbit/b2bcabinet/site/services/sotbit.b2bcabinet/img/B2B_logo.svg';

if (IsModuleInstalled('sotbit.b2bplus')) {
    $logoFilePath = $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sotbit.b2bcabinet/install/wizards/sotbit/b2bcabinet/site/services/sotbit.b2bcabinet/img/B2B_plus_logo.svg';
}

if (IsModuleInstalled('sotbit.b2bportal')) {
    $logoFilePath = $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sotbit.b2bcabinet/install/wizards/sotbit/b2bcabinet/site/services/sotbit.b2bcabinet/img/B2B_portal_logo.svg';
}

$logoFileId = CFile::SaveFile(
    CFile::MakeFileArray($logoFilePath),
    "sotbit_b2bcabinet"
);

Option::set($module, 'PATH', 'b2bcabinet', WIZARD_SITE_ID);
Option::set($module, 'LOGO', $logoFileId, WIZARD_SITE_ID);
Option::set($module, 'OPT_BLANK_GROUPS', serialize([1]), WIZARD_SITE_ID);
$basketURL = $methodInstall == 'AS_TEMPLATE' ? WIZARD_SITE_DIR . "b2bcabinet/orders/make/" : WIZARD_SITE_DIR ."orders/make/";
Option::set($module, 'BASKET_URL', $basketURL, WIZARD_SITE_ID);
Option::set($module, 'CATALOG_SHOW_SECTIONS', "MENU", WIZARD_SITE_ID);
Option::set($module, 'VERSION_TEMPLATE', "v2", WIZARD_SITE_ID);
Option::set($module, 'CATALOG_VIEW_OFFERS_VALUE', "COMBINED", WIZARD_SITE_ID);

$personalTypes = array();
$rs = PersonTypeTable::getList(
    array(
        'select' => array(
            'ID',
            'NAME'
        ),
        'filter' => array('ACTIVE' => 'Y')
    ));
while($personalType = $rs->fetch())
{
    $result[] = $personalType['ID'];
}

if(isset($result) && !empty($result))
{
    Option::set( $module, 'BUYER_PERSONAL_TYPE', serialize($result), '', WIZARD_SITE_ID);
}

if(CModule::includeModule('sotbit.bill')) {
    $arPaySystems[] = array(
        'PAYSYSTEM' => array(
            "NAME" => Loc::getMessage('SETTINGS_B2B_BILL_FOR_PAY'),
            "SORT" => 100,
            "DESCRIPTION" => Loc::getMessage('SETTINGS_B2B_BILL_DESCRIPTION'),
            "PSA_NAME" => Loc::getMessage('SETTINGS_B2B_BILL_FOR_PAY'),
            "ACTION_FILE" => "billsotbit",
            "RESULT_FILE" => "",
            "NEW_WINDOW" => "Y",
            "HAVE_PAYMENT" => "Y",
            "HAVE_ACTION" => "N",
            "HAVE_RESULT" => "N",
            "HAVE_PREPAY" => "N",
            "HAVE_RESULT_RECEIVE" => "N"
        )
    );

    $billLogo = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/sale/sale_payments/billsotbit.png';
    if (file_exists($billLogo)) {
        $lastKey = end(array_keys($arPaySystems));
        $arPictureParam = CFile::MakeFileArray($billLogo);
        if (is_array($arPictureParam)) {
            $arPaySystems[$lastKey]['PAYSYSTEM']['LOGOTIP'] = $arPictureParam;
            $arPaySystems[$lastKey]['PAYSYSTEM']['LOGOTIP']['MODULE_ID'] = "sale";
            CFile::SaveForDB($arPaySystems[$lastKey]['PAYSYSTEM'], 'LOGOTIP', 'sale/paysystem/logotip');
        }
    }

    foreach ($arPaySystems as $paySystem) {
        $saleSystemID = '';

        $dbRes = \Bitrix\Sale\PaySystem\Manager::getList(array(
            'select' => array(
                "ID",
                "NAME",
                "PAY_SYSTEM_ID"
            ),
            'filter' => array(
                "NAME" => $paySystem['PAYSYSTEM']["NAME"]
            )
        ));

        $tmpPaySystem = $dbRes->fetch();
        if (!$tmpPaySystem) {
            $resultAdd = \Bitrix\Sale\Internals\PaySystemActionTable::add($paySystem['PAYSYSTEM']);

            if ($resultAdd->isSuccess())
            {
                $saleSystemID = $resultAdd->getPrimary();
                $saleSystemID = $saleSystemID['ID'];
            }
        }
        else
        {
            $saleSystemID = $tmpPaySystem['ID'];
        }

        if (!empty($saleSystemID))
        {
            $arFields = array(
                'PAY_SYSTEM_ID' => $saleSystemID,
                'PARAMS' => serialize(array('BX_PAY_SYSTEM_ID' => intval($saleSystemID))),
                'ENTITY_REGISTRY_TYPE' => 'ORDER'
            );

            $resultAdd = \Bitrix\Sale\Internals\PaySystemActionTable::update($saleSystemID, $arFields);
        }
    }
}

Option::set("main", "auth_components_template", "flat", WIZARD_SITE_ID);
Option::set('main','optimize_css_files','N');
Option::set('main','optimize_js_files','N');
Option::set('main','use_minified_assets','N');
Option::set('main','move_js_to_body','N');
Option::set('main','compres_css_js_files','N');


$holder1 = array(
    'GADGETS' => array(
        'PROFILE@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 0,
            'HIDE' => 'N'
        ),
        'HTML_AREA@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 1,
            'HIDE' => 'N'
        ),
        'DELAYBASKET@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 2,
            'HIDE' => 'N'
        ),
        'BASKET@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 3,
            'HIDE' => 'N'
        ),
        'ACCOUNTPAY@' . rand() => array(
            'COLUMN' => 1,
            'ROW' => 0,
            'HIDE' => 'N'
        ),
        'FAVORITES@' . rand() => array(
            'COLUMN' => 1,
            'ROW' => 1,
            'HIDE' => 'N'
        ),
        'SUBSCRIBE@' . rand() => array(
            'COLUMN' => 1,
            'ROW' => 2,
            'HIDE' => 'N'
        ),
        'ORDERS@' . rand() => array(
            'COLUMN' => 2,
            'ROW' => 0,
            'HIDE' => 'N'
        ),
        'REVIEWS@' . rand() => array(
            'COLUMN' => 2,
            'ROW' => 1,
            'HIDE' => 'N'
        ),
        'WEATHER@' . rand() => array(
            'COLUMN' => 2,
            'ROW' => 2,
            'HIDE' => 'N'
        )
    )
);

$holder2 = array(
    'GADGETS' => array(
        'PROFILE@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 0,
            'HIDE' => 'N'
        ),
        'BASKET@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 1,
            'HIDE' => 'N'
        ),
        'DELAYBASKET@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 2,
            'HIDE' => 'N'
        ),
        'HTML_AREA@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 3,
            'HIDE' => 'N'
        ),
        'FAVORITES@' . rand() => array(
            'COLUMN' => 0,
            'ROW' => 4,
            'HIDE' => 'N'
        ),
        'BUYORDER@' . rand() => array(
            'COLUMN' => 1,
            'ROW' => 0,
            'HIDE' => 'N'
        ),
        'BLANK@' . rand() => array(
            'COLUMN' => 1,
            'ROW' => 1,
            'HIDE' => 'N'
        ),
        'BUYERS@' . rand() => array(
            'COLUMN' => 1,
            'ROW' => 2,
            'HIDE' => 'N'
        ),
        'SUBSCRIBE@' . rand() => array(
            'COLUMN' => 1,
            'ROW' => 3,
            'HIDE' => 'N'
        ),
        'ORDERS@' . rand() => array(
            'COLUMN' => 2,
            'ROW' => 0,
            'HIDE' => 'N'
        ),
        'REVIEWS@' . rand() => array(
            'COLUMN' => 2,
            'ROW' => 1,
            'HIDE' => 'N'
        ),
        'WEATHER@' . rand() => array(
            'COLUMN' => 2,
            'ROW' => 2,
            'HIDE' => 'N'
        )
    )
);

CUserOptions::SetOption( "intranet", "~gadgets_holder1", $holder1, true, 0 );
CUserOptions::SetOption( "intranet", "~gadgets_holder2", $holder2, true, 0 );

$result = \Bitrix\Main\UserTable::getList( array(
    'select' => array(
        'ID'
    ),
    'filter' => array()
) );
while ( $User = $result->fetch() ) {
    $holder1 = array(
        'GADGETS' => array(
            'PROFILE@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 0,
                'HIDE' => 'N'
            ),
            'HTML_AREA@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 1,
                'HIDE' => 'N'
            ),
            'DELAYBASKET@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 2,
                'HIDE' => 'N'
            ),
            'BASKET@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 3,
                'HIDE' => 'N'
            ),
            'ACCOUNTPAY@' . rand() => array(
                'COLUMN' => 1,
                'ROW' => 0,
                'HIDE' => 'N'
            ),
            'FAVORITES@' . rand() => array(
                'COLUMN' => 1,
                'ROW' => 1,
                'HIDE' => 'N'
            ),
            'SUBSCRIBE@' . rand() => array(
                'COLUMN' => 1,
                'ROW' => 2,
                'HIDE' => 'N'
            ),
            'ORDERS@' . rand() => array(
                'COLUMN' => 2,
                'ROW' => 0,
                'HIDE' => 'N'
            ),
            'REVIEWS@' . rand() => array(
                'COLUMN' => 2,
                'ROW' => 1,
                'HIDE' => 'N'
            ),
            'WEATHER@' . rand() => array(
                'COLUMN' => 2,
                'ROW' => 2,
                'HIDE' => 'N'
            )
        )
    );

    $holder2 = array(
        'GADGETS' => array(
            'PROFILE@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 0,
                'HIDE' => 'N'
            ),
            'BASKET@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 1,
                'HIDE' => 'N'
            ),
            'DELAYBASKET@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 2,
                'HIDE' => 'N'
            ),
            'HTML_AREA@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 3,
                'HIDE' => 'N'
            ),
            'FAVORITES@' . rand() => array(
                'COLUMN' => 0,
                'ROW' => 4,
                'HIDE' => 'N'
            ),
            'BUYORDER@' . rand() => array(
                'COLUMN' => 1,
                'ROW' => 0,
                'HIDE' => 'N'
            ),
            'BLANK@' . rand() => array(
                'COLUMN' => 1,
                'ROW' => 1,
                'HIDE' => 'N'
            ),
            'BUYERS@' . rand() => array(
                'COLUMN' => 1,
                'ROW' => 2,
                'HIDE' => 'N'
            ),
            'SUBSCRIBE@' . rand() => array(
                'COLUMN' => 1,
                'ROW' => 3,
                'HIDE' => 'N'
            ),
            'ORDERS@' . rand() => array(
                'COLUMN' => 2,
                'ROW' => 0,
                'HIDE' => 'N'
            ),
            'REVIEWS@' . rand() => array(
                'COLUMN' => 2,
                'ROW' => 1,
                'HIDE' => 'N'
            ),
            'WEATHER@' . rand() => array(
                'COLUMN' => 2,
                'ROW' => 2,
                'HIDE' => 'N'
            )
        )
    );
    CUserOptions::SetOption("intranet", "~gadgets_holder1", $holder1, 0, $User['ID']);
    CUserOptions::SetOption("intranet", "~gadgets_holder2", $holder2, 0, $User['ID']);
}
?>