<?php

/**
 * @var CMain $APPLICATION
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";

$APPLICATION->SetTitle("Вопросы и ответы");

$APPLICATION->IncludeComponent(
	"questions:questions",
	"",
Array()
);

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";