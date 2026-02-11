<?php

namespace Diler\Salon\Crm;

use CUserTypeManager;
use Diler\Salon\Orm\CarTable as OrmCar;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\Type;
use Bitrix\Bitrix24;

\Bitrix\Main\Loader::includeModule('crm');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');

class UserTypeSpares extends \Bitrix\Main\UserField\TypeBase
{
    const USER_TYPE_ID = 'usertypespares';
    const IBLOCK_ID = 15;

    /**
     * Метод возвращает массив описания собственного типа свойств
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => static::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => 'Запчасти и их количество',
            "BASE_TYPE" => \CUserTypeManager::BASE_TYPE_STRING,
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
            case 'mysql':
                return 'VARCHAR(400)';
                break;
        }

        return "VARCHAR(400)";
    }

    /**
     * Получаем список значений
     * @param $filter
     * @return array|bool|\CDBResult
     */
    public static function GetSparesList($arUserField, $filter = [])
    {
        $dbItems = \Bitrix\Iblock\ElementTable::getList(array(
            'select' => array('ID', 'NAME',),
            'filter' => array('IBLOCK_ID' => self::IBLOCK_ID)
        ));
        $items = [];
        while ($arItem = $dbItems->fetch()){
            $name = $arItem['NAME'];
            $dbPrice = \CPrice::GetBasePrice($arItem['ID']);
            if ($dbPrice) {
                $price = $dbPrice["PRICE"] . " " . $dbPrice["CURRENCY"];
            }
            $items[$arItem['ID']]['ID'] = $arItem['ID'];
            $items[$arItem['ID']]['VALUE'] = $name . ' (' . $price . ')';
        }

        return $items;
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
            $arHtmlControl['VALUE'] = $arUserField['SETTINGS']['DEFAULT_VALUE'];

        $rsEnum = call_user_func_array(
            array($arUserField['USER_TYPE']['CLASS_NAME'], 'GetSparesList'),
            array(
                $arUserField,
            )
        );

        if (!$rsEnum)
            return '';

        if ($arUserField['VALUE']) {
            $arGetSaved = json_decode($arUserField['VALUE'], true);
            if (!$arGetSaved) {
                $arGetSaved = [['spare_id' => 0, 'count' => 0]];
            }
        } else {
            $arGetSaved = [['spare_id' => 0, 'count' => 0]];
        }

        $result = '';
        $result .= '<div id="fields-container"><div class="flex-box"><div class="spare-title">Запчасть</div><div class="count-title">Количество</div><div>Удалить</div></div>';
        foreach ($arGetSaved as $arGetSavedItem) {
            $result2 = '';
            $bWasSelect = false;
            foreach ($rsEnum as $arEnum) {
                $bSelected = $arGetSavedItem['spare_id'] == $arEnum['ID'];
                $bWasSelect = $bWasSelect || $bSelected;
                $result2 .= '<option value="' . $arEnum['ID'] . '"' . ($bSelected ? ' selected' : '') . '>' . $arEnum['VALUE'] . '</option>';
            }

            $name = static::getFieldName($arUserField, $arHtmlControl);
            $result .= '<div class="flex-box"><select class="select-spare" name="spare[]" onchange="collectFieldsData()">';
            if ($arUserField["MANDATORY"] != "Y") {
                $result .= '<option value=""' . (!$bWasSelect ? ' selected' : '') . '>' . htmlspecialcharsbx(self::getEmptyCaption($arUserField)) . '</option>';
            }
            $result .= $result2;
            $result .= '</select><input onchange="collectFieldsData()" class="input-count" type="text" value="' . $arGetSavedItem["count"] . '" name="count[]">
            <button type="button" onclick="removeField(this)" class="btn btn-danger">✕</button>
            </div>';
        }
        $result .= '</div><button class="add" onclick="addField()">Добавить запчасть</button>';
        $result .= '<div class="field-template flex-box"><select onchange="collectFieldsData()" class="select-spare" name="spare[]">';
        $bWasSelect = false;
        $result .= $result2;
        if ($arUserField["MANDATORY"] != "Y") {
            $result .= '<option value=""' . (!$bWasSelect ? ' selected' : '') . '>' . htmlspecialcharsbx(self::getEmptyCaption($arUserField)) . '</option>';
        }
        $result .= '</select><input onchange="collectFieldsData()" class="input-count" type="text" value="" name="count[]">
            <button type="button" onclick="removeField(this)" class="btn btn-danger">✕</button>
        </div>';
        $result .= "<input type='hidden' id='fields-data' name='" . $name . "' value='" . htmlspecialcharsbx($arUserField['VALUE']) . "'>";
        $result .= '<style>
               .flex-box {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin: 15px 0;
               }               
               .select-spare {
                    height: 25px;
                    border-radius: 5px;
               }
               .input-count {
                    height: 20px;
                    border-radius: 5px;
                    width: 40px;
               }
               .add {
                    margin: 15px 0 20px 0;
               }
               .field-template {
                    display: none;
               }
               .count-title {
                    align-self: end;
                    width: 20%;
               }
               .spare-title {
                    width: 60%;
               }
        </style>';
        $result .= "<script>
            collectFieldsData();
            var fieldCounter = +(document.querySelectorAll('.flex-box').length - 2);
            
            function addField() {                
                fieldCounter++;
                const fieldsContainer = document.getElementById('fields-container');
                const fieldTemplate = document.querySelector('.field-template');
                const fieldClone = fieldTemplate.cloneNode(true);                                       
                fieldsContainer.appendChild(fieldClone);
                fieldClone.classList.remove('field-template');
                collectFieldsData();
            }
            function removeField(button) {              
                if (fieldCounter > 1) {
                    fieldCounter--;
                    const fieldRow = button.closest('.flex-box');                                      
                    fieldRow.remove();                   
                }
                collectFieldsData();
            }
            function collectFieldsData() {
                const result = [];
                const containers = document.querySelectorAll('.flex-box');                
                containers.forEach((container, index) => {
                    if (container.querySelector('.select-spare') && 
                        +(container.querySelector('.input-count').value) != 0 &&
                        +(container.querySelector('.select-spare').value) != 0) {
                        const select = container.querySelector('.select-spare');
                        const selectValue = +select.value;                    
                        const input = container.querySelector('.input-count');
                        const inputValue = +input.value;                        
                        result.push({
                            spare_id: selectValue,
                            count: inputValue,
                        }); 
                    }                    
                });
                
                updateHiddenField(result);
            }
            
            function updateHiddenField(fieldsData) {
                const hiddenField = document.getElementById('fields-data');
                hiddenField.value = '';
                hiddenField.value = JSON.stringify(fieldsData);
            }

        </script>";

        return $result;
    }

    public static function getPublicView ($arUserField, $arAdditionalParameters = array())
    {
		$spare = '';
        if ($arUserField['VALUE']) {
            $arSaved = json_decode($arUserField['VALUE'], true);
            if ($arSaved) {
                foreach ($arSaved as $key => $value) {
					$spare .= self::GetSparesList($arUserField)[(int)$value['spare_id']]['VALUE'] . ' - ' . $value['count'] . ' шт' . '<br>';
                }
            } else {
                $spare = 'Не заполнено';
            }
        } else {
            $spare = 'Не выбрано';
        }

        $name = static::getFieldName($arUserField, $arAdditionalParameters);
        
        return "<input type='hidden' id='fields-data' name='" . $name . "' value='" . htmlspecialcharsbx($arUserField['VALUE']) . "'/>" . $spare;
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
            : 'Запчасть не выбрана';
    }
}
