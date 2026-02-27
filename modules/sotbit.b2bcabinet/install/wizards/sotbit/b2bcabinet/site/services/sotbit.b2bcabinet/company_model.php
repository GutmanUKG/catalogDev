<?
if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true )
    die();

use Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc;

use Bitrix\Sale\Internals\PersonTypeTable;

Loc::loadMessages(__FILE__);

$module = 'sotbit.b2bcabinet';
CModule::includeModule('sale');
CModule::includeModule($module);
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");



function createOrder(array $arData, $iblockId, $userID, $personType, $siteId)
{
    $arSelect = Array("ID", "NAME");
    $arFilter = Array("IBLOCK_ID"=>$iblockId, "ACTIVE"=>"Y", "CATALOG_AVAILABLE" => "Y");
    $dbProducts = CIBlockElement::GetList(Array('rand' => 'ASC'), $arFilter, false, Array("nPageSize"=>3), $arSelect);

    $currency = \Bitrix\Currency\CurrencyManager::getBaseCurrency();
    $basket = \Bitrix\Sale\Basket::create($siteId);

    while ($product = $dbProducts->fetch()) {
        $item = $basket->createItem('catalog', $product["ID"]);
        $item->setFields(array(
            'QUANTITY' => rand(1,5),
            'LID' => $siteId,
            'NAME' => $product['NAME'],
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider'
        ));
    }


    if($order = \Bitrix\Sale\Order::create($siteId, $userID, $currency)) {
        $order->setPersonTypeId($personType);
        $order->setBasket($basket);
        $order->setField("STATUS_ID", "N");

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $service = \Bitrix\Sale\Delivery\Services\Manager::getById(1);

        $shipment->setFields(array(
            'DELIVERY_ID' => $service['ID'],
            'DELIVERY_NAME' => $service['NAME'],
        ));

        $shipment->setStoreId(1); // ID ������������ ������ ������
        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        foreach ($basket as $item) {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem();
        $payment->setField('SUM', $order->getPrice());
        $paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById(1);
        if ($paySystemService) {
            $payment->setFields(array(
                'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
                'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
            ));
        }

        $propertyCollection = $order->getPropertyCollection();

        $propertyCodeToId = array();
        foreach ($propertyCollection as $propertyValue) {
            $propertyCodeToId[$propertyValue->getField('CODE')] = $propertyValue->getField('ORDER_PROPS_ID');
        }

        foreach ($arData as $code => $prop) {
            if($prop && $propertyCodeToId[$code]){
                $propertyValue = $propertyCollection->getItemByOrderPropertyId($propertyCodeToId[$code]);
                $propertyValue->setValue($prop);
            }
        }

        $order->doFinalAction(true);
        $result = $order->save();
        $orderId = $order->getId();

        \CSaleOrderUserProps::DoSaveUserProfile($userID, '', '', $personType, $orderId, $errors);

        return $orderId;
    }
}



function getProductIblock()
{
    set_time_limit(0);
    $iblockCode = "sotbit_b2bcabinet_catalog_" . WIZARD_SITE_ID;
    $iblockType = "sotbit_b2bcabinet_type_catalog";
    $iblockXMLID = 'sotbit_b2bcabinet_catalog_' . WIZARD_SITE_ID;

    $rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockXMLID, "TYPE" => $iblockType));
    $IBLOCK_CATALOG_ID = false;
    if ($arIBlock = $rsIBlock->Fetch())
    {
        return $arIBlock["ID"];
    }
}

function setDefaultPropsName($personTypeId)
{
    $dbPropName = \Bitrix\Sale\Internals\OrderPropsTable::getList( array(
        'filter' => array(
            'ACTIVE' => 'Y',
            'PERSON_TYPE_ID' => $personTypeId,
            'IS_PROFILE_NAME' => 'Y'
        ),
        'select' => array('ID','CODE','PERSON_TYPE_ID', 'NAME')
    ) );

    while($propName = $dbPropName->fetch())
    {
        Option::set('sotbit.auth', 'COMPANY_PROPS_NAME_FIELD_' . $propName["PERSON_TYPE_ID"], $propName["CODE"], WIZARD_SITE_ID);
    }
}

function getOrderProps()
{
    $dbSaleOrderProps = CSaleOrderProps::GetList(
        [], [], false, false, ['ID', 'CODE', 'PERSON_TYPE_ID']
    );

    while($resProps = $dbSaleOrderProps->fetch()){
        $saleProps[$resProps['PERSON_TYPE_ID']][$resProps['CODE']] = $resProps;
    }

    return $saleProps;
}

function getLanguage()
{
    $dbSite = CSite::GetByID( WIZARD_SITE_ID );
    if($arSite = $dbSite->Fetch())
        return $arSite["LANGUAGE_ID"];
}

function getLocation()
{
    $lang = getLanguage();
    $dbLocation = \Bitrix\Sale\Location\LocationTable::getList( array(
        'filter' => array(
            '=NAME.LANGUAGE_ID' => $lang,
            '=NAME.NAME' => GetMessage("WZD_LOCATION"),
            '=TYPE.CODE' => 'CITY'
        ),
        'select' => array(
            'CODE'
        )
    ) )->fetch();

    if ($dbLocation)
        return $dbLocation['CODE'];
}

function createCompany($user, $personType, $num)
{
    $saleProps = getOrderProps();
    $location = getLocation();
    foreach ($saleProps[$personType] as $key=>$prop) {
        $arProp = Array(
            "ID" => $prop["ID"],
            "NAME" => $prop["NAME"],
            "CODE" => $prop["CODE"],
            "VALUE" => ""
        );

        if (
            stripos($key, "FIO") !== false ||
            stripos($key, "CONTACT_PERSON") !== false ||
            stripos($key, "NAME") !== false ||
            stripos($key, "COMPANY") !== false
        ) {
            $arProp["VALUE"] = GetMessage("WZD_COMPANY_NAME_".$num);
        }
        if (stripos($key, "ADDRESS") !== false || stripos($key, "COMPANY_ADR") !== false)
            $arProp["VALUE"] = GetMessage("WZD_COMPANY_ADDRESS_".$num);
        if (stripos($key, "EMAIL") !== false)
            $arProp["VALUE"] = GetMessage("WZD_COMPANY_EMAIL_".$num);
        if (stripos($key, "PHONE") !== false)
            $arProp["VALUE"] = GetMessage("WZD_COMPANY_PHONE_".$num);
        if (stripos($key, "ZIP") !== false)
            $arProp["VALUE"] = GetMessage("WZD_COMPANY_ZIP_".$num);
        if (stripos($key, "LOCATION") !== false)
            $arProp["VALUE"] = $location;
        if (stripos($key, "INN") !== false)
            $arProp["VALUE"] = GetMessage("WZD_COMPANY_INN_".$num);
        if (stripos($key, "KPP") !== false)
            $arProp["VALUE"] = GetMessage("WZD_COMPANY_KPP_".$num);
        if (stripos($key, "CONFIDENTIAL") !== false)
            $arProp["VALUE"] = 'Y';

        $arProps[$arProp['CODE']] = $arProp['VALUE'];
    }

    $arProps['ACTIVE'] = 'ACTIVE';
    $arProps['STATUS'] = 'A';

    $company = new Sotbit\Auth\Company\Company(WIZARD_SITE_ID);
    $arProps["COMPANY_ID"] = $company->addCompany($arProps, $personType, $user);

    return $arProps;
}


function addStaff($companyId, $num)
{
    $user = new \CUser;
    $company = new Sotbit\Auth\Company\Company(WIZARD_SITE_ID);

    $staffPass = randString(10);
    $companyStaffFields_1 = Array(
        "NAME"              => GetMessage( "WZD_COMPANY_STAFF_". $num ."_NAME" ),
        "LAST_NAME"         => GetMessage( "WZD_COMPANY_STAFF_". $num ."_LAST_NAME" ),
        "EMAIL"             => GetMessage( "WZD_COMPANY_STAFF_". $num ."_EMAIL" ),
        "LOGIN"             => GetMessage( "WZD_COMPANY_STAFF_". $num ."_LOGIN" ),
        "PASSWORD"          => $staffPass,
        "CONFIRM_PASSWORD"  => $staffPass,
    );

    if ($staffId_1 = $user->Add($companyStaffFields_1)) {
        if ($num == 3) {
            $status = "N";
        }
        else {
            $status = "Y";
        }
        $company->addStaff($companyId, $staffId_1, 'STAFF', $status);
    }
}


if (WIZARD_INSTALL_DEMO_DATA) {

    if (CModule::IncludeModule("sotbit.auth")) {

        $IBLOCK_CATALOG_ID = getProductIblock();

        $dbPerson = CSalePersonType::GetList( array(), array(
            "LID" => WIZARD_SITE_ID
        ) );

        while ( $arPerson = $dbPerson->Fetch() ) {
            $arPersonTypeNames[$arPerson["ID"]] = $arPerson["NAME"];
            $personTypeId[] = $arPerson['ID'];
        }

        $idUr = array_search( GetMessage( "WZD_PERSON_TYPE_UR" ), $arPersonTypeNames );
        $idIp = array_search( GetMessage( "WZD_PERSON_TYPE_IP" ), $arPersonTypeNames );

        setDefaultPropsName($personTypeId);

        $user = new \CUser;

        $adminPass = randString(10);
        $companyAdminFields = Array(
            "NAME"              => GetMessage( "WZD_COMPANY_ADMIN_NAME" ),
            "LAST_NAME"         => GetMessage( "WZD_COMPANY_ADMIN_LAST_NAME" ),
            "EMAIL"             => GetMessage( "WZD_COMPANY_ADMIN_EMAIL" ),
            "LOGIN"             => GetMessage( "WZD_COMPANY_ADMIN_LOGIN" ),
            "PASSWORD"          => $adminPass,
            "CONFIRM_PASSWORD"  => $adminPass,
        );

        $adminId = $user->Add($companyAdminFields);
        $personType = $idUr ?: $idIp;

        if (is_numeric($adminId) && $personType) {

            $resultCompanyFields_1 = createCompany($adminId, $personType, 1);

            if (is_numeric($resultCompanyFields_1["COMPANY_ID"])) {
                $orderId = createOrder($resultCompanyFields_1, $IBLOCK_CATALOG_ID, $adminId, $personType, WIZARD_SITE_ID);
                $userAdd_1 = addStaff($resultCompanyFields_1["COMPANY_ID"], 1);
            }

            $resultCompanyFields_2 = createCompany($adminId, $personType, 2);

            if (is_numeric($resultCompanyFields_2["COMPANY_ID"])) {
                $orderId = createOrder($resultCompanyFields_2, $IBLOCK_CATALOG_ID, $adminId, $personType, WIZARD_SITE_ID);
                $userAdd_2 = addStaff($resultCompanyFields_2["COMPANY_ID"], 2);
                $userAdd_3 = addStaff($resultCompanyFields_2["COMPANY_ID"], 3);
            }
        }
    }

    if ($IBLOCK_CATALOG_ID) {

        $draft = new Sotbit\B2BCabinet\Draft\Draft (WIZARD_SITE_ID);
        $orderTemplate = new Sotbit\B2BCabinet\OrderTemplate\OrderTemplate (WIZARD_SITE_ID);
        $item = 1;

        $arSelect = Array("ID");
        $arFilter = Array("IBLOCK_ID"=>$IBLOCK_CATALOG_ID, "ACTIVE"=>"Y");
        $dbProducts = CIBlockElement::GetList(Array('rand' => 'ASC'), $arFilter, false, Array("nPageSize"=>15), $arSelect);

        $companies = [];
        if ($resultCompanyFields_1["COMPANY_ID"] && $resultCompanyFields_2["COMPANY_ID"]) {
            $companies = [$resultCompanyFields_1["COMPANY_ID"], $resultCompanyFields_2["COMPANY_ID"]];
        }

        while($product = $dbProducts->fetch())
        {
            if ($item <= 5) {
                $productDraftFields[] = [
                    'PRODUCT_ID' => $product["ID"],
                    'QUANTITY' => rand(1,15)
                ];
            }
            else {
                $productOrderTemplateFields[] = [
                    'ID' => $product["ID"],
                    'QUANTITY' => rand(1,15)
                ];
            }
            $item ++;
        }

        $draft->add(GetMessage("WZD_DRAFT_NAME"), $productDraftFields, $adminId ?: 1);
        $orderTemplate->add(
            [
                'NAME' => GetMessage("WZD_ORDERTEMPLATE_NAME"),
                "USER_ID" => $adminId ?: 1,
                "SITE_ID" => WIZARD_SITE_ID,
                "SAVED" => 'Y',
            ],
            $productOrderTemplateFields,
            []
        );
    }

}

?>