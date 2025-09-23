<?php

namespace Otus\Model\Lists;

use Otus\Model\AbstractIblocksModel;

/**
 * класс с методами и свойствами для работы со списком doctors
 * методы возвращают список элементов либо элемент
 */
class DoctorsList extends AbstractIblocksModel
{
    const IBLOCK_ID = 16;
    public static $arFields = ['PROFILE'];
    public static $arMultyFields = ['PRICE'];
    public static $arMultyMultyFields = ['COLOR'];

    /**
     * отличается от родитеского метода тем, что получает еще и путь к элементу
     */
    public static function getTableElementList(array $fields = []): array
    {
        $list = parent::getTableElementList(self::$arFields);
        
        $arResult = [];
        foreach ($list as $key => $item) {
            $arResult[$key] = $item;
            $arResult[$key]['PATH'] = '/local/Otus/Doctors/' . $item['CODE'];
        }

        return $arResult;
    }

    public static function getMultyPropertyValues(int $id, string $property, array $fields = [], array $multy = []): array
    {
        $list = parent::getMultyPropertyValues($id, $property, self::$arMultyFields, self::$arMultyMultyFields);

        return $list;
    }
}
