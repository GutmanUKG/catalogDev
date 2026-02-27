<?
IncludeModuleLangFile(__FILE__);

Class SotbitB2bCabinet {
    const MODULE_ID = 'sotbit.b2bcabinet';
    const PATH = 'b2bcabinet';
    const VERSION = '2.0';
    static private $demo = null;

    private static function setDemo() {
        self::$demo = \Bitrix\Main\Loader::includeSharewareModule(SotbitB2bCabinet::MODULE_ID);
    }

    public static function getDemo()
    {
        if(self::$demo === false || self::$demo === null)
            self::setDemo();
        return !(self::$demo == 0 || self::$demo == 3);
    }

    public static function returnDemo()
    {
        if(self::$demo === false || self::$demo === null)
            self::setDemo();
        return self::$demo;
    }

    public function checkInstalledModules(array $arModules) {
        foreach($arModules as $module) {
            if (!\Bitrix\Main\Loader::includeModule($module))
                return false;
        }

        return true;
    }
}
?>