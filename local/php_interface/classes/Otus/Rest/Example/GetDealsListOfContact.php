<?php

namespace Otus\Rest\Example;

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

class GetDealsListOfContact
{
    public static function GetDealsOfContact($query, $nav, \CRestServer $server): array
    {
        try {
            if ($query['error']) {
                throw new \Bitrix\Rest\RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            if (!isset($query['contactId'])) {
                throw new \Bitrix\Rest\RestException('Contact ID can not be empty', 400, \CRestServer::STATUS_WRONG_REQUEST);
            }

            if (!Loader::includeModule('crm')) {
                return [];
            }

            $dealOrder = [
                'TITLE' => 'ASC',
            ];

            $dealFilterFields = [
                'CONTACT_ID' => (int)$query['contactId'],
            ];

            $dealSelectFields = [
                'ID',
                'TITLE',
                'TYPE_ID',
                'STAGE_ID',
                'CURRENCY_ID',
                'OPPORTUNITY',
                'ASSIGNED_BY_ID',
            ];

            $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

            $dealItems = $dealFactory->getItems([
                'filter' => $dealFilterFields,
                'order' => $dealOrder,
                'select' => $dealSelectFields,
            ]);

            $arResult = [];
            foreach ($dealItems as $key => $dealItem) {
                foreach ($dealSelectFields as $field) {
                    $arResult[$key][$field] = $dealItem->getData()[$field];
                }
            }
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }
}
