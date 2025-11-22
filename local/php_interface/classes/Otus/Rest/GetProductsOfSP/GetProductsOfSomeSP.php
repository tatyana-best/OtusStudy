<?php

namespace Otus\Rest\GetProductsOfSP;

class GetProductsOfSomeSP
{
    public static function GetProducts($query, $nav, \CRestServer $server): array
    {
        try {
            if ($query['error'])
            {
                throw new \Bitrix\Rest\RestException( 'Message', 402, \CRestServer::STATUS_PAYMENT_REQUIRED );
            }

            if (!isset($query['typeId']))
            {
                throw new \Bitrix\Rest\RestException( 'TypeId of smart process cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            if (!isset($query['spId']))
            {
                throw new \Bitrix\Rest\RestException( 'ID of element cannot be empty', 400, \CRestServer::STATUS_WRONG_REQUEST );
            }

            \Bitrix\Main\Loader::includeModule('crm');
            \Bitrix\Main\Loader::includeModule('iblock');

            global $DB;

            $smartTypeId = $query['typeId'];
            $spId = $query['spId'];
            $propId = $query['propId'];

            $products = \Bitrix\Crm\ProductRowTable::getList([
                'select' => ['ID', 'XML_ID'],
                'filter' => ['OWNER_ID' => $spId, 'OWNER_TYPE' => $smartTypeId]
            ])->fetchAll();

            while ($row = $products->fetch()) {
                $productXML = $row['XML_ID'];
            }

            $results = $DB->Query("SELECT ID FROM `b_iblock_element` WHERE XML_ID=" . $productXML);
            while($row = $results->Fetch()){
                $productId = $row['ID'];
            }

            $element = \Bitrix\Iblock\Elements\ElementMainCatalogTable::getByPrimary($productId, array(
                'select' => array('ID', $propId)
            ))->fetchObject();

            $ardate = $element->get($propId)->getValue();        
        } catch (Exception $e){
            return [
                'error' => $e->getCode(),
                'error_description' => $e->getMessage()
            ];
        }
        
        return $arDate;
    }
}
