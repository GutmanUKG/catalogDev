<?
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Сотрудники");

if(!Loader::includeModule('sotbit.b2bcabinet'))
{
    header('Location: '.SITE_DIR);
}

if (defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES != "Y"){
    if (!defined("ERROR_404"))
        define("ERROR_404", "Y");

    \CHTTP::setStatus("404 Not Found");

    if ($APPLICATION->RestartWorkarea()) {
        require(\Bitrix\Main\Application::getDocumentRoot()."/404.php");
        die();
    }
}
else {
    global $STAFF_LIST;

    $filter = [];
    $filterOption = new Bitrix\Main\UI\Filter\Options('STAFF_LIST');

    if(!$filterData = $filterOption->getFilter([])){
        $filterRequestOption = new Bitrix\Main\UI\Filter\Options('STAFF_UNCONFIRMED_LIST');
        $filterData = $filterRequestOption->getFilter([]);
    }

    foreach ($filterData as $key => $value)
    {
        if(in_array($key, ['ID','FULL_NAME','WORK_POSITION', 'COMPANY', 'FIND']))
        {
            switch ($key)
            {
                case 'ID':
                    {
                        $STAFF_LIST['USER_ID'] = $value;
                        break;
                    }
                case 'FULL_NAME':
                    {
                        $STAFF_LIST = [
                            [
                                "LOGIC" => "OR",
                                ["%LAST_NAME" => $value],
                                ["%EMAIL" => $value],
                                ["%NAME" => $value],
                            ]
                        ];
                        break;
                    }
                case 'WORK_POSITION':
                    {

                        foreach ($value as $role){
                            $roles[] = ["%ROLE" => serialize((string)$role)];
                        }
                        $roles["LOGIC"] = "OR";

                        $STAFF_LIST = [$roles];
                        break;
                    }
                case 'COMPANY':
                    {
                        $STAFF_LIST['%NAME_COMPANY'] = $value;
                        break;
                    }
                case 'FIND':
                    {
                        if($value) {
                            $STAFF_LIST = [
                                [
                                    "LOGIC" => "OR",
                                    ["%LAST_NAME" => $value],
                                    ["%EMAIL" => $value],
                                    ["%NAME" => $value],
                                ]
                            ];
                        }
                        break;
                    }
                default:
                    {
                        $STAFF_LIST['%LAST_NAME'] = $value;
                    }
            }
        }
    }


    $by = isset($_GET['by']) ?  $_GET['by'] : "USER_ID";
    $order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';

    $APPLICATION->IncludeComponent(
	"sotbit:auth.company.staff.list", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"SORT_BY1" => $by,
		"SORT_ORDER1" => $order,
		"USER_PROPERTY_GENERAL_DATA" => array(
			0 => "NAME",
			1 => "LAST_NAME",
			2 => "SECOND_NAME",
			3 => "EMAIL",
		),
		"USER_PROPERTY_PERSONAL_DATA" => array(
			0 => "PERSONAL_PROFESSION",
			1 => "PERSONAL_PHOTO",
		),
		"USER_PROPERTY_WORK_INFORMATION_DATA" => array(
			0 => "WORK_POSITION",
		),
		"USER_PROPERTY_FORUM_PROFILE_DATA" => "",
		"USER_PROPERTY_BLOG_PROFILE_DATA" => "",
		"USER_PROPERTY_ADMIN_NOTE_DATA" => array(
		),
		"USER_SHOW_GROUPS" => array(
			0 => "1",
			1 => "2",
			2 => "3",
			3 => "4",
			4 => "5",
			5 => "6",
			6 => "7",
			7 => "8",
			8 => "9",
			9 => "10",
			10 => "11",
			11 => "12",
			12 => "13",
			13 => "14",
		),
		"FILTER_NAME" => "STAFF_LIST",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"COUNT_STAFF_PAGE" => "15"
	),
	false
);
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>