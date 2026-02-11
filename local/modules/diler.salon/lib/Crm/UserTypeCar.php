<?php

namespace Diler\Salon\Crm;

use CUserTypeManager;
use Diler\Salon\Orm\CarTable as OrmCar;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\Type;
use Bitrix\Bitrix24;

class UserTypeCar extends \Bitrix\Main\UserField\TypeBase
{
    const USER_TYPE_ID = 'usertypecar';
    
    /**
     * Метод возвращает массив описания собственного типа свойств
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => static::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => 'Выбор автомобиля',
            "BASE_TYPE" => \CUserTypeManager::BASE_TYPE_INT,
            'EDIT_CALLBACK' => array(
                __CLASS__,
                'GetEditFormHTML'
            ),
            'VIEW_CALLBACK' => array(
                __CLASS__,
                'getPublicView'
            )
        );
    }

    /**
     * Обязательный метод для определения типа поля таблицы в БД при создании свойства
     * @param $arUserField
     * @return string
     */
    public static function GetDBColumnType($arUserField)
    {
        global $DB;
        switch (strtolower($DB->type)) {
            case "mysql":
                return "int(18)";
            case "oracle":
                return "number(18)";
            case "mssql":
                return "int";
        }
        return "int";
    }

    /**
     * Получаем список значений
     * @param $filter
     * @return array|bool|\CDBResult
     */
    public static function GetCarList($arUserField, $filter = [])
    {
        $rsEnum = [];
        $res = OrmCar::getList([
            'order' => ['ID' => 'ASC'],
            'select' => ['*'],
            'filter' => $filter,
        ]);
        while ($row = $res->fetch()) {
            $rsEnum[$row['ID']]['ID'] = $row['ID'];
            $rsEnum[$row['ID']]['VALUE'] = $row['MARKA'] . ' ' . $row['MODEL'] . ' ' . $row['NUMBER'] . ' ' . $row['YEAR'] . ' ' . $row['COLOR'];
        }

        return $rsEnum;
    }

    /**
     * Получить HTML формы для редактирования свойства
     * @param $arUserField
     * @param $arHtmlControl
     * @return string
     */
    public static function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        if (($arUserField['ENTITY_VALUE_ID'] < 1) && strlen($arUserField['SETTINGS']['DEFAULT_VALUE']) > 0)
            $arHtmlControl['VALUE'] = intval($arUserField['SETTINGS']['DEFAULT_VALUE']);
        $result = '';
        $rsEnum = call_user_func_array(
            array($arUserField['USER_TYPE']['CLASS_NAME'], 'GetCarList'),
            array(
                $arUserField,
            )
        );

        if (!$rsEnum)
            return '';

        $result2 = '';
        $bWasSelect = false;
        foreach ($rsEnum as $arEnum) {
            $bSelected = $arUserField['VALUE'] == $arEnum['ID'];
            $bWasSelect = $bWasSelect || $bSelected;
            $result2 .= '<option value="' . $arEnum['ID'] . '"' . ($bSelected ? ' selected' : '') . '>' . $arEnum['VALUE'] . '</option>';
        }

        $name = static::getFieldName($arUserField, $arHtmlControl);
        $result = '<select name="' . $name . '">';
        if ($arUserField["MANDATORY"] != "Y") {
            $result .= '<option value=""' . (!$bWasSelect ? ' selected' : '') . '>' . htmlspecialcharsbx(self::getEmptyCaption($arUserField)) . '</option>';
        }
        $result .= $result2;
        $result .= '</select>';

        return $result;
    }

    public static function getPublicView ($arUserField, $arAdditionalParameters = array())
    {
        $car = call_user_func_array(
            [$arUserField['USER_TYPE']['CLASS_NAME'], 'GetCarList'],
            [$arUserField, ['ID' => $arUserField['VALUE']]]
        )[$arUserField['VALUE']]['VALUE'];

        $name = static::getFieldName($arUserField, $arAdditionalParameters);
        return '<input type="hidden" name="' . $name . '" value="' . $arUserField['VALUE'] . '"/>' . $car;
    }

    /**
     * Получаем текст для пустого значения свойства
     * @param $arUserField
     * @return mixed|string|string[]
     */
    public static function getEmptyCaption($arUserField)
    {
        return $arUserField["SETTINGS"]["CAPTION_NO_VALUE"] <> ''
            ? $arUserField["SETTINGS"]["CAPTION_NO_VALUE"]
            : 'Автомобиль не выбран';
    }
}
