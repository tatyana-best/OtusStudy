<?php

namespace Otus\IBLOCK;

use Bitrix\Crm\DealTable;
use \Bitrix\Main\Context;
use \Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;
use \Bitrix\Iblock\Elements\ElementRequestsTable;
use \Bitrix\Iblock\Iblock;
use Bitrix\Crm\Service\Factory;

\Bitrix\Main\Loader::IncludeModule('crm');
\Bitrix\Main\Loader::includeModule('iblock');

class UpdateDealAfterRequestsIblockUpdate
{
    const REQUEST_IBLOCK_ID = 30;
    const PROPERTY_DEALC = 97;
    const PROPERTY_AMOUNT = 98;
    const PROPERTY_RESPONSIBLE = 99;

    public static function updateDeal(&$arFields)
    {
        foreach ($arFields['PROPERTY_VALUES'][static::PROPERTY_DEALC] as $val) {
            $dealId = $val['VALUE'];
        }

        if ($dealId) {
            $newAmount = 0;
            foreach ($arFields['PROPERTY_VALUES'][static::PROPERTY_AMOUNT] as $val) {
                $newAmount = explode('|', $val['VALUE'])[0];
            }

            $oldAmount = self::getOldValue($arFields['ID'], 'AMOUNT');

            if ($oldAmount != $newAmount || self::getFeildValueFromDeal($dealId, 'OPPORTUNITY') != $newAmount) {
                self::updateFieldDeal($dealId, 'OPPORTUNITY', $newAmount);
            }

            $newResponsible = 0;
            foreach ($arFields['PROPERTY_VALUES'][static::PROPERTY_RESPONSIBLE] as $val) {
                $newResponsible = $val;
            }

            $oldResponsible = self::getOldValue($arFields['ID'], 'RESPONSIBLE');

            if ($oldResponsible != $newResponsible || self::getFeildValueFromDeal($dealId, 'ASSIGNED_BY_ID') != $newResponsible) {
                self::updateFieldDeal($dealId, 'ASSIGNED_BY_ID', $newResponsible);
            }
        }

        return true;
    }

    public static function getOldValue($elementId, $field)
    {
        $iblock = Iblock::wakeUp(static::REQUEST_IBLOCK_ID);

        $element = $iblock->getEntityDataClass()::getByPrimary(
            $elementId,
            ['select' => [$field]])
            ->fetchObject();

        $sum = $element->get($field)->getValue();
        $sum = explode('|', $sum)[0];

        return $sum;
    }

    public static function updateFieldDeal($dealId, $field, $fieldValue)
    {
        $factory = Container::getInstance()->getFactory(2);
        $item = $factory->getItem($dealId);
        $item->set($field, $fieldValue);
        $item->save();
        $operation = $factory->getUpdateOperation($item);
        $operation->launch();
    }

    public static function getFeildValueFromDeal($dealId, $field)
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
}
