<?php

namespace Sotbit\B2BCabinet\Internals;

use Bitrix\Main;


class DraftTable extends \Bitrix\Main\Entity\DataManager
{
    /**
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'sotbit_b2bcabinet_draft';
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
            'NAME' => [
                'data_type' => 'string',
                'required' => true
            ],
            'DATE_CREATE' => [
                'data_type' => 'datetime',
                'default_value' => new Main\Type\DateTime(),
            ],
            'USER_ID' => [
                'data_type' => 'integer',
                'required' => true
            ],
            'SITE_ID' => [
                'data_type' => 'string',
                'required' => true
            ],
            'USER' => [
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.USER_ID' => 'ref.ID'),
                'join_type' => 'LEFT',
            ],
        );
    }
}
?>