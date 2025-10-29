<?php

namespace Crm\Tab\Crm;

use Crm\Tab\Orm\BookTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\Elements\ElementQuestionsTable;

Loc::loadMessages(__FILE__);

class Handlers
{
    public static function updateTabs(Event $event): bool //EventResult
    {
		$availableEntityIds = Option::get('crm.tab', 'TAB_ENTITIES_TO_DISPLAY_TAB');
        $availableEntityIds = explode(',', $availableEntityIds);
        $entityTypeId = $event->getParameter('entityTypeID');
        $entityId = $event->getParameter('entityID');
        $tabs = $event->getParameter('tabs');
        /*if (in_array($entityTypeId, $availableEntityIds)) {
            $tabs[] = [
                'id' => 'book_tab_' . $entityTypeId . '_' . $entityId,
                'name' => Loc::getMessage('TAB_TAB_TITLE'),
                'enabled' => true,
                'loader' => [
                    'serviceUrl' => sprintf(
                        '/bitrix/components/OtusMain/book.grid/lazyload.ajax.php?site=%s&%s',
                        \SITE_ID,
                        \bitrix_sessid_get(),
                    ),
                    'componentData' => [
                        'template' => '',
                        'params' => [
                            'ORM' => BookTable::class,
                            'DEAL_ID' => $entityId,
                        ],
                    ],
                ],
            ];
            if (Option::get('crm.tab', 'switch_on') == 'Y') {
                $tabs[] = [
                    'id' => 'questions_tab_' . $entityTypeId . '_' . $entityId,
                    'name' => Loc::getMessage('TAB_QUESTIONS_TITLE'),
                    'enabled' => true,
                    'loader' => [
                        'serviceUrl' => sprintf(
                            '/bitrix/components/OtusMain/book.grid/lazyload.ajax.php?site=%s&%s',
                            \SITE_ID,
                            \bitrix_sessid_get(),
                        ),
                        'componentData' => [
                            'template' => 'questions',
                            'params' => [
                                'ORM' => BookTable::class,
                                'DEAL_ID' => $entityId,
                            ],
                        ],
                    ],
                ];
            }
        }

        return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs,]);*/
        return false;
    }
}
