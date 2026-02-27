<?php

namespace Sotbit\B2BCabinet\Internals;

use Bitrix\Main;

class OrderTemplateProductTable extends \Bitrix\Main\Entity\DataManager
{
    /**
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'sotbit_b2bcabinet_order_template_product';
    }
    /**
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ],
            'ORDER_TEMPLATE_ID' => [
                'data_type' => 'integer',
                'required' => true
            ],
            'PRODUCT_ID' => [
                'data_type' => 'integer',
                'required' => true
            ],
            'QUANTITY' => [
                'data_type' => 'float',
                'required' => true
            ],
            'ORDER_TEMPLATE' => [
                'data_type' => 'Sotbit\B2BCabinet\Internals\OrderTemplateTable',
                'reference' => array('=this.ORDER_TEMPLATE_ID' => 'ref.ID'),
                'join_type' => 'LEFT',
            ],
        );
    }
}
?>