<?php

namespace Otus\CRM\Deals;

use \Bitrix\Main\Context;
use Bitrix\Main\Loader;
use \Bitrix\Iblock\Elements\ElementRequestsTable;
use \Bitrix\Iblock\Iblock;
use Bitrix\Crm\DealTable;

\Bitrix\Main\Loader::IncludeModule('crm');
\Bitrix\Main\Loader::includeModule('iblock');

class UpdateRequestsIblockAfterDealUpdate
{
    const REQUEST_IBLOCK_ID = 30;
    
    public static function updateRequest(&$arFields)
    {
        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/local/logs/log.txt");
        
        $elementIdToUpdate = self::findIblockElementIdByDeal($arFields['ID']);
        $iblockId = static::REQUEST_IBLOCK_ID;

        if ($elementIdToUpdate) {
            $propertyCode = "AMOUNT";
            $oldValue = self::getOldValue($arFields['ID'], 'OPPORTUNITY');
            $newValue = $arFields['OPPORTUNITY'];

            if ($oldValue != $newValue) {
                \CIBlockElement::SetPropertyValuesEx($elementIdToUpdate, $iblockId, [$propertyCode => $newValue]);
            }

            $propertyCode = "RESPONSIBLE";
            $oldValue = self::getOldValue($arFields['ID'], 'ASSIGNED_BY_ID');
            $newValue = $arFields['ASSIGNED_BY_ID'];

            if ($oldValue != $newValue) {
                \CIBlockElement::SetPropertyValuesEx($elementIdToUpdate, $iblockId, [$propertyCode => $newValue]);
            }
        } else {
            AddMessage2Log("Такой сделки не существует", "iblock");
            return false;
        }

        return true;
    }

    public static function getOldValue($dealId, $field)
    {
        $arDeals = DealTable::getList([
            'filter' => ['ID' => $dealId],
            'select' => [$field],
        ])->fetchAll();

        $sum = 0;
        foreach($arDeals as $deal){
            $sum = $deal[$field];
        }

        return $sum;
    }

    public static function findIblockElementIdByDeal($dealId)
    {
        $dataClass = Iblock::wakeUp(static::REQUEST_IBLOCK_ID)->getEntityDataClass();

        $element = $dataClass::getList([
            'select' => ['ID'],
            'filter' => ['DEALC.VALUE' => $dealId]
        ])->fetchAll();

        $req = 0;
        foreach ($element as $request){
            $req = $request['ID'];
        }

        return $req;
    }
}
