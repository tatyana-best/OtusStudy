<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Примеры');
require_once __DIR__ . "/php_interface/functions.php";
use Bitrix\Main\Diag\Debug;
//Debug::startTimeLabel("foo");
$ar = [];
for ($i=0; $i < 10; $i++) { 
    //sleep(2);
    $ar[] = $i;
}
//Debug::endTimeLabel("foo");

//dump($ar);
//sage($ar);
echo "<pre>".print_r($ar, true)."</pre>";
pr($ar);

//Debug::writeToFile(Debug::getTimeLabels());
//Debug::dump(Debug::getTimeLabels());
        

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
