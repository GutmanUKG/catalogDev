<?

use Bitrix\Main\UserTable;
use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\StoreProductTable;

use Bitrix\Main\EventManager;

use Bitrix\Main\Localization\Loc;




Loader::includeModule('sale');
\Bitrix\Main\Loader::registerNamespace(
    'Onelab\\',
    \Bitrix\Main\Loader::getDocumentRoot() . '/local/classes/onelab/'
);

\Bitrix\Main\Loader::includeModule("iblock");

function artDebug($data) {
    try{
        global $USER;
        if ($USER->GetId() == 13) {
            \Bitrix\Main\Diag\Debug::dumpToFile($data, '', '../../debug.log');
        }
    } catch (Exception $e) {
        \Bitrix\Main\Diag\Debug::dumpToFile($e, '', '../../debug.log');
    }
}

require(__DIR__ . '/constants.php');
require(__DIR__ . '/handlers.php');
//require (__DIR__ . '/agents.php');
/*
AddEventHandler("search", "BeforeIndex", "BeforeIndexHandler");

function BeforeIndexHandler($arFields) {
	$arrIblock = array(7); //ID инфоблоков, для которых производить модификацию
	$arDelFields = array("DETAIL_TEXT", "PREVIEW_TEXT"); //стандартные поля, которые нужно исключить
	if (CModule::IncludeModule('iblock') && $arFields["MODULE_ID"] == 'iblock' && in_array($arFields["PARAM2"], $arrIblock) && intval($arFields["ITEM_ID"]) > 0){
		$dbElement = CIblockElement::GetByID($arFields["ITEM_ID"]);
		if ($arElement = $dbElement->Fetch()){
			foreach ($arDelFields as $value){
				if (isset($arElement[$value]) && strlen($arElement[$value]) > 0){
					$arFields["BODY"] = str_replace(CSearch::KillTags($arElement[$value]), "", CSearch::KillTags($arFields["BODY"]));
				}
			}
		}
		return $arFields;
	}
}
*/



function updateProductPriceInDatabase($productId, $priceTypeId, $newPrice) {

    $dbPrice = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID" => $productId,
            "CATALOG_GROUP_ID" => $priceTypeId
        )
    );


    if ($arPrice = $dbPrice->Fetch()) {
        CPrice::Update($arPrice["ID"], array(
            "PRICE" => $newPrice,
            "CURRENCY" => $arPrice["CURRENCY"]
        ));
    } else {

        CPrice::Add(array(
            "PRODUCT_ID" => $productId,
            "CATALOG_GROUP_ID" => $priceTypeId,
            "PRICE" => $newPrice,
            "CURRENCY" => "KZT"
        ));
    }
}

AddEventHandler("catalog", "OnGetOptimalPrice", "OnGetOptimalPriceHandler");

function OnGetOptimalPriceHandler(
    $intProductID,
    $quantity = 1,
    $arUserGroups = array(),
    $renewal = "N",
    $arPrices = array(),
    $siteID = false,
    $arDiscountCoupons = false
) {
    global $USER;
    $userID = $USER->GetID();
    $rsUser = CUser::GetByID($userID);
    $arUser = $rsUser->Fetch();


    $priceTypeOfflineId = 12;
    $priceTypeDealerId = 7;


    $dbProductPrice = CPrice::GetListEx(
        array(),
        array("PRODUCT_ID" => $intProductID),
        false,
        false,
        array("*")
    );


    $arOfflinePrice = null;
    $arDealerPrice = null;


    while ($arProducPrice = $dbProductPrice->GetNext()) {
        if ($arProducPrice['CATALOG_GROUP_ID'] == $priceTypeOfflineId) {
            $arOfflinePrice = $arProducPrice;
        } elseif ($arProducPrice['CATALOG_GROUP_ID'] == $priceTypeDealerId) {
            $arDealerPrice = $arProducPrice;
        }
    }


    if ($arUser["UF_APPLY_PRICE"] == 1 && $arOfflinePrice) {

        updateProductPriceInDatabase($intProductID, $priceTypeOfflineId, $arOfflinePrice['PRICE']);
        return formatPriceArray($arOfflinePrice);
    } elseif ($arUser["UF_APPLY_PRICE"] != 1 && $arDealerPrice) {

        updateProductPriceInDatabase($intProductID, $priceTypeDealerId, $arDealerPrice['PRICE']);
        return formatPriceArray($arDealerPrice);
    }

    return array('PRICE' => array());
}

function formatPriceArray($arPrice) {
    return array(
        'PRICE' => array(
            'ID' => $arPrice['ID'],
            'CATALOG_GROUP_ID' => $arPrice['CATALOG_GROUP_ID'],
            'PRICE' => $arPrice['PRICE'],
            'CURRENCY' => $arPrice['CURRENCY'],
            'ELEMENT_IBLOCK_ID' => $arPrice['PRODUCT_ID'],
        )
    );
}









AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");
function bxModifySaleMails($orderID, &$eventName, &$arFields) {
    global $USER;
    CModule::IncludeModule("sale");
    $arOrder = CSaleOrder::GetByID($orderID);
    $rsUser = CUser::GetByID($arOrder['USER_ID']);

    $arUser = $rsUser->Fetch();

    $dbProps = CSaleOrderPropsValue::GetOrderProps($orderID);
    while ($arProps = $dbProps->Fetch()) {
        if(in_array($arProps['ORDER_PROPS_ID'], [2, 14])){
            $companyName = $arProps['VALUE'];
        }
        // if(in_array($arProps['ORDER_PROPS_ID'], [12, 24])){
        // 	$locationId = $arProps['VALUE']; // приходит цифра местоположения
        // }
        if(in_array($arProps['ORDER_PROPS_ID'], [3, 25])){
            $address = $arProps['VALUE'];
        }

        $arPropsExit[] = $arProps;
    }

    $order = \Bitrix\Sale\Order::load($orderID);

    $propertyCollection = $order->getPropertyCollection();

    $locationCode = false;
    foreach ($propertyCollection as $property) {
        if ($property->getField('CODE') === 'LOCATION') {
            $locationCode = $property->getField('VALUE');
        }
    }

    $location = '';

    if ($locationCode) {
        $item = \Bitrix\Sale\Location\LocationTable::getByCode($locationCode, array(
            'filter' => array('=NAME.LANGUAGE_ID' => 'ru'),// LANGUAGE_ID
            'select' => array('*', 'NAME_RU' => 'NAME.NAME')
        ))->fetch();

        $location = $item['NAME_RU'] ?? '';
    }


    $shipmentCollection = $order->getShipmentCollection()->getNotSystemItems();

    $deliveries = [];
    foreach ($shipmentCollection as $shipment) {
        $deliveries[] = $shipment->getField('DELIVERY_NAME');
    }

    $basket = $order->getBasket();
    if ($basket->getWeight() > 0) {
        $weight = ($basket->getWeight() / 1000) . 'кг';
    } else {
        $weight = '';
    }

    $couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList(array(
        'select' => array('COUPON'),
        'filter' => array('=ORDER_ID' => $order->getId())
    ));

    $coupons = [];
    while ($coupon = $couponList->fetch()) {
        $coupons[] = $coupon['COUPON'];
    }

    // $dbBasketItems = CSaleBasket::GetList(array(
    // 	"NAME" => "ASC", "ID" => "ASC"), array("ORDER_ID" => $orderID), false, false,
    // 	array("ID", "NAME", "PRODUCT_ID", "QUANTITY", "PRICE", "DISCOUNT_PRICE")
    // );

    $res = CIBlockElement::GetProperty(16, $arUser['UF_MANAGER'], array(), array("CODE" => "EMAIL"));
    while ($ob = $res->GetNext()) {
        $managerEmail = $ob['VALUE'];
    }

    $arFields['MANAGER_EMAIL'] = $managerEmail;

    $result = array_merge([
        'ONELAB_COMPANY_NAME' => $companyName,
        'ONELAB_LOCATION'     => $location,
        'ONELAB_ADDRESS'      => $address,
        'ONELAB_DELIVERIES'   => implode(',', $deliveries),
        'ONELAB_WEIGHT'       => $weight,
        'ONELAB_COUPONS'      => implode(',', $coupons),
        'ONELAB_COMMENT'      => $arOrder['USER_DESCRIPTION']
    ], $arFields);

    // $arFieldsAdd = Array(
    // 	"USER_ID" => $arOrder['USER_ID'],
    // 	"USER_FIO" => $fio,
    // 	"USER_LOGIN" => $arUser['LOGIN'],
    // 	"USER_PHONE" => $phone,
    // 	"USER_ADDRESS" => $address,
    // 	"USER_COMMENT" => $arOrder['USER_DESCRIPTION']
    // );
    // $arFields["ORDER_LIST"]=$table;
    // $result = array_merge($arFieldsAdd, $arFields);

    // ob_start();
    // echo date('Y.m.d H:i:s') . PHP_EOL;
    // print_r($arFields) . PHP_EOL;
    // $str = ob_get_contents();
    // ob_clean();
    // file_put_contents($_SERVER['DOCUMENT_ROOT'].'/makslog.log', $str, FILE_APPEND);

    $event = new CEvent;
    $eventResponse = $event->Send("SALE_NEW_ORDER_2", SITE_ID, $result, "N", 112);
}

AddEventHandler("sale", "OnOrderCancelSendEmail", "bxModifySaleMailCansel");
function bxModifySaleMailCansel($orderID, &$eventName, &$arFields) {
    global $USER;
    CModule::IncludeModule("sale");
    $arOrder = CSaleOrder::GetByID($orderID);
    $rsUser = CUser::GetByID($arOrder['USER_ID']);

    $arUser = $rsUser->Fetch();

    $res = CIBlockElement::GetProperty(16, $arUser['UF_MANAGER'], array(), array("CODE" => "EMAIL"));
    while ($ob = $res->GetNext()) {
        $managerEmail = $ob['VALUE'];
    }

    $arFields['MANAGER_EMAIL'] = $managerEmail;

    $event = new CEvent;
    $eventResponse = $event->Send("SALE_ORDER_CANCEL", SITE_ID, $arFields, "N", 113);
}





class MyFacet extends \Bitrix\Iblock\PropertyIndex\Facet {
    public function query(array $filter, array $facetTypes = array(), $facetId = 0) {
        $connection = \Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();

        $facetFilter = $this->getFacetFilter($facetTypes);
        if (!$facetFilter)
        {
            return false;
        }

        if ($filter)
        {
            $filter["IBLOCK_ID"] = $this->iblockId;

            global $searchFilter;

            $filter = array_merge($filter, $searchFilter);

            $element = new \CIBlockElement;
            $element->strField = "ID";
            $element->prepareSql(array("ID"), $filter, false, false);
            $elementFrom = $element->sFrom;
            $elementWhere = $element->sWhere;
        }
        else
        {
            $elementFrom = "";
            $elementWhere = "";
        }

        $facets = array();
        if ($facetId)
        {
            $facets[] = array(
                "where" => $this->getWhere($facetId),
                "facet" => array($facetId),
            );
        }
        else
        {
            foreach ($facetFilter as $facetId)
            {
                $where = $this->getWhere($facetId);

                $found = false;
                foreach ($facets as $i => $facetWhereAndFacets)
                {
                    if ($facetWhereAndFacets["where"] == $where)
                    {
                        $facets[$i]["facet"][] = $facetId;
                        $found = true;
                        break;
                    }
                }

                if (!$found)
                {
                    $facets[] = array(
                        "where" => $where,
                        "facet" => array($facetId),
                    );
                }
            }
        }

        $sqlUnion = array();
        foreach ($facets as $facetWhereAndFacets)
        {
            $where = $facetWhereAndFacets["where"];
            $facetFilter = $facetWhereAndFacets["facet"];

            $sqlSearch = array("1=1");

            if (empty($where))
            {
                $sqlUnion[] = "
					SELECT
						F.FACET_ID
						,F.VALUE
						,MIN(F.VALUE_NUM) MIN_VALUE_NUM
						,MAX(F.VALUE_NUM) MAX_VALUE_NUM
						".($connection instanceof \Bitrix\Main\DB\MysqlCommonConnection
                        ?",MAX(case when LOCATE('.', F.VALUE_NUM) > 0 then LENGTH(SUBSTRING_INDEX(F.VALUE_NUM, '.', -1)) else 0 end)"
                        :",MAX(".$sqlHelper->getLengthFunction("ABS(F.VALUE_NUM) - FLOOR(ABS(F.VALUE_NUM))")."+1-".$sqlHelper->getLengthFunction("0.1").")"
                    )." VALUE_FRAC_LEN
						,COUNT(DISTINCT F.ELEMENT_ID) ELEMENT_COUNT
					FROM
						".($elementFrom
                        ?$elementFrom."
							INNER JOIN ".$this->storage->getTableName()." F ON BE.ID = F.ELEMENT_ID"
                        :$this->storage->getTableName()." F"
                    )."
					WHERE
						F.SECTION_ID = ".$this->sectionId."
						and F.FACET_ID in (".implode(",", $facetFilter).")
						".$elementWhere."
					GROUP BY
						F.FACET_ID, F.VALUE
				";
                continue;
            }
            elseif (count($where) == 1)
            {
                $fcJoin = "INNER JOIN ".$this->storage->getTableName()." FC on FC.ELEMENT_ID = BE.ID";
                foreach ($where as $facetWhere)
                {
                    $sqlWhere = $this->whereToSql($facetWhere, "FC");
                    if ($sqlWhere)
                        $sqlSearch[] = $sqlWhere;
                }
            }
            elseif (count($where) <= 5)
            {
                $subJoin = "";
                $subWhere = "";
                $i = 0;
                foreach ($where as $facetWhere)
                {
                    if ($i == 0)
                        $subJoin .= "FROM ".$this->storage->getTableName()." FC$i\n";
                    else
                        $subJoin .= "INNER JOIN ".$this->storage->getTableName()." FC$i ON FC$i.ELEMENT_ID = FC0.ELEMENT_ID\n";

                    $sqlWhere = $this->whereToSql($facetWhere, "FC$i");
                    if ($sqlWhere)
                    {
                        if ($subWhere)
                            $subWhere .= "\nAND ".$sqlWhere;
                        else
                            $subWhere .= $sqlWhere;
                    }

                    $i++;
                }
                $fcJoin = "
					INNER JOIN (
						SELECT FC0.ELEMENT_ID
						$subJoin
						WHERE
						$subWhere
					) FC on FC.ELEMENT_ID = BE.ID
				";
            }
            else
            {
                $condition = array();
                foreach ($where as $facetWhere)
                {
                    $sqlWhere = $this->whereToSql($facetWhere, "FC0");
                    if ($sqlWhere)
                        $condition[] = $sqlWhere;
                }
                $fcJoin = "
						INNER JOIN (
							SELECT FC0.ELEMENT_ID
							FROM ".$this->storage->getTableName()." FC0
							WHERE FC0.SECTION_ID = ".$this->sectionId."
							AND (
							(".implode(")OR(", $condition).")
							)
						GROUP BY FC0.ELEMENT_ID
						HAVING count(DISTINCT FC0.FACET_ID) = ".count($condition)."
						) FC on FC.ELEMENT_ID = BE.ID
					";
            }

            $sqlUnion[] = "
				SELECT
					F.FACET_ID
					,F.VALUE
					,MIN(F.VALUE_NUM) MIN_VALUE_NUM
					,MAX(F.VALUE_NUM) MAX_VALUE_NUM
					".($connection instanceof \Bitrix\Main\DB\MysqlCommonConnection
                    ?",MAX(case when LOCATE('.', F.VALUE_NUM) > 0 then LENGTH(SUBSTRING_INDEX(F.VALUE_NUM, '.', -1)) else 0 end)"
                    :",MAX(".$sqlHelper->getLengthFunction("ABS(F.VALUE_NUM) - FLOOR(ABS(F.VALUE_NUM))")."+1-".$sqlHelper->getLengthFunction("0.1").")"
                )." VALUE_FRAC_LEN
					,COUNT(DISTINCT F.ELEMENT_ID) ELEMENT_COUNT
				FROM
					".$this->storage->getTableName()." F
					INNER JOIN (
						SELECT BE.ID
						FROM
							".($elementFrom
                    ?$elementFrom
                    :"b_iblock_element BE"
                )."
							".$fcJoin."
						WHERE ".implode(" AND ", $sqlSearch)."
						".$elementWhere."
					) E ON E.ID = F.ELEMENT_ID
				WHERE
					F.SECTION_ID = ".$this->sectionId."
					and F.FACET_ID in (".implode(",", $facetFilter).")
				GROUP BY
					F.FACET_ID, F.VALUE
			";
        }

        $result = $connection->query(implode("\nUNION ALL\n", $sqlUnion));

        return $result;
    }
}

function resizeImg($img, $height, $width)
{
    $renderImage = CFile::ResizeImageGet(
        $img,
        array("width" => $width, "height" => $height)
    );
    return $renderImage;
}
function dump($arr)
{
    global $USER;
    if ($USER->IsAdmin()){
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}
const CATALOG_IBLOCK_ID = 7;
const BRANDS_IBLOCK_ID = 20;

AddEventHandler("iblock", "OnAfterIBlockElementAdd", 'OnAfterIBlockElementAddHandler');

function OnAfterIBlockElementAddHandler(&$arFields){
    if($arFields['IBLOCK_ID'] == CATALOG_IBLOCK_ID){
        $arSelect = Array(
            "ID",
            "NAME",
            "IBLOCK_ID",
            //"PROPERTY_BRANDS",
            //"PROPERTY_DLYA_BRENDA",
            "PROPERTY_BREND_ATTR_S"

        );
        $arFilter = [
            'IBLOCK_ID'=> CATALOG_IBLOCK_ID
        ];
        $res = CIBlockElement::GetList(
            Array(),
            $arFilter,
            false,
            false,
            $arSelect
        );
        $arrBrand = [];
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            // dump($arFields);
            if(!empty($arFields['PROPERTY_BREND_ATTR_S_VALUE']) && !in_array($arFields['PROPERTY_BREND_ATTR_S_VALUE'], $arrBrand)){
                $arrBrand[] = $arFields['PROPERTY_BREND_ATTR_S_VALUE'];

            }

        }
        addNewElementBrand($arrBrand);
    }
}
function  addNewElementBrand($arrBrand){
    foreach ($arrBrand as $brandName){
        //dump($brandName);
        $arFilter = [
            'IBLOCK_ID' => BRANDS_IBLOCK_ID,
            'NAME' => $brandName
        ];
        $res = CIBlockElement::GetList([], $arFilter, false, false, ['ID']);
        if ($res->SelectedRowsCount() == 0) {
            // Если элемента с таким именем нет, добавляем новый элемент
            $arFields = [
                "IBLOCK_ID" => BRANDS_IBLOCK_ID,
                "NAME" => $brandName,
                "ACTIVE" => "Y" // элемент активен
            ];

            $el = new CIBlockElement;
            if ($el->Add($arFields)) {
                echo "Элемент с именем $brandName успешно добавлен.<br>";
            } else {
                echo "Ошибка при добавлении элемента с именем $brandName: " . $el->LAST_ERROR . "<br>";
            }
        } else {
            // echo "Элемент с именем $brandName уже существует.<br>";
        }
    }

}
function AgentUpdateStoreQuantity()
{
    // file_put_contents(__DIR__ . '/log.txt', "Агент стартовал \n", FILE_APPEND);
    if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
        return __FUNCTION__ . '();';
    }

    $iblockId = 7;
    $storeId = 51;
    $propertyCode = 'STORE_51';
    $ids = [];
    $res = \CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'],
        false,
        false,
        ['ID']
    );
    while($row = $res->Fetch()){
        $ids[] = (int)$row['ID'];
    }
    if(!$ids){
        return __FUNCTION__ . '();';
    }

    $amountByProduct = [];
    $spRes = StoreProductTable::GetList([
        'select' => ['PRODUCT_ID', 'AMOUNT'],
        'filter' => [
            '=STORE_ID' => $storeId,
            '@PRODUCT_ID' => $ids,
        ]
    ]);
    while ($sp = $spRes->Fetch()) {
        $pid = (int)$sp['PRODUCT_ID'];
        $amountByProduct[$pid] = (int)$sp['AMOUNT'];
    }
    foreach ($ids as $productId){
        $amount = isset($amountByProduct[$productId]) ? (int)$amountByProduct[$productId] : 0.0;
        if ($amount < 0) { $amount = 0.0; }
        \CIBlockElement::SetPropertyValuesEx(
            $productId,
            $iblockId,
            [$propertyCode => $amount]
        );
    }
    return __FUNCTION__ . '();';
}
// AddEventHandler("sale", "OnSaleOrderSaved", "setOfflinePriceTypeOnOrderSaved", 10000);

// function setOfflinePriceTypeOnOrderSaved($ID, $arFields)
// {
//     file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", "OnSaleOrderSaved вызван для заказа ID: $ID\n", FILE_APPEND);
// }

//AddEventHandler(
//    "main",
//    "OnBeforeEventAdd",
//    "myFeedbackHandler"
//);

function myFeedbackHandler(&$event, &$lid, &$arFields, &$message_id)
{
    if ($event !== "FEEDBACK_FORM") {
        return;
    }
    $FEEDBACK_IBLOCK_ID = 36;
    $el = new CIBlockElement;
    $dataTime = date("Y-m-d H:i:s");
    $arLoadProductArray = array(
        // элемент лежит в корне раздела
        "IBLOCK_ID" => $FEEDBACK_IBLOCK_ID,
        "PROPERTY_VALUES" => [
            'EMAILS' => $arFields['EMAIL_TO'],
            "DATA" => $dataTime,
            "AUTHOR" => $arFields['AUTHOR'],
            "AUTHOR_EMAIL" => $arFields['AUTHOR_EMAIL']
        ],
        "NAME" => "Обратная связь " . $dataTime,
        "ACTIVE" => "Y",            // активен
        "PREVIEW_TEXT" => $arFields['TEXT'],
    );

    if ($PRODUCT_ID = $el->Add($arLoadProductArray))
        echo "New ID: " . $PRODUCT_ID;
    else
        echo "Error: " . $el->LAST_ERROR;


}
// Кастомное условие "Товар из ИБ 37 привязан к складу" для правил корзины
// Автозагрузка класса — нужна при выполнении сгенерированного кода скидок
spl_autoload_register(function ($className) {
    if ($className === 'CCondCtrlProductOnWarehouse') {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/sale_discount/condition_warehouse.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});



function debugFront($arr)
{
    global $USER;
    if($USER->isAdmin()){
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}

function debugFile($arr)
{
    global $USER;
    if($USER->isAdmin()){
        file_put_contents(__DIR__ . '/log.txt', print_r($arr, true), FILE_APPEND);
    }
}


// Регистрация условия через D7 EventManager (как в рабочем примере o2k.ru)
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandlerCompatible(
    'sale',
    'OnCondSaleActionsControlBuildList',
    ['CCondCtrlProductOnWarehouse', 'GetControlDescr']
);

// Вариант 2: фиксируем цену markdown-строки корзины по складу (CUSTOM_PRICE=Y).
// Это убирает зависимость от выбора "первой подходящей" акции движком скидок.
$markdownCustomPriceFile = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/sale_discount/markdown_custom_price.php';
if (file_exists($markdownCustomPriceFile)) {
    require_once $markdownCustomPriceFile;
    AddEventHandler('sale', 'OnBeforeBasketAdd', ['MarkdownBasketCustomPrice', 'onBeforeBasketAdd']);
    AddEventHandler('sale', 'OnBeforeBasketUpdate', ['MarkdownBasketCustomPrice', 'onBeforeBasketUpdate']);
    $eventManager->addEventHandlerCompatible(
        'sale',
        'OnSaleBasketItemBeforeSaved',
        ['MarkdownBasketCustomPrice', 'onSaleBasketItemBeforeSaved']
    );
}
