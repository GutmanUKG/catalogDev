<?

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

	$dbProductPrice = CPrice::GetListEx(
		array(),
		array("PRODUCT_ID" => $intProductID),
		false,
		false,
		array("*")
	);
	while ($arProducPrice = $dbProductPrice->GetNext()) {
		if($arProducPrice['CATALOG_GROUP_CODE'] == 'Цена дилерского портала KZT') {
			$arOpt = $arProducPrice;
		}
	}

	if ($arUser["UF_APPLY_PRICE"] == 1) {
		$arPricesNew = [];

		$allProductPrices = \Bitrix\Catalog\PriceTable::getList([
			"select" => ["*"],
			"filter" => [
				"=PRODUCT_ID" => $intProductID,
			],
		])->fetchAll();
		
		foreach ($allProductPrices as $allProductPrice) {
			$arPricesNew[] = $allProductPrice;
		}

		if($arPricesNew[2]["PRICE"] != '') {
			if($arUser["UF_APPLY_PRICE_FOR"] == 8) {
				$arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
			}
			elseif($arUser["UF_APPLY_PRICE_FOR"] == 9) {
				$arBrandNames = [];
				foreach ($arUser["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
					$obBrand = CIBlockElement::GetByID($brandID);
					if($arBrand = $obBrand->GetNext()) {
						$arBrandNames[] = $arBrand['NAME'];
					}
				}
				$res = CIBlockElement::GetProperty(7, $intProductID, "sort", "asc", array("CODE" => "BREND_ATTR_S"));
				if ($ob = $res->GetNext()) {
					$brand = $ob['VALUE'];
				}
				if(in_array($brand, $arBrandNames)) {
					$arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
				}
			}
			elseif($arUser["UF_APPLY_PRICE_FOR"] == 10) {
				$arBrandNames = [];
				foreach ($arUser["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
					$obBrand = CIBlockElement::GetByID($brandID);
					if($arBrand = $obBrand->GetNext()) {
						$arBrandNames[] = $arBrand['NAME'];
					}
				}
				$res = CIBlockElement::GetProperty(7, $intProductID, "sort", "asc", array("CODE" => "BREND_ATTR_S"));
				if ($ob = $res->GetNext()) {
					$brand = $ob['VALUE'];
				}
				if(in_array($brand, $arBrandNames)) {}
				else {
					$arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
				}
			}
			else {
				$arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
			}
		}
	}

	// ob_start();
	// echo date('Y.m.d H:i:s') . PHP_EOL;
	// print_r($arOpt);
	// echo PHP_EOL;
	// $str = ob_get_contents();
	// ob_clean();
	// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/makslog.log', $str, FILE_APPEND);

	$arBASE_PRICE = array(
		'ID' => $arOpt['ID'],
		'CATALOG_GROUP_ID' => $arOpt['CATALOG_GROUP_ID'],
		'PRICE' => $arOpt['PRICE'],
		'CURRENCY' => $arOpt['CURRENCY'],
		'ELEMENT_IBLOCK_ID' => $arOpt['PRODUCT_ID'],
	);
	return array('PRICE' => $arBASE_PRICE);
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

function setOfflinePriceType(\Bitrix\Main\Event $event)
{
    /** @var \Bitrix\Sale\Order $order */
    $order = $event->getParameter("ENTITY");

    // Получаем ID пользователя
    $userId = $order->getUserId();

    if ($userId > 0) {
        // Проверяем наличие галочки UF_APPLY_PRICE
        $user = UserTable::getList([
            'filter' => ['ID' => $userId],
            'select' => ['UF_APPLY_PRICE'],
        ])->fetch();

        if ($user && $user['UF_APPLY_PRICE'] === 'Y') {
            // Устанавливаем тип цены "OFFLINE KZT"
            $priceTypeId = getPriceTypeIdByName("OFFLINE KZT");

            if ($priceTypeId) {
                foreach ($order->getBasket()->getBasketItems() as $basketItem) {
                    $basketItem->setField("PRICE_TYPE", $priceTypeId);
                }
            }
        }
    }
}

function getPriceTypeIdByName($priceTypeName)
{
    // Получение ID типа цены по названию
    $priceType = \Bitrix\Catalog\GroupTable::getList([
        'filter' => ['NAME' => $priceTypeName],
        'select' => ['ID'],
    ])->fetch();

    return $priceType ? $priceType['ID'] : null;
}


