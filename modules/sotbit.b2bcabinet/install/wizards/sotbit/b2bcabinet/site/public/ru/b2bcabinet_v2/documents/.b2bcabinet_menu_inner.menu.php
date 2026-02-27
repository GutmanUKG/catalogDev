<?php
use Sotbit\B2bCabinet\Helper\{Config, Document};

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

$arrInfoblocks = Document::getIblocks(true);
$aMenuLinksNew = [];

if(!empty($arrInfoblocks)) {
    foreach ($arrInfoblocks as $fields) {
        $aMenuLinksNew[] = array(
            $fields['NAME'],
            $fields['CODE']."/",
            '',
            Array("ICON_CLASS"=>"ph-clipboard-text"),
        );
    }
}

$aMenuLinks = array_merge($aMenuLinksNew, $aMenuLinks);
?>