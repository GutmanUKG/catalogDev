<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

	use Bitrix\Main\Loader;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Page\Asset;
    use Bitrix\Main\Config;

	Loc::loadMessages(__FILE__);

    $authIsUsed = Loader::includeModule('sotbit.auth') && Config\Option::get('sotbit.auth', 'EXTENDED_VERSION_COMPANIES', 'N', SITE_ID) === 'Y';

    if ($authIsUsed === true) {
        $APPLICATION->IncludeComponent(
            "sotbit:auth.company.list",
            "b2b_gadget",
            [
                "SEF_FOLDER" => sprintf('%s/companies/', $arParams["G_BUYERS_SEF_FOLDER"]),
                "PATH_TO_DETAIL" => $arParams["G_BUYERS_PATH_TO_DETAIL"],
                'PER_PAGE' => $arGadgetParams['PER_PAGE'],
            ],
            false,
        );
    } else {
        $APPLICATION->IncludeComponent(
            "bitrix:sale.personal.profile.list",
            "b2b_gadget",
            [
                "PATH_TO_DETAIL" => $arParams["G_BUYERS_PATH_TO_DETAIL"],
                'PER_PAGE' => $arGadgetParams['PER_PAGE'],
                "SEF_FOLDER" => sprintf('%s/buyer/', $arParams["G_BUYERS_SEF_FOLDER"]),
                "GRID_HEADER" => array(
                    array("id"=>"ID", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_ID'), "sort"=>"ID", "default"=>true, "editable"=>false),
                    array("id"=>"NAME", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_NAME'), "sort"=>"NAME", "default"=>true, "editable"=>false),
                    array("id"=>"DATE_UPDATE", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_DATE_UPDATE'), "sort"=>"DATE_UPDATE", "default"=>true, "editable"=>false),
                    array("id"=>"PERSON_TYPE_NAME", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_PERSON_TYPE_NAME'), "sort"=>"PERSON_TYPE_ID", "default"=>true, "editable"=>true),
                ),
                "BUYER_PERSONAL_TYPE" => $arParams['BUYER_PERSONAL_TYPE'],
            ],
            false,
        );
    }

?>

