<?php

namespace Sotbit\B2BCabinet\Personal;
use Bitrix\Bizproc\Workflow\Template\Packer\Result\Pack;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

class Buyers extends \SotbitB2bCabinet
{
    protected $hasB2BCabinet = false;
    protected $companyNameCode = false;
    protected $includeAuth = false;
    protected $authExtendedVersion = false;
    protected $personType;
    protected $siteId = false;

    public function __construct($siteId = '') {
        if ($siteId) {
            $this->siteId = $siteId;
        }

        $this->hasB2BCabinet = Loader::includeModule('sotbit.b2bcabinet');
        $this->companyNameCode = Option::get('sotbit.b2bcabinet','PROFILE_ORG_NAME','COMPANY');
        $this->includeAuth = \CModule::IncludeModule("sotbit.auth");
        $this->personType = $this->getPersonType();
        $this->authExtendedVersion = $this->getAuthVertion();


    }

    public function findBuyersForUser($idUser = 0) {
        $listBuyers = array();

        if($this->getDemo() && $idUser > 0) {
            $filter = array("USER_ID" => $idUser);
            if($this->hasB2BCabinet) {
                $filter['PERSON_TYPE_ID'] = unserialize(Option::get('sotbit.b2bcabinet','BUYER_PERSONAL_TYPE','a:0:{}'), ['allowed_classes' => false]);
            }
            $rsBuyers = \CSaleOrderUserProps::GetList( array(), $filter );
            while ($buyer = $rsBuyers->fetch()) {
                $listBuyers[$buyer['ID']] = new Buyer($buyer);
            }
            if($this->hasB2BCabinet && count($listBuyers) > 0) {
                $db_propVals = \CSaleOrderUserPropsValue::GetList(
                    array("ID" => "ASC"),
                    array(
                        "USER_PROPS_ID"=>array_keys($listBuyers),
                        'CODE' => $this->companyNameCode
                    )
                );
                while ($arPropVals = $db_propVals->Fetch()) {
                    if($arPropVals['VALUE']) {
                        $listBuyers[$arPropVals['USER_PROPS_ID']]->setOrg($arPropVals['VALUE']);
                    }
                }
            }
        }

        return $listBuyers;
    }

    private function getAuthVertion()
    {
        if (!$this->includeAuth) {
            return false;
        }
        return  Option::get('sotbit.auth','EXTENDED_VERSION_COMPANIES','N') == "Y" ? true : false;
    }

    private function getPersonType()
    {
        return $this->includeAuth ? unserialize(Option::get('sotbit.auth','WHOLESALERS_PERSON_TYPE','', $this->siteId)) : unserialize(Option::get('sotbit.b2bcabinet','BUYER_PERSONAL_TYPE','', $this->siteId));
    }

    public function getCompanyByInn($inn)
    {
        if (!$inn) {
            return null;
        }

        $innProps = $this->getInnProps();
        $nameProps = $this->getPropertyName();
        $companyId = $this->getCompanyId($innProps, $inn);
        $companyName = $this->getCompanyPropertyValue($nameProps , $companyId);

        return [
            "ID" => $companyId,
            "NAME" => $companyName,
        ];
    }

    private function getCompanyPropertyValue($property, $companyId)
    {
        if ($this->authExtendedVersion) {
            $resComp = \Sotbit\Auth\Internals\CompanyPropsValueTable::getList([
                'filter' => ['PROPERTY_ID' => $property, 'COMPANY_ID' => $companyId],
                'select' => ['VALUE']
            ])->fetch();

            return $resComp ? $resComp["VALUE"] : null;
        } else {
            $resComp = \CSaleOrderUserPropsValue::GetList(
                [],
                [
                    "USER_PROPS_ID" => $companyId,
                    "ORDER_PROPS_ID" => $property
                ],
                false,
                false,
                ["VALUE"]
            )->fetch();
            return $resComp ? $resComp["VALUE"] : null;
        }
    }

    private function getCompanyId($propertyId, $propertyValue) {
        if ($this->authExtendedVersion) {
            $resComp = \Sotbit\Auth\Internals\CompanyPropsValueTable::getList([
                'filter' => ['PROPERTY_ID' => $propertyId, 'VALUE' => $propertyValue],
                'select' => ['COMPANY_ID']
            ])->fetch();

            return $resComp ? $resComp["COMPANY_ID"] : null;
        } else {
            $resComp = \CSaleOrderUserPropsValue::GetList(
                [],
                [
                    "VALUE" => $propertyValue,
                    "ORDER_PROPS_ID" => $propertyId
                ],
                false,
                false,
                ["USER_PROPS_ID"]
            )->fetch();
            return $resComp ? $resComp["USER_PROPS_ID"] : null;
        }

    }

    private function getPropertyName()
    {
        if ($this->includeAuth) {
            foreach ($this->personType as $typeId) {
                $namePropsCode[] = Option::get('sotbit.auth','COMPANY_PROPS_NAME_FIELD_' . $typeId,'', $this->siteId);
            }
            $nameProps = $this->getOrderPropsId(["CODE" => $namePropsCode]);
        } else {
            $nameProps = unserialize(Option::get('sotbit.b2bcabinet','PROFILE_ORG_NAME','', $this->siteId));
        }

        return $nameProps;
    }

    public function getInnProps()
    {
        if ($this->includeAuth) {
            foreach ($this->personType as $typeId) {
                $innPropsCode[] = Option::get('sotbit.auth','GROUP_ORDER_INN_FIELD_' . $typeId,'');
            }
            $innProps = $this->getOrderPropsId(["CODE" => $innPropsCode]);
        } else {
            $innProps = unserialize(Option::get('sotbit.b2bcabinet','PROFILE_ORG_INN','', $this->siteId));
        }

        return $innProps;
    }

    public function getProfileId($innValue)
    {
        $res = \CSaleOrderUserPropsValue::GetList([],
            [
                "ORDER_PROPS_ID" => $this->getInnProps(),
                "VALUE" => $innValue
            ],
            false,
            false,
            ["USER_PROPS_ID"]
        )->fetch();

        return $res["USER_PROPS_ID"];
    }


    private function getOrderPropsId($filter)
    {
        $result = [];
        $dbOrderPropertie = \CSaleOrderProps::GetList(
            [],
            $filter,
            false,
            false,
            ["ID"]
        );

        while ($arProperty = $dbOrderPropertie->fetch()) {
            $result[] = $arProperty["ID"];
        }

        return $result;
    }

}