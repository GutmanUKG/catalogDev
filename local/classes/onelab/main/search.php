<?php

namespace Onelab\Main;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class Search
{
    public static function prepareQuery($query)
    {
        return str_replace(['-', '/', '#', '.', ',', '+', 'для'], '', $query);
    }
}