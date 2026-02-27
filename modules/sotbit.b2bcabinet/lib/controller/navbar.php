<?php

namespace Sotbit\B2bcabinet\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\Request;
use Bitrix\Main\Engine\ActionFilter;

class Navbar extends \Bitrix\Main\Engine\Controller
{
    const CATEGORY = 'b2bcabinet_navbar';
    const GET_PARAM = 'tab';

    public function configureActions()
    {
        return [
            'add' => [
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

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function addAction($navbarId, $navbarItemId = '')
    {
        \CUserOptions::SetOption(self::CATEGORY, $navbarId, $navbarItemId);
    }

    public function getActiveTab($navbarId, $default = '')
    {
        if ($currentTab = $this->getTabFromParams()) {
            $this->addAction($navbarId, $currentTab);
            return $currentTab;
        }

        return \CUserOptions::GetOption(self::CATEGORY, $navbarId, $default);
    }

    private function getTabFromParams()
    {
        return $this->request->get(self::GET_PARAM) ?: false;
    }
}