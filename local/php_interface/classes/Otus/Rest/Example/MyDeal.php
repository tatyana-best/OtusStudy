<?php

namespace Otus\Rest\Example;

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;
use Bitrix\Rest\RestException;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Event;
use \Bitrix\Main\EventResult;

class MyDeal
{
    public static function addCustomRestMethods(): array
    {
        return [
            'crm' => [
                'crm.deal.myDeal' => [
                    'callback' => ['Otus\\Rest\\Example\\MyDeal', 'GetMyDeal'],
                    'options' => [],
                ],
            ],
        ];
    }

    public static function GetMyDeal($query, $nav, \CRestServer $server): array
    {
        try {
            global $USER;

            if ($query['error']) {
                throw new RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            if (!Loader::includeModule('crm')) {
                return [];
            }

            $dealFilterFields = [];
            if (isset($query['dealId'])) {
                $dealFilterFields['ID'] = (int)$query['dealId'];
            }

            $arResult = self::getDealList($dealFilterFields);

            if (!empty($arResult)) {
                if (Loader::includeModule('im') && isset($query['dealId'])) {
                    $arMessageFields = array(
                        "FROM_USER_ID" => 1,
                        "TO_USER_ID" => $USER->getId(),
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_TAG" => "",
                        "NOTIFY_MESSAGE" => "Id сделки: " . $arResult[0]['ID'] . ", Название сделки: " . $arResult[0]['TITLE'],
                    );

                    \CIMNotify::Add($arMessageFields);
                }
            } else {
                throw new RestException('Deal(s) list is empty', 400, \CRestServer::STATUS_WRONG_REQUEST);
            }
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }

    public static function getDealList($dealFilterFields)
    {
        $dealOrder = [
            'TITLE' => 'ASC',
        ];

        $dealSelectFields = [
            'ID',
            'TITLE',
            'ASSIGNED_BY_ID',
        ];

        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

        $dealItems = $dealFactory->getItems([
            'filter' => $dealFilterFields,
            'order' => $dealOrder,
            'limit' => 50,
            'select' => $dealSelectFields,
        ]);


        $arResult = [];
        $i = 0;
        foreach ($dealItems as $dealItem) {
            foreach ($dealSelectFields as $field) {
                $arResult[$i][$field] = $dealItem->getData()[$field];
            }
            $arResult[$i]['ASSIGNED_BY_ID'] = self::getUserById($dealItem->getData()['ASSIGNED_BY_ID']);
            $i ++;
        }

        return $arResult;
    }

    public static function getUserById($userId)
    {
        $user = UserTable::getByPrimary($userId, [
            'select' => ['ID', 'NAME', 'LAST_NAME'],
        ])->fetchObject();

        return $user['NAME'] . ' ' . $user['LAST_NAME'];
    }

    public static function updateTabs(Event $event)
    {
        $tabs = $event->getParameter('tabs');
        $entityID = $event->getParameter('entityID');
        $entityTypeID = $event->getParameter('entityTypeID');
        if ($entityTypeID == \CCrmOwnerType::Deal) {
            $tabs[] = [
                'id' => 'newMyTab',
                'name' => 'Моя сделка',
                'html' => "
                <style>
                    .wrap {
                        width: 95%;
                        height: 100%;
                        background: white;
                        padding: 20px;
                    }
                    .info {
                        width: 300px;
                        padding: 15px;
                        margin: 0px 15px;
                        background-color: #f6f7f8;
                        border: 1px solid #ddd;                                      
                        border-radius: 5px;
                    }
                    .send-deal {
                        margin: 13px;
                    }
                </style>
                <div class='wrap'>
                <div class='info'></div>
                <button class='ui-btn ui-btn-primary ui-btn-md send-deal'>Отправить уведомление</button>
                </div>
                <script>
                    $('.send-deal').click(function() {                        
                        getDealData(" . $entityID . ")
                            .then(dealData => {                                
                                $('.info').html('Сделка:<br>ID = ' + dealData[0].ID + '<br> Название = ' + dealData[0].TITLE + '<br>Ответственный: ' + dealData[0].ASSIGNED_BY_ID);
                                showConfirm();
                            });
                    });                            

                    async function getDealData(dealId) {
                        try {
                            const result = await BX.rest.callMethod('crm.deal.myDeal', {
                                dealId: dealId
                            });
                    
                            if (result.error()) {
                                console.error('Ошибка:', result.error());
                                return null;
                            }
                    
                            return result.data();
                        } catch (error) {
                            console.error('Ошибка вызова метода:', error);
                            return null;
                        }
                    }
                    function showConfirm()
                    {
                        class CircleBalloon extends BX.UI.Notification.Balloon
                        {
                            render()
                            {
                                var content = this.getContent();
                                return BX.create('div', {
                                    props: {
                                        className: 'circle-balloon'
                                    },
                                    children: [
                                        BX.create('div', {
                                            props: {
                                                className: 'circle-balloon-content'
                                            },
                                            html: BX.type.isDomNode(content) ? null : content,
                                            children: BX.type.isDomNode(content) ? [content] : []
                                        })
                                    ]
                                })
                            }
                        }
                    
                        BX.UI.Notification.Center.notify({
                            content: '<div class=\"dialog-block\">Уведомление успешно отправлено</div>',
                            type: 'CircleBalloon',
                        });
                    }
                </script>"
            ];

            $reflection = new \ReflectionClass($event);
            $property = $reflection->getProperty('parameters');
            $property->setAccessible(true);

            $eventParameters = $property->getValue($event);

            $eventParameters['tabs'] = $tabs;
            $property->setValue($event, $eventParameters);

            return new EventResult(EventResult::SUCCESS, [
                'tabs' => $tabs,
            ]);
        }
        return false;
    }
}
