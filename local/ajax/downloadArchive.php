<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Crm\Service;
use \Bitrix\Main\Context;
use Bitrix\Crm\Item;

\Bitrix\Main\Loader::IncludeModule('crm');

Loader::includeModule("iblock");

$request = Context::getCurrent()->getRequest();

if ($request->isPost()) {
    $postData = $request->getPostList()->toArray();

    $ok = $postData['ok'] ?? '';
    if ($ok) {
        unlink($_SERVER["DOCUMENT_ROOT"] . "/local/ajax/" . "download.zip");
        $arRes = [
            'message' => 'Архив удален',
        ];

        echo json_encode($arRes);
    }

    $id = $postData['id'] ?? 0;

    if ($id) {
        $factory = Service\Container::getInstance()->getFactory(CCrmOwnerType::Deal);
        $item = $factory->getItem($id);

        $zip_file_name_with_location = $_SERVER["DOCUMENT_ROOT"] . "/local/ajax/" . "download.zip";
        touch($zip_file_name_with_location);
        $zip = new ZipArchive;
        $opening_zip = $zip->open($zip_file_name_with_location);
        
        $arFiles = [];
        foreach ($item->get('UF_DOCUMENTS') as $key => $file) {
            $arFiles[] = CFile::GetPath($file);
            $filePath = $_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($file);
            if (file_exists($filePath)) {
                if ($zip->addFile($filePath, basename($filePath))) {
                    error_log("Файл добавлен: " . basename($filePath));
                } else {
                    error_log("Не удалось добавить файл: " . basename($filePath));
                }             
            } else {
                error_log("Файл не существует " . basename($filePath));
            }            
        }

        try {
            $fh = fopen($zip_file_name_with_location, 'r');
            fclose($fh);
            if ($zip->close()) {
                error_log("Архив закрыт");
            }            
        } catch (Exception $e) {            
            error_log($e->getMessage());
        }        

        $arRes = [
            'message' => 'Архив создан',
        ];

        echo json_encode($arRes);
    }
}
