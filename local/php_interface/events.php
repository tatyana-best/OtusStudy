<?php

use \Bitrix\Main\EventManager;
use \Bitrix\Main\Diag\Debug;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandlerCompatible("rest", "OnRestServiceBuildDescription", Array("\\Otus\\Rest\\Example\\CustomRestMethod", "addCustomRestMethods"));
$eventManager->addEventHandlerCompatible("rest", "OnRestServiceBuildDescription", Array("\\Otus\\Rest\\Example\\MyDeal", "addCustomRestMethods"));
$eventManager->addEventHandler("crm", "onEntityDetailsTabsInitialized", Array("\\Otus\\Rest\\Example\\MyDeal", "updateTabs"));

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
        'Otus\UserTypes\IBLink', // класс обработчик пользовательского типа свойства
        'GetUserTypeDescription'
    ]
);

$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'Otus\UserTypes\Booking', // класс обработчик пользовательского типа свойства
        'GetUserTypeDescription'
    ]
);

//if (CSite::InDir('/local/Otus/MyDeal/')) {
 /*   AddEventHandler("main", "OnEpilog", array("MyDeal", "OnEpilogHandler"));

    class MyDeal
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

            );

            foreach ($arJsConfig as $ext => $arext) {
                CJSCore::RegisterExt($ext, $arext);
            }

            CUtil::InitJSCore(array('my_deal'));
        }
    }*/
//}
