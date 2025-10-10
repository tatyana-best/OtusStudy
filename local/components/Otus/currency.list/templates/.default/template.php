<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
* @global CMain $APPLICATION
*/

$nav = new \Bitrix\Main\UI\PageNavigation('report_list');
$nav->setRecordCount($arResult['COUNT']);
$nav->allowAllRecords(false)->setPageSize($arResult['NUM_PAGE'])->initFromUri();

?>
<ul>
    <li><a href="/bitrix/admin/fileman_admin.php?PAGEN_1=1&SIZEN_1=20&lang=ru&site=s1&path=%2Flocal%2Fcomponents%2FOtus&show_perms_for=0&fu_action=" target="_blank">Компонент в админке</a></li>
    <li><a href="/bitrix/admin/fileman_admin.php?PAGEN_1=1&SIZEN_1=20&lang=ru&site=s1&path=%2Flocal%2FOtus%2FCurrencies&show_perms_for=0&fu_action=" target="_blank">Данная страница в админке</a></li>
</ul>
<?php $APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => 'MY_GRID_ID',
        'COLUMNS' => $arResult["COLUMNS"],
        'ROWS' => $arResult["LIST"],
        'AJAX_MODE' => 'Y',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        "NAV_OBJECT" => $nav,
        "TOTAL_ROWS_COUNT" => $arResult['COUNT']
    ]
);?>
