<?php

namespace Otus\Rest\Example;

class NewIblockElement
{
    public static function iBlockElementAdd($query, $nav, \CRestServer $server): array
    {
        try {
            if ($query['error'])
            {
                throw new \Bitrix\Rest\RestException( 'Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED );
            }

            if (!isset($query['iblockId']))
            {
                throw new \Bitrix\Rest\RestException( 'IBLOCK_ID can not be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['fields']))
            {
                throw new \Bitrix\Rest\RestException( 'Iblock fields can not be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            \Bitrix\Main\Loader::includeModule('iblock');
            global $USER;
            $el = new \CIBlockElement;

            $arFields = Array(
                "MODIFIED_BY"    => $USER->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID"      => (int)$query['iblockId'],
                "NAME"           => $query['fields']['NAME'],
                "CODE"           => $query['fields']['CODE'],
                "ACTIVE"         => "Y",
            );

            if($elId = $el->Add($arFields))
                return ['result' => $elId];
            else {
                throw new \Bitrix\Rest\RestException( $el->LAST_ERROR );
            }
        }
        catch (Exception $e){
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }
    }
}