<?php

namespace UDTTest\TaskOne;

use Bitrix\Main\Diag\Debug;

/**
 * read json file, get users and closed deals
 */
class ReadJson
{
    const PATH_TO_JSON = __DIR__ . '/mock_data.json';
    public $udtLogFile;

    public function __construct()
    {
        $this->udtLogFile = '/local/logs/log.txt';
    }

    public function readFromJson(): array
    {
        $arResult = [];

        if (file_exists(self::PATH_TO_JSON)) {
            $jsonString = file_get_contents(self::PATH_TO_JSON);
            $arResult = json_decode($jsonString, true);
            if ($arResult === null) {
                return [];
            }
        } else {
            Debug::writeToFile('Файл ' . self::PATH_TO_JSON . ' не существует', 'Запись в лог:', $this->udtLogFile);
            return [];
        }

        return $arResult;
    }

    public function getUsers(): array
    {
        if (array_key_exists('users', $this->readFromJson())) {
            return $this->readFromJson()['users'];
        } else {
            return [];
        }
    }

    public function getClosedDeals(): array
    {
        if (array_key_exists('deals', $this->readFromJson())) {
            $arResult = [];
            foreach ($this->readFromJson()['deals'] as $deal) {
                if (in_array('WON', $deal) || in_array('LOSE', $deal)) {
                    $arResult[] = $deal;
                }
            }
            return $arResult;
        } else {
            return [];
        }
    }

}
