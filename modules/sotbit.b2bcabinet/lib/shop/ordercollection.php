<?php
namespace Sotbit\B2BCabinet\Shop;

use Bitrix\Sale\Internals\PersonTypeTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Integration\Numerator;

class OrderCollection extends \SotbitB2BCabinet
{
    public $orderList = [];
    private $limit = 2;
    private $uniqueId;

    public function __construct()
    {
        $useAccountNumber = Numerator\NumeratorOrder::isUsedNumeratorForOrder();
        $this->uniqueId = $useAccountNumber ? 'ACCOUNT_NUMBER' : 'ID';
    }

    public function getOrders($filter = [])
    {
        if($this->getDemo()) {
            $listStatusNames = \Bitrix\Sale\OrderStatus::getAllStatusesNames(LANGUAGE_ID);
            $personTypes = [];
            $orderList = \Bitrix\Sale\Order::getList([
                'select' => [
                    "ID", 'PERSON_TYPE_ID', 'STATUS_ID', 'ACCOUNT_NUMBER', 'PRICE',
                    'CURRENCY', 'DELIVERY_ID', 'DATE_INSERT', 'CANCELED',
                ],
                'filter' => $filter,
                'order'  => ["ID" => "DESC"],
                'limit' => $this->limit],
            );

            while($order = $orderList->fetch()) {
                $this->orderList[$order[$this->uniqueId]] = new Order($order, $this->uniqueId);
                $this->orderList[$order[$this->uniqueId]]->setStatus(
                    $order['CANCELED'] === 'Y'
                    ? [loc::getMessage('SOTBIT_AUTH_ORDER_CANSELED')]
                    : [$order['STATUS_ID'] => $listStatusNames[$order['STATUS_ID']]]
                );

                $personTypes[$order[$this->uniqueId]] = $order['PERSON_TYPE_ID'];
            }
            $rsPersonTypes = PersonTypeTable::getList(['filter' => ['ID' => $personTypes]]);
            while($personType = $rsPersonTypes->fetch()) {
                foreach($personTypes as $idOrder => $idPersonType) {
                    if($idPersonType == $personType['ID']) {
                        $this->orderList[$idOrder]->setPersonType($personType['NAME']);
                    }
                }
            }
        }

        return $this->orderList;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}