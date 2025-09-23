<?php

namespace Otus\Model;

use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use CIBlockElement;
use \Bitrix\Iblock\Elements;

/**
 * класс с общими методами, которые пригодятся для работы
 * с инфоблоками, списками
 */
abstract class AbstractIblocksModel extends DataManager
{
    const IBLOCK_ID = 0;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'b_iblock_element_prop_s' . static::IBLOCK_ID;
    }

    /**
     * @return string
     */
    public static function getTableNameMulti(): string
    {
        return 'b_iblock_element_prop_m' . static::IBLOCK_ID;
    }

    /**
     * метод принимает массив нужных пользовательских свойств элемента
     * инфоблока. На выходе получаем массив со всеми элементами инфоблока,
     * его полями 'ID', 'NAME', 'IBLOCK_ID', 'CODE', 'PREVIEW_PICTURE'
     * и его пользовательскими свойствами, заданными на входе
     */
    public static function getTableElementList(array $fields = []): array
    {
        $dbItems = ElementTable::getList(array(
            'select' => ['ID', 'NAME', 'IBLOCK_ID', 'CODE', 'PREVIEW_PICTURE'],
            'filter' => ['IBLOCK_ID' => static::IBLOCK_ID]
        ));

        $items = [];
        while ($arItem = $dbItems->fetch()){
            $arItem['PREVIEW_PICTURE'] = \CFile::GetPath($arItem['PREVIEW_PICTURE']);
            $dbProperty = \CIBlockElement::getProperty(
                $arItem['IBLOCK_ID'],
                $arItem['ID']
            );
            while($arProperty = $dbProperty->Fetch()){
                if (in_array($arProperty['CODE'], $fields)) {
                    $arItem[$arProperty['CODE']] = $arProperty['VALUE'];
                }
            }
            $items[] = $arItem;
        }

        return $items;
    }

    /**
     * метод принимает параметры:
     * id элемента инфоблока
     * $property множественное свойство, данные которого нужно получить
     * $fields массив пользовательских свойств (не множественные), значения которых надо получить
     * $multy массив множественных пользовательских свойств, значения которых надо получить
     * В итоге получаем массив значений нужных пользовательских свойств пользовательского свойства
     * элемента инфоблока
     */
    public static function getMultyPropertyValues(int $id, string $property, array $fields = [], array $multy = []): array
    {
        $fieldsName = [$property . '.ELEMENT.NAME', $property . '.ELEMENT.ID'];

        foreach ($fields as $field) {
            $fieldsName[] = $property . '.ELEMENT.' . $field;
        }

        foreach ($multy as $field) {
            $fieldsName[] = $property . '.ELEMENT.' . $field;
        }

        $select = array_merge(['ID', 'NAME'], $fieldsName);
        $elements = Elements\ElementDoctorsTable::getList([
            'select' => $select,
            'filter' => [
                'ID' => $id,
                'ACTIVE' => 'Y',
            ],
        ])
            ->fetchCollection();

        $arResult = [];
        foreach ($elements as $element) {
            foreach($element->get($property)->getAll() as $key => $prItem) {
                $arResult[$key]['NAME'] = $prItem->getElement()->getName();
                $arResult[$key]['ID'] = $prItem->getElement()->getId();
                foreach ($fields as $field) {
                    if ($prItem->getElement()->get($field)!== null){
                        $arResult[$key][$field] = $prItem->getElement()->get($field)->getValue();
                    }
                }

                foreach ($multy as $field) {
                     foreach($prItem->getElement()->get($field)->getAll() as $k => $val) {
                         $arResult[$key][$field][$k] = $val->getValue();
                     }
                }
            }
        }

        return $arResult;
    }

    /**
     * метод принимает символьный код элемента инфоблока
     * вызвращает id элемента
     */
    public static function getElementIdByCode(string $code): int
    {
        $elemId = ElementTable::getList(['filter'=>['CODE'=>$code]])->Fetch()["ID"];

        return intval($elemId);
    }

    /**
     * метод принимает символьный код элемента инфоблока
     * вызвращает name элемента
     */
    public static function getElementNameByCode(string $code): string
    {
        $elemName = ElementTable::getList(['filter'=>['CODE'=>$code]])->Fetch()["NAME"];

        return $elemName;
    }

    public static function deleteElement(int $id, int $iblockId): void
    {
        global $DB;
        if (\CIBlock::GetPermission($iblockId)>='W') {
            $strWarning = '';
            $DB->StartTransaction();
            if (!\CIBlockElement::Delete($id)) {
                $strWarning .= 'Error!';
                $DB->Rollback();
            }
            else
                $DB->Commit();
        }
    }
}
