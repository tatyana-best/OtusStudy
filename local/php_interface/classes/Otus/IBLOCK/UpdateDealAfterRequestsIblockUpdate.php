<?php

namespace Otus\IBLOCK;

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

        $newAmount = 0;
        foreach ($arFields['PROPERTY_VALUES'][static::PROPERTY_AMOUNT] as $val) {
            $newAmount = explode('|', $val['VALUE'])[0];
        }

        $oldAmount = self::getOldAmount($arFields['ID']);

        $error1 = false;
        if ($oldAmount != $newAmount && $newAmount > 0) {
            self::updateAmount($dealId, $newAmount);
        } else {
            $error1 = true;
        }

        $newResponsible = 0;
        foreach ($arFields['PROPERTY_VALUES'][static::PROPERTY_RESPONSIBLE] as $val) {
            $newResponsible = $val;
        }

        $oldResponsible = self::getOldResponsible($arFields['ID']);

        $error2 = false;
        if ($oldResponsible != $newResponsible) {
            self::updateResponsible($dealId, $newResponsible);
        } else {
            $error2 = true;
        }

        return !($error1 && $error2);
    }

    public static function getOldAmount($elementId)
    {
        $iblock = Iblock::wakeUp(static::REQUEST_IBLOCK_ID);

        $element = $iblock->getEntityDataClass()::getByPrimary(
            $elementId,
            ['select' => ['AMOUNT']])
            ->fetchObject();

        $sum = $element->get('AMOUNT')->getValue();
        $sum = explode('|', $sum)[0];

        return $sum;
    }

    public static function getOldResponsible($elementId)
    {
        $iblock = Iblock::wakeUp(static::REQUEST_IBLOCK_ID);

        $element = $iblock->getEntityDataClass()::getByPrimary(
            $elementId,
            ['select' => ['RESPONSIBLE']])
            ->fetchObject();

        $resp = $element->get('RESPONSIBLE')->getValue();

        return $resp;
    }

    public static function updateResponsible($dealId, $responsible)
    {
        $factory = Container::getInstance()->getFactory(2);
        $item = $factory->getItem($dealId);
        $item->set('ASSIGNED_BY_ID', $responsible);
        $item->save();
        $operation = $factory->getUpdateOperation($item);
        $operation->launch();
    }

    public static function updateAmount($dealId, $amount)
    {
        $factory = Container::getInstance()->getFactory(2);
        $item = $factory->getItem($dealId);
        $item->setOpportunity($amount);
        $item->save();
        $operation = $factory->getUpdateOperation($item);
        $operation->launch();
    }
}
