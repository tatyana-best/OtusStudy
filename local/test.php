<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Примеры');

$ar = [];
for ($i=0; $i < 10; $i++) { 
    $ar[] = $i;
}

dump($ar);
sage($ar);

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
