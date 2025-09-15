<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Вывод текущей даты');

use Bitrix\Main\Diag\Debug;

$currentDate = date('d.m.Y H:i:s');
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/" . DEBUG_FILE_NAME);

Debug::writeToFile($currentDate, "Текущая дата и время", DEBUG_FILE_NAME);
Debug::dump($currentDate);


AddMessage2Log($currentDate, "main");

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
