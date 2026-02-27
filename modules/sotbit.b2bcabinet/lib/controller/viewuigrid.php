<?

namespace Sotbit\B2bcabinet\Controller;

use Bitrix\Main\Engine;
use Bitrix\Main\Request;

class ViewUIGrid extends Engine\Controller {
    const CATEGORY = 'b2bcabinet_grid_type';
    const GET_PARAM = 'view';

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }
    public function addAction($gridId, $typeView = 'list') {
        \CUserOptions::SetOption(self::CATEGORY, $gridId, $typeView);
    }

    public function getActiveView($navbarId, $default = '')
    {
        if ($currentTypeView = $this->getViewFromParams()) {
            $this->addAction($navbarId, $currentTypeView);
            return $currentTypeView;
        }

        return \CUserOptions::GetOption(self::CATEGORY, $navbarId, $default);
    }

    private function getViewFromParams()
    {
        return $this->request->get(self::GET_PARAM) ?: false;
    }
}