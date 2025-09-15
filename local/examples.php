<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Примеры');

//check how classes reloading works
use MyDebug\DebugFirst;
use MyDebug\DebugSecond;

$ar = [];
for ($i=0; $i < 10; $i++) { 
    sleep(2);
    $ar[] = $i;
}

echo "<pre>" .print_r($ar, true). "</pre>";

//check how composer works
dump($ar);
sage($ar);

$ob = new DebugFirst();
$ob->printVar([1, 2, 3, 5, 8]);

$ob2 = new DebugSecond();
$ob2->printVar("Ура!!!");

//check custom debugging
$t = 9/0;

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
