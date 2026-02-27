<?php

use Bitrix\Main\Config\Option;

$MAIL_CONSTANTS = [
    'PHONE_NUMBER' => '+7 (812) 670-07-40',
    'EMAIL' => Option::get('sale', 'order_email', 'sale@sale.com')
];