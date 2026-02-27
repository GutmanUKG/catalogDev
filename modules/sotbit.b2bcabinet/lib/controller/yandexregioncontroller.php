<?php

namespace Sotbit\B2bcabinet\Controller;

use Bitrix\Main\Engine;
use Bitrix\Main\Engine\Response;
use Bitrix\Main\IO;
use Bitrix\Main\Application;
use Bitrix\Main\Text\Encoding;
use SplFileObject;

class YandexRegionController extends Engine\Controller
{
    public function configureActions()
    {
        return [];
    }

    public function getRegionIdAction(string $city): array
    {
        $result = [];

        $regionsFile = new SplFileObject(Application::getDocumentRoot() . '/include/yandex_regions_use.csv');
        while ($string = $regionsFile->fgets()) {
            $curentCharSet = mb_strtolower(Encoding::convertEncodingToCurrent($string));
            $city = mb_strtolower(Encoding::convertEncodingToCurrent($city));
            if (is_int(mb_strpos($curentCharSet, $city))) {
                $stringAsArray = explode(';', $string);
                $id = trim($stringAsArray[0], '"');
                $len = count($stringAsArray);
                unset($stringAsArray[0], $stringAsArray[$len - 1]);
                $result['c'.$id] = implode(', ', array_map(function($i) {return trim($i, '"');}, $stringAsArray));
            }
        }

        return $result;
    }
}