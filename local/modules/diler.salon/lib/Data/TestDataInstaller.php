<?php

namespace Diler\Salon\Data;

use Diler\Salon\Orm\CarTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;

class TestDataInstaller
{
    /**
     * @throws SystemException
     * @throws \Exception
     */
    public static function addCars(): void
    {
        $cars = [
            [
                'MARKA' => 'Chevrolet',
                'MODEL' => 'NIVA',
                'NUMBER' => 'А123БВ 77',
                'YEAR' => 2023,
                'COLOR' => 'черный',
                'KM' => 3400,
                'CONTACT_ID' => 1
            ],
            [
                'MARKA' => 'Chevrolet',
                'MODEL' => 'ORLANDO',
                'NUMBER' => 'П123ВЫ 54',
                'YEAR' => 2022,
                'COLOR' => 'белый',
                'KM' => 2400,
                'CONTACT_ID' => 1
            ],
            [
                'MARKA' => 'Citroen',
                'MODEL' => 'Berlingo',
                'NUMBER' => 'Х542ОР 123',
                'YEAR' => 2000,
                'COLOR' => 'серебристый',
                'KM' => 98700,
                'CONTACT_ID' => 8
            ],
            [
                'MARKA' => 'Chrysler',
                'MODEL' => '300C',
                'NUMBER' => 'Е542ШО 837',
                'YEAR' => 2012,
                'COLOR' => 'красный',
                'KM' => 2700,
                'CONTACT_ID' => 8
            ],
            [
                'MARKA' => 'Daewoo',
                'MODEL' => 'Nexia 1',
                'NUMBER' => 'Ы282МИ 492',
                'YEAR' => 2019,
                'COLOR' => 'синий',
                'KM' => 9200,
                'CONTACT_ID' => 6
            ],
            [
                'MARKA' => 'FORD',
                'MODEL' => 'Explorer 5',
                'NUMBER' => 'Л932ЛА 36',
                'YEAR' => 2012,
                'COLOR' => 'зеленый',
                'KM' => 19000,
                'CONTACT_ID' => 3
            ],
            [
                'MARKA' => 'Honda',
                'MODEL' => 'Accord 7',
                'NUMBER' => 'Д462ТМ 27',
                'YEAR' => 2004,
                'COLOR' => 'синий',
                'KM' => 87000,
                'CONTACT_ID' => 2
            ],
            [
                'MARKA' => 'Hyundai',
                'MODEL' => 'GRANDEUR',
                'NUMBER' => 'П846СИ 84',
                'YEAR' => 2024,
                'COLOR' => 'белый',
                'KM' => 12000,
                'CONTACT_ID' => 1
            ],
            [
                'MARKA' => 'Infiniti',
                'MODEL' => 'седан',
                'NUMBER' => 'Ф956ДТ 94',
                'YEAR' => 2025,
                'COLOR' => 'черный',
                'KM' => 7000,
                'CONTACT_ID' => 3
            ],
            [
                'MARKA' => 'Lexus',
                'MODEL' => 'ES',
                'NUMBER' => 'М107ЛИ 28',
                'YEAR' => 2005,
                'COLOR' => 'белый',
                'KM' => 84500,
                'CONTACT_ID' => 4
            ],
            [
                'MARKA' => 'Mazda',
                'MODEL' => 'седан',
                'NUMBER' => 'Д574РС 03',
                'YEAR' => 2015,
                'COLOR' => 'зеленый',
                'KM' => 51500,
                'CONTACT_ID' => 4
            ],
            [
                'MARKA' => 'Mercedes-Benz',
                'MODEL' => 'A W176',
                'NUMBER' => 'Л103фД 58',
                'YEAR' => 2020,
                'COLOR' => 'серебристый',
                'KM' => 89500,
                'CONTACT_ID' => 4
            ],
            [
                'MARKA' => 'Mitsubishi',
                'MODEL' => 'Carisma',
                'NUMBER' => 'П923ПО 84',
                'YEAR' => 2021,
                'COLOR' => 'зеленый',
                'KM' => 10500,
                'CONTACT_ID' => 7
            ],
            [
                'MARKA' => 'Nissan',
                'MODEL' => 'Juke',
                'NUMBER' => 'О298ДЛ 92',
                'YEAR' => 2022,
                'COLOR' => 'черный',
                'KM' => 18700,
                'CONTACT_ID' => 7
            ],
            [
                'MARKA' => 'Opel',
                'MODEL' => 'Antara',
                'NUMBER' => 'Ч394БИ 27',
                'YEAR' => 2022,
                'COLOR' => 'белый',
                'KM' => 12100,
                'CONTACT_ID' => 8
            ]
        ];

        foreach ($cars as $carData) {
            $resultAdd = CarTable::add($carData);
            if (!$resultAdd->isSuccess()) {
                throw new SystemException('Не удалось добавить тестовые данные: ' . implode(', ', $resultAdd->getErrorMessages()));
            }
        }
    }
}
