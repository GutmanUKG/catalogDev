<?
namespace Onelab;
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/brands/main.php");



$brandsEvent = new BrandsEvent;

$brandsEvent-> main($_POST['IBLOCK_ID'], $_POST['SECTION_NEWS_ID'], $_POST['ITEM_ID']);



