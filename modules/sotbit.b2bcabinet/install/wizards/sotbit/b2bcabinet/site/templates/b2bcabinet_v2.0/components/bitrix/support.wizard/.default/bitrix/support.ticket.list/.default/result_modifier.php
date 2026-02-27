<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

foreach ($arResult["FILTER"] as $key => &$item) {
	if ($item['id'] == 'ID') {
		unset($arResult["FILTER"][$key]);
	}

	if ($item['id'] == 'LAMP') {
		$item['items'] = [...array_slice($item['items'], 0, 1), 'yellow' => Loc::getMessage('SUP_YELLOW'), ...array_slice($item['items'], 1)];
	}
}

if(isset($_REQUEST['FIND']) && !empty($_REQUEST['FIND'])) {
    foreach ($arResult['ROWS'] as $key => $row) {
        if(strpos(mb_strtolower($row['data']['TITLE']), mb_strtolower(str_replace('"', '', $_REQUEST['FIND']))) === false) {
            unset($arResult['ROWS'][$key]);
        }
    }
}

if(isset($_GET['by']) && in_array($_GET['by'], ['ID','LAMP','TIMESTAMP_X']))
{
	$by = $_GET['by'];
	$order = in_array($_GET['order'], [
		'asc',
		'ASC',
		'desc',
		'DESC'
	]) ? strtolower($_GET['order']) : 'asc';

	for ($i = 0; $i < count($arResult['ROWS']); $i++)
	{
		for ($j = 0; $j < count($arResult['ROWS']) - 1; $j++)
		{
			$change = false;
			$t = [];

			if($order == 'desc' && strcmp($arResult['ROWS'][$i]['data'][$by], $arResult['ROWS'][$j]['data'][$by]) > 0)
			{
				$change = true;
			}
			elseif($order == 'asc' && strcmp($arResult['ROWS'][$i]['data'][$by], $arResult['ROWS'][$j]['data'][$by]) < 0)
			{
				$change = true;
			}

			if($change)
			{
				$t = $arResult['ROWS'][$j];
				$arResult['ROWS'][$j] = $arResult['ROWS'][$i];
				$arResult['ROWS'][$i] = $t;
			}
		}
	}
}