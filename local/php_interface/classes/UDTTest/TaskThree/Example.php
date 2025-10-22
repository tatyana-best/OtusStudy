<?php

namespace UDTTest\TaskThree;

use Bitrix\Main\Diag\Debug;

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/UDTTest/dbConnection.php';

/**
 * correct code
 */
class Example
{
    private static $link;
    public $udtLogFile;

    public function __construct()
    {
        if (!self::$link) {
            self::$link = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        }

        $this->udtLogFile = '/local/logs/log.txt';
    }

    public function findAll(): array
    {
        $arResult = [];
        try {
            $query = "SELECT * FROM products";
            $stmt = self::$link->prepare($query);
            $stmt->execute();
            $arData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($arData as $key => $row) {
                $arResult[$key] = $row['name'];
            }
        } catch (\PDOException $e) {
            Debug::writeToFile($e->getMessage(), 'Ошибка при выборке данных из таблицы products: ', $this->udtLogFile);
        }

        return $arResult;
    }
}
