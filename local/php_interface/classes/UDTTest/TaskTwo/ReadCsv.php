<?php

namespace UDTTest\TaskTwo;

use UDTTest\TaskTwo\Db;


/**
 * read from csv file, create, full table products, add and update record
 */
class ReadCsv extends Db
{
    public $filePath = '';
    public $arTitle = [
        'name' => 'VARCHAR(50) NOT NULL',
        'art' => 'VARCHAR(20)',
        'price' => 'INT',
        'quantity' => 'INT'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->filePath = __DIR__ . '/product.csv';
    }

    public function readCSV(): array
    {
        $file = fopen($this->filePath, "r");
        if ($file) {
            $arResult = [];
            $i = 0;
            while (($row = fgetcsv($file)) !== false) {
                $arData = explode(';', $row[0]);
                $j = 0;
                foreach ($this->arTitle as $key => $value) {
                    $arResult[$i][$key] = $arData[$j];
                    $j ++;
                }
                $i ++;
            }
            fclose($file);
        }

        return $arResult;
    }

    public function createTableProducts(): bool
    {
        $arResult = $this->createTable("products", $this->arTitle);

        return $arResult;
    }

    public function isRecordExists(array $data): int
    {
        $arResult = $this->findBy('products', ['id'], $data)[0];

        if ($arResult) {
            return $arResult['id'];
        }

        return 0;
    }

    public function fullProductsWithDataFromCsv(): bool
    {
        $count = count($this->readCSV());

        $arData = $this->readCSV();
        for ($i = 1; $i < $count; $i++) {
            if (!$this->isRecordExists(['name' => $arData[$i]['name'], 'art' => $arData[$i]['art']])) {
                $this->insertOne("products", $arData[$i]);
            }
        }

        return true;
    }

    public function insertOneIntoProducts(array $data): bool
    {
        if (!$this->isRecordExists(['name' => $data['name'], 'art' => $data['art']])) {
            $this->insertOne("products", $data);

            return true;
        }

        return false;
    }

    public function updateOneIntoProducts(array $data): bool
    {
        $id = $this->isRecordExists(['name' => $data['name'], 'art' => $data['art']]);
        if ($id > 0) {
            $this->updateOne("products",$id, $data);

            return true;
        }

        return false;
    }

    public function getAllProducts(): array
    {
        $data = $this->findAll("SELECT * FROM products");

        if ($data) {
            return $data;
        }

        return [];
    }
}
