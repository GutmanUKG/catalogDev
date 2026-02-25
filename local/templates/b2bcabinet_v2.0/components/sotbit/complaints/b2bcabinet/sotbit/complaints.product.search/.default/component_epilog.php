<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$APPLICATION->IncludeComponent(
    "sotbit:sotbit.b2bcabinet.notifications",
    "b2bcabinet",
    [],
    false
);