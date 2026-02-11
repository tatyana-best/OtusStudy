<?php

namespace Diler\Salon\Crm;

use Diler\Salon\Orm\CarTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Crm\DealTable;
use Bitrix\Im\Notify;

Loader::includeModule('im');
Loc::loadMessages(__FILE__);

class Handlers
{
    /**
     * @param Event $event
     * @return EventResult
     */
    public static function updateTabs(Event $event): EventResult
    {
        $availableEntityIds = Option::get('diler.salon', 'DILER_ENTITIES_TO_DISPLAY_TAB');
        $availableEntityIds = explode(',', $availableEntityIds);
        $entityTypeId = $event->getParameter('entityTypeID');
        $entityId = $event->getParameter('entityID');
        $tabs = $event->getParameter('tabs');
        if (in_array($entityTypeId, $availableEntityIds)) {
            $tabs[] = [
                'id' => 'car_tab_' . $entityTypeId . '_' . $entityId,
                'name' => Loc::getMessage('DILER_TAB_TITLE'),
                'enabled' => true,
                'loader' => [
                    'serviceUrl' => sprintf(
                        '/bitrix/components/OtusMain/car.grid/lazyload.ajax.php?site=%s&%s',
                        \SITE_ID,
                        \bitrix_sessid_get(),
                    ),
                    'componentData' => [
                        'template' => '',
                        'params' => [
                            'ORM' => CarTable::class,
                            'DEAL_ID' => $entityId,
                        ],
                    ],
                ],
            ];
        }

        return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs,]);
    }

    /**
     * @param $arFields
     * @return bool
     */
    public static function OnBeforeCrmDealAddHandler( &$arFields )
    {
        $categoryId = $arFields['CATEGORY_ID'];
        if ($categoryId == 2) {
            $carId = $arFields['UF_CAR'];
            if (Loader::includeModule('crm')) {
                $res = DealTable::getList([
                    'order' => ['ID' => 'DESC'],
                    'filter' => ['UF_CAR' => $carId, 'CATEGORY_ID' => $categoryId, '=CLOSED' => 'N'],
                    'select' => ['ID', ]
                ]);
                $arUnclosedDeals = [];
                while ($deal = $res->fetch()) {
                    $arUnclosedDeals[] = $deal['ID'];
                }

                if ($arUnclosedDeals) {
                    $arMessageFields = array(
                        "FROM_USER_ID" => 1,
                        "TO_USER_ID" => $arFields['ASSIGNED_BY_ID'],
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_TAG" => "",
                        "NOTIFY_MESSAGE" => Loc::getMessage('DILER_TAB_WARNING'),
                    );
                    \CIMNotify::Add($arMessageFields);

                    return false;
                }
            }
        }

        return true;
    }
}
