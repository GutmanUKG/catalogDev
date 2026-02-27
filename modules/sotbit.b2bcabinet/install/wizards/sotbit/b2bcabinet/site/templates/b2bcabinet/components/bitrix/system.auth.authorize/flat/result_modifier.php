<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}
use Bitrix\Main\Context;
$request = Context::getCurrent()->getRequest();
$userFields = CUser::GetList(($by="id "), ($order="asc"), ["LOGIN"=>$request->get("USER_LOGIN")])->Fetch();
if($userFields["ACTIVE"] == "N" || $userFields["BLOCKED"] == "Y"){
    $arResult["USER_BLOCKED"] = "Y";
}