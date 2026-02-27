<?php

namespace Sotbit\B2BCabinet\Internals;


class DraftProductTable extends \Bitrix\Main\Entity\DataManager
{
    /**
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'sotbit_b2bcabinet_draft_product';
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
            'DRAFT_ID' => [
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
            'DRAFT' => [
                'data_type' => 'Sotbit\B2BCabinet\Internals\DraftTable',
                'reference' => array('=this.DRAFT_ID' => 'ref.ID'),
                'join_type' => 'LEFT',
            ],
        );
    }
}
?>