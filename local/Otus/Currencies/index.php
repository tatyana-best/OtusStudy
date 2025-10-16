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
	".default", 
	array(
		"CACHE_TIME" => "36000",
		"CACHE_TYPE" => "A",
		"CURRENCY_LIMIT" => "4",
		"CURRENCY_LIST" => "UAH",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?><?php require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";?>