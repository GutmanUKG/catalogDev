<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc;

class SotbitB2bcabinetAlerts extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'closeAlert' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ]
        ];
    }

    public function executeComponent()
    {
        global $USER;

        if ($USER->IsAuthorized() || isset($_SESSION["CLOSE_ALERT"])) {
            return;
        }

        $this->arResult["ALERT_MESSAGE"] = str_replace(PHP_EOL, '\n', Option::get('sotbit.b2bcabinet', 'ALERT_FOR_NOT_AUTHORIZED_USER', '', SITE_ID) ?: Loc::getMessage("B2B_ALERTS_DEFAULT"));

        if (!$this->arResult["ALERT_MESSAGE"]) {
            return;
        }

        $this->includeComponentTemplate();
    }

    public function closeAlertAction()
    {
        $_SESSION["CLOSE_ALERT"] = true;
        return true;
    }
}