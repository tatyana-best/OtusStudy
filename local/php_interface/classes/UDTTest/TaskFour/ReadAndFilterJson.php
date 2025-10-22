<?php

namespace UDTTest\TaskFour;

use Bitrix\Main\Diag\Debug;

/**
 * read from json file and filter deals with jquery
 */
class ReadAndFilterJson
{
    const PATH_TO_JSON = __DIR__ . '/mock_deals.json';
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

    public function getStatuses(): array
    {
        $statuses = [];
        if ($this->readFromJson()) {
            foreach ($this->readFromJson() as $deal) {
                $statuses[$deal['status']] = $deal['status'];
            }
        }

        return $statuses;
    }
}
