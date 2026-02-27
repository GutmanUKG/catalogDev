<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Sotbit\B2bCabinet\Helper;
use Bitrix\Main\UI\Filter;
use Sotbit\B2BCabinet\Controller\FileController;
use Bitrix\Main\Context;
use Bitrix\Sale;
use Sotbit\B2bCabinet;
use Bitrix\Main\Config;

if(Loader::includeModule('sale') && Loader::includeModule('iblock'))
{
    $arDocs = array();

    $request = Helper\Request::getInstance();
    $docIblockID = Helper\Document::getIblocks();
    $docIblockType = Helper\Document::getIblocksType();
    $innProps = unserialize(Helper\Config::get('PROFILE_ORG_INN'));


    // INN
    if(!is_array($innProps))
        $innProps = [];

    if(!empty($innProps)) {
        foreach ($innProps as $keyId) {
            if(isset($arResult['ORDER_PROPS_VALUES']['ORDER_PROP_'. $keyId])) {
                $innProps = $keyId;
                break;
            }
        }
    }

    // Delete document
    if(isset($_REQUEST['DOC_DELETE']) && $request->get('DOC_DELETE') === 'Y' && !empty($request->get('DOC_ID'))) {
        $docId = (int)$request->get('DOC_ID');

        if(!empty($docId) && !empty($docIblockType) && !empty($docIblockID)) {
            if(Helper\Document::checkPermissionElement($docId)) {
                $DB->StartTransaction();
                if(!CIBlockElement::Delete($docId)) {
                    $strWarning .= 'Error!';
                    $DB->Rollback();
                } else {
                    $DB->Commit();
                }
            }
        }
    }

    $arResult['ROWS'] = [];

    $filter = [];
    $filterOption = new Filter\Options('DOCUMENTS_LIST');
    $filterData = $filterOption->getFilter([]);

    foreach ($filterData as $key => $value)
    {
        if(in_array($key, ['ID','NAME','DATE_CREATE_from','DATE_CREATE_to', 'FIND']))
        {
            switch ($key)
            {
                case 'NAME':
                    $filter['%NAME'] = $value;
                    break;
                case 'DATE_CREATE_from':
                    $filter['>=DATE_CREATE'] = $value;
                    break;
                case 'DATE_CREATE_to':
                    $filter['<=DATE_CREATE'] = $value;
                    break;
                case 'ID':
                    $filter['=ID'] = $value;
                    break;
                default:
                    $filter['%NAME'] = $value;
            }
        }
    }
    $by = isset($_GET['by']) ?  $request->get('by') : (isset($arParams["SORT_BY1"]) ? $arParams["SORT_BY1"] : '');
    $order = isset($_GET['order']) ? strtoupper($request->get('order')) : (isset($arParams["SORT_ORDER1"]) ? $arParams["SORT_ORDER1"] : '');

    if($by == 'DATE_UPDATE')
    {
        $by = 'TIMESTAMP_X';
    }

    $filter = array_merge($filter,
        array(
            "IBLOCK_ID" => $docIblockID,
            "PROPERTY_ORGANIZATION" => $arResult['ORDER_PROPS_VALUES']['ORDER_PROP_'. $innProps],
            "PROPERTY_USER" => $USER->GetID()
        )
    );
    $resDocs = CIBlockElement::GetList(
        array("SORT" => "ASC"),
        $filter,
        false,
        false
    );

    while ($res = $resDocs->GetNextElement())
    {
        $tmp = $res->GetFields();

        $arDocs[$tmp['ID']] = $tmp;
        $props = $res->GetProperties();
        if(is_array($props))
        {
            // if(!empty($props['DOCUMENT']['VALUE']))
                // $props['DOCUMENT']['DOCS'] = Bitrix\Main\FileTable::getById($props['DOCUMENT']['VALUE'])->fetch();

            $arDocs[$tmp['ID']] = array_merge($arDocs[$tmp['ID']], $props);
        }
    }
}

foreach ($arDocs as $val)
{
    $documentDeleteUrl = FileController::urlGenerate(
        'DocumentDelete',
        ['documentID' => $val['ID'], 'backUrl' => Context::getCurrent()->getRequest()->getRequestUri()],
    );

    $fileDownloadUrl = FileController::urlGenerate(
        'fileDownload',
        ['fileId' => $val['DOCUMENT']['VALUE'], 'fileName' => addslashes($val['~NAME'])],
    );

    $aActions = [
        array("SEPARATOR"=>true),
        [
            "TEXT"=>Loc::getMessage('OPP_DELETE_DOC'),
            "ONCLICK"=>"if(confirm('". Loc::getMessage('OPP_DELETE_CONFIRM_MESS') ."')) ". "window.location.href='" . $documentDeleteUrl . "'",
        ],
    ];
    $aActions = empty($val['DOCUMENT']['VALUE'])
        ? $aActions
        : array_merge([
            [
                "TEXT"=>Loc::getMessage('OPP_DOWNLOAD_DOC'),
                "ONCLICK"=>"window.location.href='" . $fileDownloadUrl . "'",
            ]],
            $aActions,
        );
    $orderLink = '';
    $ORDER_DETAIL_PATH = Config\Option::get('sotbit.b2bcabinet', 'ADDRESS_ORDER', '', SITE_ID);
    if($val['ORDER']['VALUE']){
        foreach ($val['ORDER']['VALUE'] as $accountNumber){
            if ($order = Sale\Order::loadByAccountNumber($accountNumber)) {
                $b2bOrder = new B2bCabinet\Shop\Order($order->toArray());
                $url = $b2bOrder->getUrl($ORDER_DETAIL_PATH);
                $orderLink .= '<a href="'. $url .'" target="__blank">'. $b2bOrder->getDisplayId() .'</a><br>';
            }
        }
    }

    array_push($arResult['ROWS'], [
        'data' => array_merge(
            [
            "ID" => $val['ID'],
            "NAME" => '<a href="'. $fileDownloadUrl .'" target="__blank">'. $val['NAME'] .'</a><br>',
            "DATE_CREATE" => $val['DATE_CREATE'],
            "ORDER" => $orderLink
            ]
        ),
        'actions' => $aActions,
        'COLUMNS' => $aCols,
        'editable' => true,
    ]);
}

if(isset($_GET['by']) && !in_array($_GET['by'], [
        'ID',
        'NAME',
        'DATE_UPDATE',
        'PERSON_TYPE_NAME'
    ]))
{
    $by = $request->get('by');
    $order = in_array(strtolower($request->get('order')), [
        'asc',
        'desc'
    ]) ? strtolower($request->get('order')) : 'asc';

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