<?php

namespace Otus\Model;

use Bitrix\Main;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use CIBlockElement;
use \Bitrix\Iblock\Elements;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\SystemException;

/**
 * класс с общими методами, которые пригодятся для работы
 * с инфоблоками, списками
 */
abstract class AbstractIblocksModel extends DataManager
{
    const IBLOCK_ID = 0;

    protected static ?array $properties = null;
    protected static ?CIBlockElement $iblockElement = null;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'b_iblock_element_prop_s'.static::IBLOCK_ID;
    }

    /**
     * @return string
     */
    public static function getTableNameMulti(): string
    {
        return 'b_iblock_element_prop_m'.static::IBLOCK_ID;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap(): array
    {
        $cache = Cache::createInstance();
        $cacheDir = 'iblock_property_map/'.static::IBLOCK_ID;

        $multipleValuesTableClass = static::getMultipleValuesTableClass();

        static::initMultipleValuesTableClass();

        if ($cache->initCache(3600, md5($cacheDir), $cacheDir)) {
            $map = $cache->getVars();
        } else {
            $cache->startDataCache();

            $map['IBLOCK_ELEMENT_ID'] = new IntegerField('IBLOCK_ELEMENT_ID', ['primary' => true]);
            $map['ELEMENT'] = new ReferenceField(
                'ELEMENT',
                ElementTable::class,
                ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID']
            );

            foreach (static::getProperties() as $property) {
                if ($property['MULTIPLE'] === 'Y') {
                    $map[$property['CODE']] = new ExpressionField(
                        $property['CODE'],
                        sprintf('(select group_concat(`VALUE` SEPARATOR "\0") as VALUE from %s as m where m.IBLOCK_ELEMENT_ID = %s and m.IBLOCK_PROPERTY_ID = %d)',
                            static::getTableNameMulti(),
                            '%s',
                            $property['ID']
                        ),
                        ['IBLOCK_ELEMENT_ID'],
                        ['fetch_data_modification' => [static::class, 'getMultipleFieldValueModifier']]
                    );

                    if ($property['USER_TYPE'] === 'EList') {
                        $map[$property['CODE'].'_ELEMENT_NAME'] = new ExpressionField(
                            $property['CODE'].'_ELEMENT_NAME',
                            sprintf('(select group_concat(e.NAME SEPARATOR "\0") as VALUE from %s as m join b_iblock_element as e on m.VALUE = e.ID where m.IBLOCK_ELEMENT_ID = %s and m.IBLOCK_PROPERTY_ID = %d)',
                                static::getTableNameMulti(),
                                '%s',
                                $property['ID']
                            ),
                            ['IBLOCK_ELEMENT_ID'],
                            ['fetch_data_modification' => [static::class, 'getMultipleFieldValueModifier']]
                        );
                    }

                    $map[$property['CODE'].'|SINGLE'] = new ReferenceField(
                        $property['CODE'].'|SINGLE',
                        $multipleValuesTableClass,
                        [
                            '=this.IBLOCK_ELEMENT_ID' => 'ref.IBLOCK_ELEMENT_ID',
                            '=ref.IBLOCK_PROPERTY_ID' => new SqlExpression('?i', $property['ID'])
                        ]
                    );

                    continue;
                }

                if ($property['PROPERTY_TYPE'] == PropertyTable::TYPE_NUMBER) {
                    $map[$property['CODE']] = new IntegerField("PROPERTY_{$property['ID']}");
                } elseif ($property['USER_TYPE'] === 'Date') {
                    $map[$property['CODE']] = new DatetimeField("PROPERTY_{$property['ID']}");
                } else {
                    $map[$property['CODE']] = new StringField("PROPERTY_{$property['ID']}");
                }

                if ($property['PROPERTY_TYPE'] === 'E' && ($property['USER_TYPE'] === 'EList' || is_null($property['USER_TYPE']))) {
                    $map[$property['CODE'].'_ELEMENT'] = new ReferenceField(
                        $property['CODE'].'_ELEMENT',
                        ElementTable::class,
                        ["=this.{$property['CODE']}" => 'ref.ID']
                    );
                }
            }

            if (empty($map)) {
                $cache->abortDataCache();
            } else {
                $cache->endDataCache($map);
            }
        }

        return $map;
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

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public static function getProperties(): array
    {
        if (isset(static::$properties[static::IBLOCK_ID])) {
            return static::$properties[static::IBLOCK_ID];
        }

        $dbResult = PropertyTable::query()
            ->setSelect(['ID', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'NAME', 'USER_TYPE'])
            ->where('IBLOCK_ID', static::IBLOCK_ID)
            ->exec();
        while ($row = $dbResult->fetch()) {
            static::$properties[static::IBLOCK_ID][$row['CODE']] = $row;
        }

        return static::$properties[static::IBLOCK_ID] ?? [];
    }

    /**
     * @param  string  $code
     *
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getPropertyId(string $code): int
    {
        return (int) static::getProperties()[$code]['ID'];
    }

    /**
     * @return string
     */
    private static function getMultipleValuesTableClass(): string
    {
        $arClassname = explode('\\', static::class);
        $className = end($arClassname);
        $namespace = str_replace('\\'.$className, '', static::class);
        $className = str_replace('Table', 'MultipleTable', $className);

        return $namespace.'\\'.$className;
    }

    /**
     * @return void
     */
    private static function initMultipleValuesTableClass(): void
    {
        $arClassname = explode('\\', static::class);
        $className = end($arClassname);
        $namespace = str_replace('\\'.$className, '', static::class);
        $className = str_replace('Table', 'MultipleTable', $className);

        if (class_exists($namespace.'\\'.$className)) {
            return;
        }
    }
}
