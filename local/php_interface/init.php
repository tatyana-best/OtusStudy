<?php

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\UI\Extension;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

if (file_exists(__DIR__ . '/events.php')) {
    require_once __DIR__ . '/events.php';
}

if (file_exists(__DIR__ . '/functions.php')) {
    require_once __DIR__ . '/functions.php';
}

Extension::load([
    'otus.crm.negative_currency',
]);


/*spl_autoload_register(function($sClassName)
{
    $sClassFile = __DIR__ . '/classes';

    if ( file_exists($sClassFile . '/' . str_replace('\\', '/', $sClassName) . '.php') )
    {
        require_once($sClassFile . '/'.str_replace('\\', '/', $sClassName) . '.php');
        return;
    }

    $arClass = explode('\\', strtolower($sClassName));
    foreach($arClass as $sPath )
    {
        $sClassFile .= '/' . ucfirst($sPath);
    }

    $sClassFile .= '.php';
    if (file_exists($sClassFile))
    {
        require_once($sClassFile);
    }
});*/
