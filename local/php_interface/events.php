<?php

use \Bitrix\Main\EventManager;
use \Bitrix\Main\Diag\Debug;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandlerCompatible("rest", "OnRestServiceBuildDescription", Array("\\Otus\\Rest\\Example\\CustomRestMethod", "addCustomRestMethods"));
$eventManager->addEventHandlerCompatible("rest", "OnRestServiceBuildDescription", Array("\\Otus\\Rest\\Example\\MyDeal", "addCustomRestMethods"));
$eventManager->addEventHandlerCompatible("rest", "OnRestServiceBuildDescription", Array("\\Otus\\Rest\\API\\CRUDMethods", "addCustomRestMethods"));
$eventManager->addEventHandler("crm", "onEntityDetailsTabsInitialized", Array("\\Otus\\Rest\\Example\\MyDeal", "updateTabs"));
$eventManager->addEventHandler("crm", "OnBeforeCrmDealUpdate", Array("\\Otus\\CRM\\Deals\\UpdateRequestsIblockAfterDealUpdate", "updateRequest"));
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("\\Otus\\IBLOCK\\UpdateDealAfterRequestsIblockUpdate", "updateDeal"));

if (CSite::InDir('/crm/deal/details/')){
    AddEventHandler("main", "OnEpilog", Array("HideStatuses", "OnEpilogHandler"));
    class HideStatuses
    {
        static function OnEpilogHandler()
        {
            CJSCore::Init(array("jquery3"));
            $arJsConfig = array(
                'hide_statuses' => array(
                    'js' => '/local/jsLibs/HideStatuses.js',
                    'css' => '/local/jsLibs/HideStatuses.css',
                    'rel' => Array(),
                    'lang' => '/local/jsLibs/lang/' . LANGUAGE_ID . 'lib.php',
                ),

            );

            foreach($arJsConfig as $ext => $arext){
                CJSCore::RegisterExt($ext, $arext);
            }

            CUtil::InitJSCore(array('hide_statuses'));
        }
    }
}

$eventManager->addEventHandlerCompatible(
    "crm",
    "OnAfterCrmControlPanelBuild",
    function( &$menuItems ){
        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/logs/log.txt");
        $arMyLink = [
            'ID' => 'MYDEAL',
            'MENU_ID' => 'menu_crm_custom_mydeal',
            'NAME' => 'Моя сделка',
            'URL' => '/local/Otus/MyDeal/',
            'IS_ACTIVE' => false,
        ];

        foreach ($menuItems as $key => $menuItem) {
            if ($menuItem['ID'] == 'DEAL') {
                //AddMessage2Log("check1: <pre>".print_r($menuItems, true)."</pre>", "bizproc");
                $keyDeal = $key;
            }
        }

        $menuItems[$keyDeal]['ITEMS'][0] = $arMyLink;
    }
);

$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'Otus\UserTypes\IBLink',
        'GetUserTypeDescription'
    ]
);

$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'Otus\UserTypes\Booking',
        'GetUserTypeDescription'
    ]
);

$eventManager->addEventHandlerCompatible(
    'tasks',
    'OnTaskUpdate',
    [
        'Otus\Tasks\MoveResultFilesToDeal',
        'MoveFiles'
    ]
);

$eventManager->addEventHandler('disk', 'onAfterAddFile', function (\Bitrix\Main\Event $event)
{
    CModule::IncludeModule("disk");
    
    list($file) = $event->getParameters();
    
    $fileID = $file->getID();
    $arrType = $file->getFile();
    $path = CFile::GetPath($arrType["ID"]);
 
    if ($file) 
    {
        $fileInfo = $file->getFile();
        $fileName = $file->getName();
        $fileNameOne = substr($fileName, 0, -5);
        $info = new SplFileInfo($fileName);
        $infoFile = $info->getExtension();
        $storageID = "";
    
        if(($infoFile == "heic") || ($infoFile == "HEIC")){
    
            function curl_get_file_contents($URL)
            {
                session_start(); 
                $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/'; 
                session_write_close();
                
                $c = curl_init();
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_COOKIE, $strCookie); 
                curl_setopt($c, CURLOPT_URL, $URL);
                $contents = curl_exec($c);
                curl_close($c);
 
                if ($contents) return $contents;
                    else return FALSE;
            }
            
            //Скачиваем файл себе в папку
            $local_file_path = "";
            $local_file_name = "";
            if ( Loader::IncludeModule('disk') )
            {
                $arDiskFiles = Disk\Internals\FileTable::getList(['filter'=>['ID'=>$fileID]]);
 
                // Работает для версии main старше 17.0
                foreach( $arDiskFiles as $arFile )
                {
                    $storageID = $arFile["STORAGE_ID"];
                    $file_two = Disk\BaseObject::buildFromArray($arFile);
                    // Опять же в $file_two будет объект класса Bitrix\Disk\File
 
                    // ссылка на файл на портале
                    $urlManager = \Bitrix\Disk\Driver::getInstance()->getUrlManager(); 
                    //echo $urlManager->getPathFileDetail($file_two).'<br>'; 
 
                    // публичная ссылка
                    $urlManager = \Bitrix\Disk\Driver::getInstance()->getUrlManager(); 
                    $extLink = $file_two->addExternalLink( 
                        array( 
                            'CREATED_BY' => 1, 
                            'TYPE' => \Bitrix\Disk\Internals\ExternalLinkTable::TYPE_MANUAL, 
                        ) 
                    );
                    $getDataHash = $extLink->getHash();
                    $downloadToken = Random::getString(12);
                    $_SESSION['DISK_PUBLIC_VERIFICATION'][$fileID] = $downloadToken;
                    
                    $extLinkUrl = $urlManager->getShortUrlExternalLink( 
                        array( 
                            'hash' => $getDataHash, 
                            'action' => 'default', 
                        ), 
                        true 
                    );
                    
                    //Ссылка на скачивание файла
                    $input = "https://bitrix24.test.com/docs/pub/".$getDataHash."/download/?&token=".$downloadToken;
                    
                    $result = curl_get_file_contents($input);
                    file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/mobile_heic/".$fileNameOne.".heic", $result);
 
                    
                    $local_file_path = "https://bitrix24.test.com/upload/mobile_heic/".$fileNameOne.".heic";
                    $local_file_name = $fileNameOne.".heic";
                    $htslc = "/upload/mobile_heic/".$fileNameOne.".heic";
                    
                    //Удаляем исходный файл
                    $file->delete($file_two);
                }
            }
    
            //Конвертируем в jpg
            function convertFile($path_file, $file_name, $start_name){
                $result_url_download = "";
                // конвертация
                $query = "https://api.cloudconvert.com/convert?apikey=".CLOUDCONVERT_KEY."&inputformat=heic&outputformat=jpg&input=download&file=".$path_file."&filename=".$file_name."&wait=true&download=true&save=true";
 
                if($curl = curl_init()){
                $headers = array("Content-type: application/x-www-form-urlencoded; charset=utf-8");
                curl_setopt($curl, CURLOPT_URL, $query);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $result_curl = curl_exec($curl);
                curl_close($curl);
                }
 
                $tmpExp = explode("https://", $result_curl);
                $result_url_download = trim("https://".$tmpExp[1]);
 
                return $result_url_download;
            }
            
            $download_file = convertFile($local_file_path, $local_file_name, $htslc);
            
            // скачиваем файл
            if($ch = curl_init($download_file)){
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Googlebot/2.1 (http://www.googlebot.com/bot.html)');
                $output = curl_exec($ch);
 
                // проверить что нет ошибок
                if(substr_count($output, "error") == 0){
                    $fh = fopen($_SERVER['DOCUMENT_ROOT']."/upload/mobile_heic/".$fileNameOne.".jpg", 'w');
                    fwrite($fh, $output);
                    fclose($fh);
                }
            }
 
            //Загружаем новый файл
            $storage = \Bitrix\Disk\Storage::loadById($storageID);
            if ($storage) 
            { 
                $folder = $storage->getRootObject(); 
                $fileArray = \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT']."/upload/mobile_heic/".$fileNameOne.".jpg");
                $file = $folder->uploadFile($fileArray, array(  
                    'CREATED_BY' => 1326 
                ));  
            }
            
            //Удаляем временные файлы
            unlink($_SERVER['DOCUMENT_ROOT']."/upload/mobile_heic/".$fileNameOne.".jpg");
            unlink($_SERVER['DOCUMENT_ROOT']."/upload/mobile_heic/".$fileName);
        }
    }
});

if (CSite::InDir('/crm/deal/details/')) {
   AddEventHandler("main", "OnEpilog", array("DownloadArchive", "OnEpilogHandler"));
    class DownloadArchive
    {
        static function OnEpilogHandler()
        {
            CJSCore::Init(array("jquery3"));
            $arJsConfig = array(
                'my_deal' => array(
                    'js' => '/local/jsLibs/myDeal.js',
                    'css' => '/local/jsLibs/myDeal.css',
                    'rel' => array(),
                    'lang' => '/local/jsLibs/lang/' . LANGUAGE_ID . 'lib.php',
                ),
                'documentButton' => array(
                    'js' => '/local/jsLibs/documentButton/documentButton.js',
                    'css' => '/local/jsLibs/documentButton/documentButton.css',
                    'rel' => array(),
                    'lang' => '/local/jsLibs/lang/' . LANGUAGE_ID . 'lib.php',
                ),
            );

            foreach ($arJsConfig as $ext => $arext) {
                CJSCore::RegisterExt($ext, $arext);
            }

            CUtil::InitJSCore(array('my_deal', 'documentButton'));
        }
    }
}

AddEventHandler("main", "OnEpilog", array("StartOfWorkingDay", "OnEpilogHandler"));
class StartOfWorkingDay
{
    static function OnEpilogHandler()
    {
        CJSCore::Init(array("jquery3", "popup"));
        $arJsConfig = array(
            'workStart' => array(
                'js' => '/local/jsLibs/workStart/workStart.js',
                'css' => '/local/jsLibs/workStart/workStart.css',
                'rel' => array(),
                'lang' => '/local/jsLibs/lang/' . LANGUAGE_ID . 'lib.php',
            ),
        );

        foreach ($arJsConfig as $ext => $arext) {
            CJSCore::RegisterExt($ext, $arext);
        }

        CUtil::InitJSCore(array('workStart'));
    }
}
