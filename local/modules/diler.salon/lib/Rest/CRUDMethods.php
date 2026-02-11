<?php

namespace Diler\Salon\Rest;

use Bitrix\Main\Loader;
use Bitrix\Rest\RestException;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Event;
use \Bitrix\Main\EventResult;
use Diler\Salon\Orm\CarTable;

class CRUDMethods
{
    /**
     * register rest methods
     */
    public static function CRUDForCar(): array
    {
        return [
            'orm' => [
                'orm.car.add' => [
                    'callback' => ['Diler\\Salon\\Rest\\CRUDMethods', 'AddCar'],
                    'options' => [],
                ],
                'orm.car.update' => [
                    'callback' => ['Diler\\Salon\\Rest\\CRUDMethods', 'UpdateCar'],
                    'options' => [],
                ],
                'orm.car.delete' => [
                    'callback' => ['Diler\\Salon\\Rest\\CRUDMethods', 'DeleteCar'],
                    'options' => [],
                ],
                'orm.car.get' => [
                    'callback' => ['Diler\\Salon\\Rest\\CRUDMethods', 'GetCar'],
                    'options' => [],
                ],
            ],
        ];
    }

    /**
     * params = ['MARKA', 'MODEL', 'NUMBER', 'YEAR', 'COLOR', 'KM', 'CONTACT_ID']
     */
    public static function AddCar($query, $nav, \CRestServer $server): array
    {
        try {
            global $USER;

            if ($query['error']) {
                throw new RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            if (!isset($query['MARKA']))
            {
                throw new \Bitrix\Rest\RestException( 'MARKA cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['MODEL']))
            {
                throw new \Bitrix\Rest\RestException( 'MODEL cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['NUMBER']))
            {
                throw new \Bitrix\Rest\RestException( 'NUMBER cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['YEAR']))
            {
                throw new \Bitrix\Rest\RestException( 'YEAR cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['COLOR']))
            {
                throw new \Bitrix\Rest\RestException( 'COLOR cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['KM']))
            {
                throw new \Bitrix\Rest\RestException( 'KM cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['CONTACT_ID']))
            {
                throw new \Bitrix\Rest\RestException( 'CONTACT_ID cannot be empty. Enter, please, one of the IDS: 1)Михайлов В. 2)Соколов Б. 3)Титов Г. 4)Тихонов С. 5)Филатов О. 6)Шашкова Е. 7)Шарова Е 8)Иванов С.', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            $arFields = $query;

            $res = CarTable::add($arFields);

            define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/logs/log.txt");
            AddMessage2Log("check1: <pre>".print_r($res->getId(), true)."</pre>", "bizproc");

            $arResult = ['id' => $res->getId(), 'message' => 'Автомобиль добавлен'];
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }

    /**
     * params = ['ID', 'MARKA', 'MODEL', 'NUMBER', 'YEAR', 'COLOR', 'KM', 'CONTACT_ID']
     */
    public static function UpdateCar($query, $nav, \CRestServer $server): array
    {
        try {
            global $USER;

            if ($query['error']) {
                throw new RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            if (!isset($query['ID']))
            {
                throw new \Bitrix\Rest\RestException( 'ID cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            $res = self::GetCarByFilter(['ID' => $query['ID']]);

            if (empty($res)) {
                $arResult['message'] = 'Таких автомобилей нет';
            } else {
                $arFields = [];
                foreach ($query as $field => $value) {
                    if ($field != 'ID') {
                        $arFields[$field] = $query[$field];
                    }
                }

                CarTable::update($query['ID'], $arFields);

                $arResult = ['id' => $query['ID'], 'message' => 'Данные автомобиля изменены'];
            }
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }

    /**
     * params = ['ID']
     */
    public static function DeleteCar($query, $nav, \CRestServer $server): array
    {
        try {
            global $USER;

            if ($query['error']) {
                throw new RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            if (!isset($query['ID']))
            {
                throw new \Bitrix\Rest\RestException( 'ID cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            $res = self::GetCarByFilter(['ID' => $query['ID']]);

            if (empty($res)) {
                $arResult['message'] = 'Таких автомобилей нет';
            } else {
                CarTable::delete($query['ID']);

                $arResult = ['id' => $query['ID'], 'message' => 'Автомобиль удален'];
            }
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }

    /**
     * params = ['ID'] can be empty
     */
    public static function GetCar($query, $nav, \CRestServer $server): array
    {
        try {
            global $USER;

            if ($query['error']) {
                throw new RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            $arFilter = [];
            if (isset($query['ID'])) {
                $arFilter = ['ID' => $query['ID']];
            }

            $res = self::GetCarByFilter($arFilter);

            $arResult = [];
            if (!$res) {
                $arResult['message'] = 'Таких автомобилей нет';
            } else {
                $arResult = $res;
            }
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }

    public static function GetCarByFilter(array $arFilter = []): array
    {
        if ($arFilter) {
            $ar = CarTable::query()
                ->setSelect(['ID', 'MARKA', 'MODEL', 'NUMBER', 'YEAR', 'COLOR', 'KM', 'CONTACT_ID'])
                ->setFilter($arFilter)
                ->fetchAll();
        } else {
            $ar = CarTable::query()
                ->setSelect(['ID', 'MARKA', 'MODEL', 'NUMBER', 'YEAR', 'COLOR', 'KM', 'CONTACT_ID'])
                ->fetchAll();
        }

        $arResult = [];
        foreach ($ar as $key => $value) {
            $arResult[$key]['ID'] = $value['ID'];
            $arResult[$key]['MARKA'] = $value['MARKA'];
            $arResult[$key]['MODEL'] = $value['MODEL'];
            $arResult[$key]['NUMBER'] = $value['NUMBER'];
            $arResult[$key]['YEAR'] = $value['YEAR'];
            $arResult[$key]['COLOR'] = $value['COLOR'];
            $arResult[$key]['KM'] = $value['KM'];
            $arResult[$key]['CONTACT_ID'] = $value['CONTACT_ID'];
        }

        return $arResult;
    }
}
