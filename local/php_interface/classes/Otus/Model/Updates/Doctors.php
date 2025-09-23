<?php

namespace Otus\Model\Updates;

use Otus\Model\AbstractIblocksModel;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Iblock;
use \Bitrix\Iblock\Elements;

Loader::includeModule('iblock');

/**
 * класс с методами и свойствами для работы со списком doctors
 * методы реализуют добавление, удаление, изменение элементов
 * инфоблоков doctors и procedure
 */
class Doctors extends AbstractIblocksModel
{
    const PROP_PROCEDURE_ID = 65;
    const PROP_PROCEDURE_NAME = 'PROCEDURE';
    const PROP_PROFILE_NAME = 'PROFILE';
    const DOCTORE_IBLOCK_ID = 16;
    
    public static function getDoctorPropertyProcedureValues(int $elemId): array
    {
        $dbItem = Elements\ElementDoctorsTable::getByPrimary($elemId, [
            'select' => ['ID', static::PROP_PROCEDURE_NAME, static::PROP_PROFILE_NAME],
        ]);

        $arItems = $dbItem->fetchAll();

        $arResult = [];
        foreach ($arItems as $key => $arItem) {
            $arResult[static::PROP_PROCEDURE_NAME][] = $arItem['IBLOCK_ELEMENTS_ELEMENT_DOCTORS_PROCEDURE_IBLOCK_GENERIC_VALUE'];
            $arResult[static::PROP_PROFILE_NAME] = $arItem['IBLOCK_ELEMENTS_ELEMENT_DOCTORS_PROFILE_VALUE'];
        }

        return $arResult;
    }

    public static function deleteProcedureFromDoctor(int $elemId, int $prId): void
    {
        $arValues = self::getDoctorPropertyProcedureValues($elemId);
        unset($arValues[static::PROP_PROCEDURE_NAME][array_search($prId, $arValues[static::PROP_PROCEDURE_NAME])]);
        $el = new \CIBlockElement;
        $arLoadProductArray = Array(
            "PROPERTY_VALUES"=> $arValues,
        );

        $res = $el->Update($elemId, $arLoadProductArray);

        /*
         * здесь не получилось, ошибки нет, Элемент успешно обновлен
         * но изменений нет
         $fields = [
            'PROPERTY_VALUES' => [
                static::PROP_PROCEDURE_NAME => $arValues,
            ]
        ];
           try {
            $res = Elements\ElementDoctorsTable::update($elemId, $fields);
            if ($res->isSuccess()) {
                echo "Элемент успешно обновлен";
            } else {
                $errors = $res->getErrorMessages();
                print_r($errors);
            }
        } catch (\Exception $e) {
            echo "Ошибка: " . $e->getMessage();
        }*/
    }

    public static function addProcedureToDoctor(int $procedure, int $elemId): void
    {
        $arValues = self::getDoctorPropertyProcedureValues($elemId);
        $arValues[static::PROP_PROCEDURE_NAME][] = $procedure;
        
        $el = new \CIBlockElement;
        $arLoadProductArray = Array(
            "PROPERTY_VALUES"=> $arValues,
        );

        $res = $el->Update($elemId, $arLoadProductArray);
    }

    public static function updateProcedure(int $id, int $iblockId): void
    {

    }

    public static function addDoctor(string $fio, string $profile): void
    {
        $arParams = [
            "replace_space" => "-",
            "replace_other" => "-"
        ];
        $transFio = \Cutil::translit($fio,"ru",$arParams);

        $arElementProps = [
            'PROFILE' => $profile,
        ];
        $arIblockFields = [
            'IBLOCK_ID' => static::DOCTORE_IBLOCK_ID,
            'NAME' => $fio,
            'CODE' => $transFio,
            'PROPERTY_VALUES' => $arElementProps
        ];

        $objIblockElement = new \CIBlockElement();
        $objIblockElement->Add($arIblockFields);
    }
}
