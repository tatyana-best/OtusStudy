<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";

$APPLICATION->SetTitle("Валюты");

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 */

Loc::loadLanguageFile(__FILE__);

?><?$APPLICATION->IncludeComponent(
	"Otus:currency.list",
	"",
	Array(
		"CACHE_TIME" => "36000",
		"CACHE_TYPE" => "A",
		"CURRENCY_LIMIT" => "3",
		"CURRENCY_LIST" => "RUB"
	)
);?><?php require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";?>