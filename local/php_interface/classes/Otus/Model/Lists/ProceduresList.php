<?php

namespace Otus\Model\Lists;

use Otus\Model\AbstractIblocksModel;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Iblock;

Loader::includeModule('iblock');

/**
 * класс с методами и свойствами для работы со списками procedures
 * методы возвращают список элементов либо элемент
 */
class ProceduresList extends AbstractIblocksModel
{
    const IBLOCK_ID = 17;
    public static $arFields = ['PRICE'];

    /**
     * получаем также все значения множественного свойства color
     */
    public static function getTableElementList(array $fields = []): array
    {
        $list = parent::getTableElementList(self::$arFields);

        $arResult = [];
        foreach ($list as $key => $item) {
            $arOrder = [];
            $arFilter = ["CODE" => "COLOR"];
            $arResult[$key] = $item;
            $rsProps = \CIBlockElement::GetProperty(static::IBLOCK_ID, $item['ID'], $arOrder, $arFilter);
            while($arrProps = $rsProps->Fetch()) {
                $arResult[$key]['COLOR'][] = $arrProps['VALUE'];
            }
        }

        return $arResult;
    }
}
