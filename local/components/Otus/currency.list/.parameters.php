<?php

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loader::includeModule('currency');

$currencyList = CurrencyManager::getCurrencyList();

$arComponentParameters = [
    'PARAMETERS' => [
        'CURRENCY_LIST' => Array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('CURRENCY_LIST_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => $currencyList,
            'DEFAULT' => 'RUB',
        ),
        'CURRENCY_LIMIT' => Array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('CURRENCY_LIST_LIMIT'),
            'TYPE' => 'INT',
            'DEFAULT' => 4,
        ),
        "CACHE_TIME" => ["DEFAULT" => 36000000],
    ]
];
