<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Выгрузка в эксель');
$APPLICATION->IncludeComponent('bitrix:ui.sidepanel.wrapper', '', [
    'POPUP_COMPONENT_NAME' => 'Otus:book.grid',
    'POPUP_COMPONENT_TEMPLATE_NAME' => '',
    'POPUP_COMPONENT_PARAMS' => [
        'BOOK_PREFIX' => 'TEST ',
        'ORM_CLASS' => \Otus\ORM\BookTable::class,
    ],
]);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';