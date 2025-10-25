<?php

use \Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandlerCompatible("rest", "OnRestServiceBuildDescription", Array("\\Otus\\Rest\\Example\\CustomRestMethod", "addCustomRestMethods"));

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
