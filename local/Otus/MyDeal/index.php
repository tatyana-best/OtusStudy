<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Получаем сделку');

CJSCore::init(array("jquery3"));

Bitrix\Main\UI\Extension::load("myextensions.myDeal");

?>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
