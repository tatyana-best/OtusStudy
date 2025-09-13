<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Примеры');

$ar = [];
for ($i=0; $i < 10; $i++) { 
    $ar[] = $i;
}

dump($ar);
sage($ar);

Debug::writeToFile($_SERVER);
Debug::dump($_SERVER);

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
