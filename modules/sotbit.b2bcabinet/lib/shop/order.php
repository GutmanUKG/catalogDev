<?php
namespace Sotbit\B2BCabinet\Shop;
use Bitrix\Sale\Integration\Numerator;

class Order
{
    private $price = '';
    private $date = '';
    private $status = [];
    private $id = '';
    private $displayId = '';
    private $personType = '';
    private $uniqueId;

    /**
     * @param string $order;
     * @param int|string $uniqueId;
     */
    public function __construct($order = [], $uniqueId=null)
    {
        $this->uniqueId = $uniqueId;
        if (empty($uniqueId)) {
            $useAccountNumber = Numerator\NumeratorOrder::isUsedNumeratorForOrder();
            $this->uniqueId = $useAccountNumber ? 'ACCOUNT_NUMBER' : 'ID';
        }

        $this->setId($order[$this->uniqueId]);
        $this->setDisplayId($order['ACCOUNT_NUMBER']);

        if($order['PRICE'] > 0 && $order['CURRENCY']) {
            $this->setPrice($order['PRICE'], $order['CURRENCY']);
        }
        if($order['DATE_INSERT']) {
            $this->setDate($order['DATE_INSERT']);
        }
    }

    public function getOrgName($props = [])
    {
        $return = '';
        $order = \Bitrix\Sale\Order::load($this->getId());
        $propertyCollection = $order->getPropertyCollection();
        foreach($propertyCollection as $property) {
            if(in_array($property->getPropertyId(), $props)) {
                $return .= $property->getValue().' ';
            }
        }

        return trim($return);
    }

    public function getDownloadBillLink($pathToPay = '')
    {
        $return = '';

        if ($this->uniqueId == 'ACCOUNT_NUMBER') {
            $order = \Bitrix\Sale\Order::loadByAccountNumber($this->getId());
        } else {
            $order = \Bitrix\Sale\Order::load($this->getId());
        }

        $paymentCollection = $order->getPaymentCollection();
        foreach($paymentCollection as $payment) {
            if(!$payment->isPaid()) {
                $paymentFields = $payment->getFieldValues();
                $paySystem = \Bitrix\Sale\PaySystem\Manager::getById($paymentFields["PAY_SYSTEM_ID"]);
                if($paySystem['ACTION_FILE'] == 'bill') {
                    $return = $pathToPay.'?ORDER_ID='.$this->getId().'&PAYMENT_ID='.$payment->getId().'&pdf=1&DOWNLOAD=Y';
                    break;
                }
            }
        }

        return $return;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setDisplayId($displayId)
    {
        $this->displayId = $displayId;
    }

    public function getDisplayId()
    {
        return $this->displayId;
    }

    public function getUrl($rule = '')
    {
        return str_replace('#ID#', urlencode(urlencode($this->id)), $rule);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPrice(
        $price, $currency
    )
    {
        $this->price = CurrencyFormat($price, $currency);
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getPersonType()
    {
        return $this->personType;
    }

    public function setPersonType($personType)
    {
        $this->personType = $personType;
    }

    public function getDownloadBillUrl()
    {
        return $this->downloadBillUrl;
    }

    public function setDownloadBillUrl($downloadBillUrl)
    {
        $this->downloadBillUrl = $downloadBillUrl;
    }
}