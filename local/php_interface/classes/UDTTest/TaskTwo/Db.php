<?php

namespace UDTTest\TaskTwo;

use Bitrix\Main\Diag\Debug;

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/UDTTest/dbConnection.php';

/**
 * description DB queries
 */
class Db
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

    protected function findBy(string $tableName, array $fields, array $data): array
    {
        $strQuery = "SELECT ";
        if ($fields) {
            $strQuery .=  implode(", ", $fields) . " FROM " . $tableName;
        }

        if ($data) {
            $arVal = [];
            foreach ($data as $key => $value) {
                $arVal[$key] = "$key=:$key";
            }
            $strQuery .= " WHERE " . implode(' AND ', $arVal) . ";";
        } else {
            $strQuery = ";";
        }

        $stmt = self::$link->prepare($strQuery);
        foreach ($data as $key => $value) {
            $stmt->bindValue("$key", $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function findAll(string $query): array
    {
        $stmt = self::$link->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function insertOne(string $tableName, array $fields): bool
    {
        $arField = $arVal = $arExec = [];
        foreach ($fields as $key => $value) {
            $arVal[] = ":$key";
            $arField[] = "$key";
            $arExec[$key] = $fields[$key];
        }

        $strVal = implode(", ", $arVal);
        $strField = implode(", ", $arField);
        try {
            $sql = "INSERT INTO " . $tableName . " (" . $strField . ") VALUES (" . $strVal . ")";

            $stmt = self::$link->prepare($sql);
            $stmt->execute($arExec);
        } catch (\PDOException $e) {
            Debug::writeToFile($e->getMessage(), 'Ошибка при добавлении записи в таблицу ' . $tableName . ':', $this->udtLogFile);

            return false;
        }

        return true;
    }

    public function updateOne(string $tableName, int $id, array $fields): bool
    {
        $stmt = self::$link->prepare("SELECT * FROM " . $tableName . " WHERE id=:id");
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $arResult = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($arResult) {
            $sqlAr = [];
            $arVal = [];
            foreach ($fields as $key => $value) {
                $sqlAr[] = "$key=:$key";
                $arVal[$key] = $fields[$key];
            }

            $sqlStr = implode(", ", $sqlAr);

            try {
                $stmt = self::$link->prepare("UPDATE " . $tableName . " SET " . $sqlStr . " WHERE id=:id");

                $stmt->bindValue("id", $id);
                foreach ($arVal as $key => $value) {
                    $stmt->bindValue("$key", $value);
                }
                $stmt->execute();
            } catch (\PDOException $e) {
                Debug::writeToFile($e->getMessage(), 'Ошибка при изменении записи в таблице ' . $tableName . ':', $this->udtLogFile);

                return false;
            }


        } else {
            return false;
        }

        return true;
    }

    public function createTable(string $tableName, array $fieldsTypes): bool
    {
        $arFields = [];
        foreach ($fieldsTypes as $key => $fieldType) {
            $arFields[] = $key . ' ' . $fieldType;
        }

        $strFields = implode(", ", $arFields);

        try {
            $query = "CREATE TABLE IF NOT EXISTS " . $tableName . " (id INT PRIMARY KEY AUTO_INCREMENT, " . $strFields . ")";

            $stmt = self::$link->prepare($query);
            $stmt->execute();
        } catch (\PDOException $e) {
            Debug::writeToFile($e->getMessage(), 'Ошибка при создании таблицы: ' . $tableName, $this->udtLogFile);

            return false;
        }

        return true;
    }
}
