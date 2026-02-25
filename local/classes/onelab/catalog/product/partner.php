<?

    

namespace Onelab\Catalog\Partner;



\Bitrix\Main\Loader::includeModule("catalog");



class Basket

{

    const defaultCatalogGroupXmlId = "RRP";

    

    public static function add($productId,$quantity=1,$catalogGroupXmlId)

    {

        // функция добавления в корзину, сохраняем тип цены в свойствах элемента корзины

        // параметр $catalogGroupXmlId содержит XML_ID нужного типа цены

        if($catalogGroupXmlId)

        {

            $rsGroup = \Bitrix\Catalog\GroupTable::getList(array('filter'=>array('XML_ID'=>$catalogGroupXmlId)));

            if(!$rsGroup->fetch())

                $catalogGroupXmlId = self::defaultCatalogGroupXmlId;

        }

        else

            $catalogGroupXmlId = self::defaultCatalogGroupXmlId;



        $arRewriteFields = array(

            'PRODUCT_PROVIDER_CLASS' => '\Partner\CatalogProvider',

        );

        $arProps = array( // required for $arRewriteFields, can be an empty array

            array(

                "NAME" => "РРЦ",
                "CODE" => "RRP",
                "VALUE" => $catalogGroupXmlId,
                "SORT" => "100",
            ),

        );

        $basketId = \Add2BasketByProductID(

            $productId,

            $quantity,

            $arRewriteFields,

            $arProps

        );



        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());

        $refreshStrategy = \Bitrix\Sale\Basket\RefreshFactory::create(\Bitrix\Sale\Basket\RefreshFactory::TYPE_FULL);

        $result = $basket->refresh($refreshStrategy);

        $basket->save();

        

        return $basketId;

    }

}



class CatalogProvider extends \Bitrix\Catalog\Product\CatalogProvider

{

    private static $PartnerPrice = false;

    

    /**

     * @param array $products

     *

     * @return Sale\Result

     */

    public function getProductData(array $products)

    {

        return self::customGetData($products,__FUNCTION__);

    }



    /**

     * @param array $products

     *

     * @return Sale\Result

     */

    public function getCatalogData(array $products)

    {

        return self::customGetData($products,__FUNCTION__);

    }



    private function customGetData(array $products,$methodName)

    {

        self::$PartnerPrice = false;



        foreach($products as $product)

        {

            $basketPropRes = \Bitrix\Sale\Internals\BasketPropertyTable::getList(array(

                'filter' => array('BASKET_ID' => $product['BASKET_ID'],'CODE'=>'CATALOG_GROUP_XML_ID'),

            ));

            

            if (

                !($property = $basketPropRes->fetch())

                || !($catalogGroupXmlId=$property['VALUE'])

            ) 

                continue;

            

            $rsPrice = \Bitrix\Catalog\PriceTable::getList(array(

                'filter' => array('PRODUCT_ID'=>$product['PRODUCT_ID'],'CATALOG_GROUP.XML_ID'=>$catalogGroupXmlId),

                'limit' => 1,

            ));

            if($arPrice=$rsPrice->fetch())

            {

                if(!is_array(self::$PartnerPrice))

                    self::$PartnerPrice=array();

                self::$PartnerPrice[intval($product['PRODUCT_ID'])] = $arPrice;

            }

        }



        $eventManager = \Bitrix\Main\EventManager::getInstance();

                

        // Добавляем обработчик OnGetOptimalPrice

        $eventManager->addEventHandler('catalog', 'OnGetOptimalPrice', '\\'.static::class.'::onGetOptimalPriceHandler');



        $arResult = parent::$methodName($products);



        self::$PartnerPrice = false;

        

        // Убираем обработчик OnGetOptimalPrice

        $eventManager->removeEventHandler('catalog', 'OnGetOptimalPrice', '\\'.static::class.'::onGetOptimalPriceHandler');        

        

        return $arResult;        

    }



    public function onGetOptimalPriceHandler(

        $intProductID,

        $quantity = 1,

        $arUserGroups = array(),

        $renewal = "N",

        $arPrices = array(),

        $siteID = false,

        $arDiscountCoupons = false        

    ) {

        /*

        В результате работы обработчика могут быть возвращены следующие значения:

        

        true - обработчик ничего не сделал, будет выполнена работа метода CCatalogProduct::GetOptimalPrice;

        false - возникла ошибка, работа метода прерывается;

        массив, описывающий наименьшую цену для товара.        

        */

        if(

            !is_array(self::$PartnerPrice)

            || !is_array($arPrice=self::$PartnerPrice[$intProductID])

        )

            return true;



        return array('PRICE'=>$arPrice); // также можно вернуть описания скидок

    }    

}



?>