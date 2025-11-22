<?php

namespace Otus\Tasks;

use Bitrix\Crm\Service;
use \Bitrix\Main\Context;
use Bitrix\Crm\Item;
use Bitrix\Disk\File;
use Bitrix\Main\Loader;
use Bitrix\Crm\DealTable;
use Bitrix\Main\Diag\Debug;

\Bitrix\Main\Loader::includeModule('disk');
\Bitrix\Main\Loader::IncludeModule('crm');
\Bitrix\Main\Loader::includeModule('tasks');

class MoveResultFilesToDeal
{
    public static $categoryTender = 1;
    public static $statusClosed = 5;
    //public static $fieldFilesCode = 'UF_CRM_1712825435374';
    public static $fieldFilesCode = 'UF_DOCUMENTS';
    public static $urlToHookDisk = 'https://b24mybeget.ru/rest/1/amk6xfu7682d4h6d/disk.file.get'; //rigts for tasks and disk
    public static $urlToHookTask = 'https://b24mybeget.ru/rest/1/amk6xfu7682d4h6d/tasks.task.result.list'; //rigts for tasks and disk

    public static function MoveFiles($taskId, $editedFields, $originalFields)
    {
        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/local/logs/log.txt");
        $crm = explode('_', $editedFields['META:PREV_FIELDS']['UF_CRM_TASK'][0]);
        $status = $editedFields['STATUS'] ?? 0;
        
        if ($crm[0] == 'D') {
            $crmDealId = $crm[1];
            $factory = Service\Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
            $item = $factory->getItem($crmDealId);
            $categoryId = $item->get('CATEGORY_ID');

            if ($categoryId == self::$categoryTender && $status == self::$statusClosed) {
                self::sendFilesToDeals(self::getFiles($taskId), $crmDealId, $item, $factory);
            }            
        }
    }

    public static function getFiles($taskId)
    {
        $data = array(
            'taskId'  => $taskId
        );

        $resComments = self::connectToHook(self::$urlToHookTask, $data);
        
        $arFiles = [];
        foreach ($resComments as $comment) {
            if (!empty($comment['files'])) {
                foreach ($comment['files'] as $file) {
                    $arFiles[$file] = $file;
                }
            }
        }
        
        return $arFiles;
    }

    public static function sendFilesToDeals($arFiles, $dealId, $item, $factory)
    {
        global $DB;        

        $arFilesCopy = [];
        $myFiles = [];
        foreach($arFiles as $fileId){
            $results = $DB->Query("SELECT OBJECT_ID FROM b_disk_attached_object WHERE ID=" . $fileId);
            if ($row = $results->Fetch()) {
                $idResultFile = self::getFileOfDiskId($row['OBJECT_ID']);
                $myFiles[] = $idResultFile;
                $arFilesCopy[] = \CFile::MakeFileArray($idResultFile);
            }            
        }

        $fields = [
            self::$fieldFilesCode => $arFilesCopy,
        ];

        $errorMessage = '';

        if (!$dealId) {
            $errorMessage = "КРИТИЧНО: Не указан ID сделки для обновления";
        } else {
            if (!$arFiles) {
                $errorMessage = "В задаче нет файлов";
            } else {
                $result = DealTable::update($dealId, $fields);

                if ($result->isSuccess()) {
                    $errorMessage = "Файлы в сделку добавлены";
                } else {
                    $errorMessage = "ОШИБКА: Не удалось обновить сделку ID: " . $dealId . ". " . implode(', ', $result->getErrorMessages());               
                }
            }
        }
        
        Debug::writeToFile($errorMessage);
    }

    public static function getFileOfDiskId($id)
    {
        $data = array(
            'id'  => $id,
        );

        return self::connectToHook(self::$urlToHookDisk, $data)['FILE_ID'];      
    }

    public static function connectToHook(string $url, array $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, true)['result'];

        return $res;
    }
}
