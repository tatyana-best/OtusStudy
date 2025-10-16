<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = [
	"NAME" => GetMessage("QUESTIONS_NAME"),
	"DESCRIPTION" => GetMessage("QUESTIONS_DESCRIPTION"),
	"CACHE_PATH" => "Y",
	"PATH" => [
		"ID" => "service",
		"CHILD" => [
			"ID" => "questions",
			"NAME" => GetMessage("QUESTIONS_SERVICE")
		]
	],
];
