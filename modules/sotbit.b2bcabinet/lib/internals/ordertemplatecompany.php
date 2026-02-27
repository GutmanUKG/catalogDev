<?php

namespace Sotbit\B2BCabinet\Internals;


class OrderTemplateCompanyTable extends \Bitrix\Main\Entity\DataManager
{
    /**
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'sotbit_b2bcabinet_order_template_company';
    }
    /**
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ORDER_TEMPLATE_ID' => [
                'data_type' => 'integer',
                'primary' => true,
            ],
            'COMPANY_ID' => [
                'data_type' => 'integer',
                'primary' => true,
            ],
        );
    }
}
?>