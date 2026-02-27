<?
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
define('STOP_STATISTICS', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Loc::loadMessages(__FILE__);

$file = array();
$error = array();
$qntColl = 0;
$idColl = 0;

if(Loader::includeModule("sotbit.b2bcabinet") && Loader::includeModule("catalog"))
{
	if(isset($_REQUEST['file_id']) && !empty($_REQUEST['file_id']))
		$fileID = $_REQUEST['file_id'];

	$filePath = $_SERVER['DOCUMENT_ROOT'] . CFile::GetPath($fileID);

	if(file_exists($filePath) === false)
		$error[] = [
			'TYPE' => 'File',
			'msg' => 'File does not exist'
		];

	$curCurrency = '';
	$lcur = CCurrency::GetList(($by = 'sort'), ($order = 'asc'), LANGUAGE_ID);
	while($lcur_res = $lcur->Fetch())
	{
		if($lcur_res['BASE'] == 'Y' && !empty($lcur_res['FORMAT_STRING']))
		{
			$curCurrency = preg_replace('/[\#\d\s]+/', '', $lcur_res['FORMAT_STRING']);
			break;
		}
	}

	$dbPriceType = CCatalogGroup::GetList(
					array("SORT" => "ASC"),
					array()
					);
	while ($arPriceType = $dbPriceType->Fetch())
	{
		$arPriceName[] = $arPriceType['NAME_LANG'];
	}


	if(empty($error) && strpos($filePath,'.xls') !== false)
	{
		$objPHPExcel = PHPExcel_IOFactory::load($filePath);

		$objPHPExcel->setActiveSheetIndex(0);
		$aSheet = $objPHPExcel->getActiveSheet();

		$higestRow = $aSheet->getHighestRow();

		$rowiterator = $aSheet->getRowIterator();
		foreach ($rowiterator as $row)
		{
			$cellIterator = $row->getCellIterator();

			foreach ($cellIterator as $j => $cell)
			{
				if($cell->getValue() == $_REQUEST['quantity']) {
					$qntColl = $cell->getColumn();
					continue;
				}

				if($cell->getValue() == 'ID') {
					$idColl = $cell->getColumn();
					continue;
				}

				foreach ($arPriceName as $pName) {
					if(
						$pName == mb_convert_encoding($cell->getValue(), 'CP1251', 'UTF-8') ||
						$pName == $cell->getValue()
					) {
						$arPriceNamePos[] = $cell->getColumn();
					}
				}
			}

			if(empty($qntColl) || empty($idColl)) {
				$error[] = [
					'TYPE' => 'file',
					'msg' => 'Wrong structure excel file!'
				];
			}
			break;
		}

		if(empty($error)) {
			for ($i = 2; $i < $higestRow+1; $i++) {
				$cellID = $aSheet->getCell($idColl . $i);
				$cellQNT = $aSheet->getCell($qntColl . $i);

				if (!empty($cellID->getValue()) && !empty($cellQNT->getValue()) && intval($cellQNT->getValue()) > 0) {
					$id = $cellID->getValue();
					$qnt = $cellQNT->getValue();
					$arrExcelProductIDs[$id]['QNT'] = $qnt;
				}
			}
		}

		if(empty($error)) {
			$arrExcelIDs = array_keys($arrExcelProductIDs);
			$arrMeasure = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($arrExcelIDs);

			foreach ($arrExcelProductIDs as $key => $product) {
				if ($product['QNT'] < $arrMeasure[$key])
					$product['QNT'] = $arrMeasure[$key];

				if (fmod($product['QNT'], $arrMeasure[$key]) == 0)
					$arrExcelProductIDs[$key]['QNT'] = $product['QNT'];
				else {
					$intQNT = preg_replace('/\..+/', '', $product['QNT'] / $arrMeasure[$key]);
					$arrExcelProductIDs[$key]['QNT'] = $intQNT * $arrMeasure[$key];
				}
			}
		}
	} else {
		$error[] = [
			'TYPE' => 'file',
			'msg' => Loc::getMessage('B2B_IMPORT_ORDER_TEMPLATE_FROM_EXEL_NOT_EXCEL'),
		];
	}
	//unlink($filePath);
    CFile::Delete($filePath);
}
else
{
	$error[] = [
		'TYPE' => 'module',
		'msg' => 'module: sotbit.b2bcabinet, Not installed!'
	];
}

if (empty($arrExcelProductIDs)) {
	$error[] = [
		'TYPE' => 'quantity',
		'msg' => Loc::getMessage('B2B_IMPORT_ORDER_TEMPLATE_FROM_EXEL_NOT_PRODUCTS'),
	];
}

if(!empty($error))
	echo \Bitrix\Main\Web\Json::encode($error);
else
	echo \Bitrix\Main\Web\Json::encode($arrExcelProductIDs);
?>
