<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage("CURRENCY_LIST_COMPONENT_NAME"),
    "DESCRIPTION" => Loc::getMessage("CURRENCY_LIST_COMPONENT_DESC"),
    "CACHE_PATH" => "Y",
    "COMPLEX" => "N",
    "PATH" => [
        "ID" => "content",
        "CHILD" => [
            "ID" => "CURRENCY",
            "NAME" => Loc::getMessage("CURRENCY_LIST_COMPONENT_NAME"),
        ],
    ],
];
