<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("announcement"))
	return;

$arComponentParameters = [
    "TEXT" => [
        "NAME" => GetMessage("ANNOUNCEMENT_TEXT"),
        "TYPE" => "STRING",
        "PARENT" => "DATA_SOURCE",
        "DEFAULT" => COption::GetOptionString('announcement', 'text'),
    ],
    "CACHE_TIME"  =>  ["DEFAULT"=>36000000],
    "CACHE_GROUPS" => [
        "PARENT" => "CACHE_SETTINGS",
        "NAME" => GetMessage("CP_BN_CACHE_GROUPS"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ],
];
