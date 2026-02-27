<?php

namespace Sotbit\B2bcabinet\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\BFile;
use Bitrix\Main\Engine\UrlManager;
use Bitrix\Main\IO\File;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Engine\Response\Redirect;

class FileController extends Controller
{
    public function configureActions()
    {
        return [];
    }

    public function fileDownloadAction(int $fileId, string $fileName): BFile
    {
        $fileAr = \CFile::GetFileArray($fileId);
        $file = new File(Application::getDocumentRoot() . $fileAr['SRC']);
        $extension = $file->getExtension();
        return new BFile($fileAr, "$fileName.$extension");
    }

    public function DocumentDeleteAction(int $documentID, string $backUrl): Redirect
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            \CIBlockElement::Delete($documentID);
        }
        return new Redirect($backUrl);
    }

    public static function urlGenerate(string $action, array $queryParams): Uri
    {
        $controller = "sotbit:b2bcabinet.FileController.{$action}";
        $queryParams['sessid'] = bitrix_sessid();
        return UrlManager::getInstance()->create($controller, $queryParams);
    }
}