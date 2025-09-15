<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Примеры');

use Bitrix\Main\Diag\Debug;
Debug::startTimeLabel("foo");
$ar = [];
for ($i=0; $i < 10; $i++) { 
    sleep(2);
    $ar[] = $i;
}
Debug::endTimeLabel("foo");

dump($ar);
sage($ar);

Debug::writeToFile(Debug::getTimeLabels());
Debug::dump(Debug::getTimeLabels());
        

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
