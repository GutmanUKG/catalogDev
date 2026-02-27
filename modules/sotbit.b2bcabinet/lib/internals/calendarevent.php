<?php

namespace Sotbit\B2BCabinet\Internals;

use Bitrix\Main;


class CalendarEventTable extends \Bitrix\Main\Entity\DataManager
{
    /**
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'sotbit_b2bcabinet_calendar_events';
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
            'CODE' => [
                'data_type' => 'string',
                'required' => true
            ],
            'VALUES' => [
                'data_type' => 'string',
                'required' => true,
                'serialized' => true,
            ],
            'DATE' => [
                'data_type' => 'datetime',
                'default_value' => new Main\Type\DateTime(),
            ],
            'USER_ID' => [
                'data_type' => 'integer',
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