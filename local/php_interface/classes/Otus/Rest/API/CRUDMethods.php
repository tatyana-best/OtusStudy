<?php

namespace Otus\Rest\API;

use Bitrix\Main\Loader;
use Bitrix\Rest\RestException;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Event;
use \Bitrix\Main\EventResult;
use Otus\ORM\PatientTable;

class CRUDMethods
{
    /**
     * register rest methods
     */
    public static function addCustomRestMethods(): array
    {
        return [
            'orm' => [
                'orm.patient.add' => [
                    'callback' => ['Otus\\Rest\\API\\CRUDMethods', 'AddPatient'],
                    'options' => [],
                ],
                'orm.patient.update' => [
                    'callback' => ['Otus\\Rest\\API\\CRUDMethods', 'UpdatePatient'],
                    'options' => [],
                ],
                'orm.patient.delete' => [
                    'callback' => ['Otus\\Rest\\API\\CRUDMethods', 'DeletePatient'],
                    'options' => [],
                ],
                'orm.patient.get' => [
                    'callback' => ['Otus\\Rest\\API\\CRUDMethods', 'GetPatient'],
                    'options' => [],
                ],
            ],
        ];
    }

    /**
     * params = ['FULL_NAME', 'BIRTH_DATE', 'FLAT', 'PLOT']
     * example URL = https://b24mybeget.ru/rest/1/sjgpe05aas31vmj9/orm.patient.add?FULL_NAME=Курочкина Елена Павловна&BIRTH_DATE=12.09.2002&FLAT=94&PLOT_ID=90
     * PLOT_ID can be 87, 88, 89, 90
     */
    public static function AddPatient($query, $nav, \CRestServer $server): array
    {
        try {
            global $USER;

            if ($query['error']) {
                throw new RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            if (!isset($query['FULL_NAME']))
            {
                throw new \Bitrix\Rest\RestException( 'FULL_NAME cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['BIRTH_DATE']))
            {
                throw new \Bitrix\Rest\RestException( 'BIRTH_DATE cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['FLAT']))
            {
                throw new \Bitrix\Rest\RestException( 'FLAT cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['PLOT_ID']))
            {
                throw new \Bitrix\Rest\RestException( 'PLOT_ID cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            $arFields = $query;
            $arFields['BIRTH_DATE'] = new \Bitrix\Main\Type\DateTime(
                $query['BIRTH_DATE'],
                "d.m.Y"
            );

            $arResult = [PatientTable::add($arFields)];
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }

    /**
     * params = ['ID', 'FULL_NAME', 'BIRTH_DATE', 'FLAT', 'PLOT']
     * example URL = https://b24mybeget.ru/rest/1/sjgpe05aas31vmj9/orm.patient.update?ID=23&FULL_NAME=Петушкова Елена Павловна&BIRTH_DATE=12.09.2002&FLAT=123&PLOT_ID=87
     * PLOT_ID can be 87, 88, 89, 90
     */
    public static function UpdatePatient($query, $nav, \CRestServer $server): array
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

            if (!isset($query['FULL_NAME']))
            {
                throw new \Bitrix\Rest\RestException( 'FULL_NAME cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['BIRTH_DATE']))
            {
                throw new \Bitrix\Rest\RestException( 'BIRTH_DATE cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['FLAT']))
            {
                throw new \Bitrix\Rest\RestException( 'FLAT cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['PLOT_ID']))
            {
                throw new \Bitrix\Rest\RestException( 'PLOT_ID cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            $arFields = [];
            foreach ($query as $field => $value) {
                if ($field != 'ID') {
                    $arFields[$field] = $query[$field];
                }
            }

            $arFields['BIRTH_DATE'] = new \Bitrix\Main\Type\DateTime(
                $query['BIRTH_DATE'],
                "d.m.Y"
            );

            $arResult = [PatientTable::update($query['ID'], $arFields)];
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
     * example URL = https://b24mybeget.ru/rest/1/sjgpe05aas31vmj9/orm.patient.delete?ID=23
     */
    public static function DeletePatient($query, $nav, \CRestServer $server): array
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

            $arResult = [PatientTable::delete($query['ID'])];
        } catch (Exception $e) {
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }

        return $arResult;
    }

    /**
     * example URL = https://b24mybeget.ru/rest/1/sjgpe05aas31vmj9/orm.patient.get
     */
    public static function GetPatient($query, $nav, \CRestServer $server): array
    {
        try {
            global $USER;

            if ($query['error']) {
                throw new RestException('Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED);
            }

            $ar = PatientTable::query()
                ->setSelect(['ID', 'FULL_NAME', 'BIRTH_DATE', 'FLAT', 'PLOT'])
                ->fetchAll();

            $arResult = [];
            foreach ($ar as $key => $value) {
                $arResult[$key]['ID'] = $value['ID'];
                $arResult[$key]['FULL_NAME'] = $value['FULL_NAME'];
                $arResult[$key]['FLAT'] = $value['FLAT'];
                $phpDate = new \Bitrix\Main\Type\Date($value['BIRTH_DATE']);
                $arResult[$key]['BIRTH_DATE'] = $phpDate->toString();
                $arResult[$key]['PLOT'] = $value['OTUS_ORM_PATIENT_PLOT_NAME'];
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
