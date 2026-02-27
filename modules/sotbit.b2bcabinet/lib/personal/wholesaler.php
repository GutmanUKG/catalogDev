<?php

namespace Sotbit\B2BCabinet\Personal;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\Internals\UserPropsValueTable;
use Sotbit\Auth\Internals\FileTable;
use Sotbit\B2bCabinet\Helper\Config;

Loader::includeModule('sale');

class Wholesaler
{
    const MAIL_EVENT_NAME = 'SOTBIT_B2BCABINET_WHOLESALER_REGISTER';
    public $sendNotice = true;
    protected int $personType;
    protected array $fields;
    protected $error;
    private $siteId;

    public function __construct($personType, $site = SITE_ID)
    {
        $this->personType = $personType;
        $this->siteId = $site;
    }

    public function checkUniqueProfile()
    {
        return UserPropsValueTable::query()
                ->addSelect('ID')
                ->where('PROPERTY.CODE', $this->getUniqueField())
                ->where('PROPERTY.PERSON_TYPE_ID', $this->personType)
                ->where('VALUE', '=', trim($this->getField('ORDER_FIELDS')[$this->getUniqueField()]))
                ->fetch() === false;
    }

    public function getUniqueField()
    {
        return Config::get('GROUP_UNIQUE_FIELDS_' . $this->personType, $this->siteId);
    }

    public function getField($name = '')
    {
        return $this->fields[$name];
    }

    public function checkOrderProps(): bool
    {
        $orderFields = is_array($this->getField('ORDER_FIELDS')) ? $this->getField('ORDER_FIELDS') : [];
        $arOrderProps = OrderPropsTable::query()
            ->setSelect(['ID', 'CODE', 'REQUIRED', 'NAME'])
            ->where('PERSON_TYPE_ID', $this->personType)
            ->whereIn('CODE', array_keys($orderFields))
            ->fetchAll();

        foreach ($arOrderProps as $arProperty) {
            if ($arProperty['REQUIRED'] === 'Y' && empty($orderFields[$arProperty['CODE']])) {
                $this->error[] = [
                    'TEXT' => Loc::getMessage('SB_WHOLESALER_ORDER_PROP_EMPTY', ['#NAME#' => $arProperty['NAME']])
                ];
                return false;
            }
        }

        return true;
    }

    public function addBuyer()
    {
        $orderFields = is_array($this->getField('ORDER_FIELDS')) ? $this->getField('ORDER_FIELDS') : [];

        $name = date('Y-m-d');

        if ($orderFields) {
            $dbResult = \Bitrix\Sale\Internals\OrderPropsTable::getList([
                'filter' => [
                    'ACTIVE' => 'Y',
                    'PERSON_TYPE_ID' => $this->personType,
                    'CODE' => array_keys($orderFields)
                ],
                'select' => [
                    'ID',
                    'CODE',
                    'PERSON_TYPE_ID',
                    'IS_PROFILE_NAME'
                ]
            ]);
            while ($property = $dbResult->fetch()) {
                if ($property['IS_PROFILE_NAME'] == 'Y') {
                    $name = $orderFields[$property['CODE']];
                }
                $arWholesaler[$property['PERSON_TYPE_ID']][$property['ID']] = $orderFields[$property['CODE']];
            }
        }

        $idUserProps = \CSaleOrderUserProps::add(
            [
                'NAME' => $name,
                'USER_ID' => $this->getField('USER_ID'),
                'PERSON_TYPE_ID' => $this->personType
            ]
        );

        if ($idUserProps && $orderFields) {
            \CSaleOrderUserProps::DoSaveUserProfile($this->getField('USER_ID'), $idUserProps, $name,
                $this->personType, $arWholesaler[$this->personType], $this->error);
        }

        if ($idUserProps && $this->sendNotice) {
            $this->sendNotice();
        }

        return $idUserProps ?: false;
    }

    public function sendNotice()
    {
        $userFields = UserTable::query()
            ->setSelect(['*'])
            ->where('ID', $this->getField('USER_ID'))
            ->fetch();

        if (!$userFields) {
            return;
        }

        \Bitrix\Main\Mail\Event::send(
            [
                "EVENT_NAME" => self::MAIL_EVENT_NAME,
                "LID" => $this->siteId,
                "C_FIELDS" => $userFields
            ]);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields(array $arFields)
    {
        $this->fields = $arFields;
    }

    public function setField($name = '', $value = '')
    {
        $this->fields[$name] = $value;
    }

    public function getError()
    {
        return $this->error ? array_column($this->error, 'TEXT') : [];
    }
}