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
            $oldValue = self::getOldAmount($arFields['ID']);
            $newValue = $arFields['OPPORTUNITY'];

            $error1 = false;
            if ($oldValue != $newValue) {
                \CIBlockElement::SetPropertyValuesEx($elementIdToUpdate, $iblockId, [$propertyCode => $newValue]);
            } else {
                $error1 = true;
            }

            $propertyCode = "RESPONSIBLE";
            $oldValue = self::getOldResponsible($arFields['ID']);
            $newValue = $arFields['ASSIGNED_BY_ID'];

            $error2 = false;
            if ($oldValue != $newValue) {
                \CIBlockElement::SetPropertyValuesEx($elementIdToUpdate, $iblockId, [$propertyCode => $newValue]);
            } else {
                $error2 = true;
            }
        } else {
            AddMessage2Log("Такой сделки не существует", "iblock");
            return false;
        }

        return !($error1 && $error2);
    }

    public static function getOldAmount($dealId)
    {
        $arDeals = DealTable::getList([
            'filter' => ['ID' => $dealId],
            'select' => ['OPPORTUNITY'],
        ])->fetchAll();

        $sum = 0;
        foreach($arDeals as $deal){
            $sum = $deal['OPPORTUNITY'];
        }

        return $sum;
    }

    public static function getOldResponsible($dealId)
    {
        $arDeals = DealTable::getList([
            'filter' => ['ID' => $dealId],
            'select' => ['ASSIGNED_BY_ID'],
        ])->fetchAll();

        $resopsible = 0;
        foreach($arDeals as $deal){
            $resopsible = $deal['ASSIGNED_BY_ID'];
        }

        return $resopsible;
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
