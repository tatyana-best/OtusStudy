<?php

use \Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandlerCompatible("rest", "OnRestServiceBuildDescription", Array("\\Otus\\Rest\\Example\\CustomRestMethod", "addCustomRestMethods"));
