<?php

namespace Sotbit\B2bcabinet\Controller;

use Bitrix\Main\Engine;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Engine\Response;

class AccountPayVigetController extends Engine\Controller
{
    public function configureActions()
    {
        return [];
    }

    public function getAccountPayComposentAction(array $arParams): Response\Component
    {
        $component = new Response\Component(
            "bitrix:sale.account.pay",
            "sotbit_cabinet_widget",
            $arParams, [], [], ['javascriptParams']
        );

        return $component;
    }
}