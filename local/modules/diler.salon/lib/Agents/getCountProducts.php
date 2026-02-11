<?php

namespace Diler\Salon\Agents;

use Diler\Salon\Orm\CarTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\CEventLog;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Crm\Service;
use Bitrix\Main\UserGroupTable;

Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::includeModule('crm');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');

/**
 * добавляем агент
 */
class getCountProducts
{
    const PRODUCT_IBLOCK_ID = 15;
    const BUYING_SPARES_SMART = 4;
    const BUYERS = 20;

    /**
     * @return string
     */
    public static function agentCountProducts()
    {
        \CEventLog::Add(array(
            "SEVERITY" => "SECURITY",
            "AUDIT_TYPE_ID" => "MY_OWN_TYPE",
            "MODULE_ID" => "diler.salon",
            "ITEM_ID" => 123,
            "DESCRIPTION" => "<pre>" . print_r(self::setCountProducts(), true) . "</pre>",
        ));

        return '\\Diler\\Salon\\Agents\\getCountProducts::agentCountProducts()';
    }

    /**
     * @return array
     */
    public static function setCountProducts()
    {
        $dbItems = \Bitrix\Iblock\ElementTable::getList(array(
            'select' => array('ID', 'NAME', 'IBLOCK_ID'),
            'filter' => array('IBLOCK_ID' => static::PRODUCT_IBLOCK_ID)
        ));

        $items = [];
        while ($arItem = $dbItems->fetch()){
            $rsProduct = \CCatalogProduct::GetByID($arItem['ID']);
            if ($rsProduct) {
                $items[$arItem['ID']]['ID'] = $arItem['ID'];
                $items[$arItem['ID']]['COUNT_WAS'] = $rsProduct['QUANTITY'];
            }

            $countNew = self::getCountProductsFromService();

            if ($arItem['ID'] == 175) {
                $countNew = 0;
            }

            if ($countNew == 0) {
                $items[$arItem['ID']]['MESSAGE'] = self::createQueryOfBuyingSpare($arItem['ID'], $arItem['NAME']);
            } else {
                $arFields = array(
                    'QUANTITY' => $countNew,
                );

                $rsProduct = new \CCatalogProduct();
                if ($rsProduct->Update($arItem['ID'], $arFields)) {
                    $items[$arItem['ID']]['COUNT_NOW'] = $countNew;
                } else {
                    $items[$arItem['ID']]['MESSAGE'] = Loc::getMessage('ERROR_UPDATING');
                }
            }
        }

        return $items;
    }

    /**
     * @param $productId
     * @param $productName
     * @return mixed
     */
    public static function createQueryOfBuyingSpare($productId, $productName)
    {
        $container = Service\Container::getInstance();
        $factory = $container->getFactory(1042);
        $initialFields = [
            'TITLE' => Loc::getMessage('BUYING_MESSAGE', ["#PROD_NAME#" => $productName, "#PROD_ID#" => $productId]),
            'UF_IS_AUTO' => true,
            'UF_SPARES' => json_encode([0 => ['spare_id' => $productId, 'count' => 10]]),
            'STAGE_ID' => 'DT1042_7:SUCCESS',
			'ASSIGNED_BY_ID' => 1,
        ];

        $item = $factory->createItem($initialFields);
        $context = new \Bitrix\Crm\Service\Context();
        $context->setUserId(1);
        $operation = $factory->getAddOperation($item, $context);
        $operation->disableAllChecks();
        $operationResult = $operation->launch();
        if ($operationResult->isSuccess())
        {
            $message = Loc::getMessage('CONFIRM_MESSAGE', ["#PROD_NAME#" => $productName, "#PROD_ID#" => $productId]);

            foreach (self::getBuyers() as $buyer) {
                $arMessageFields = array(
                    "FROM_USER_ID" => 1,
                    "TO_USER_ID" => $buyer,
                    "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                    "NOTIFY_TAG" => "",
                    "NOTIFY_MESSAGE" => $message,
                );

                \CIMNotify::Add($arMessageFields);
            }
        }
        else
        {
			$errors = $operationResult->getErrorMessages();

            $message = Loc::getMessage('ERROR_MESSAGE', ["#ERROR_SYSTEM#" => implode(", ", $errors)]);
            \CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "MY_OWN_TYPE",
                "MODULE_ID" => "diler.salon",
                "ITEM_ID" => 123,
                "DESCRIPTION" => $message,
            ));
        }

        return $message;
    }

    /**
     * @return array
     */
    public static function getBuyers()
    {
        $groupId = static::BUYERS;
        $rsUsers = UserGroupTable::getList([
            'filter' => [
                '=GROUP_ID' => $groupId,
            ],
            'select' => ['USER_ID']
        ]);

        $arBuyers = [];
        while ($user = $rsUsers->fetch()) {
            $arBuyers[] = $user['USER_ID'];
        }

        return $arBuyers;
    }

    /**
     * @return mixed
     */
    public static function getCountProductsFromService()
    {
        $url = 'https://www.random.org/integers/?';

        $headers = [
            'X-Yandex-API-Key: bcf02bb7-f890-42cd-b09a-388498b33a68',
            'Access-Control-Allow-Origin: *',
            'Content-Type: application/json; charset=utf-8',
        ];

        $get = array(
            'num' => 1,
            'min' => 0,
            'max' => 100,
            'col' =>1,
            'base' => 10,
            'format' => 'plain',
            'rnd' => 'new',
        );

        $ch = curl_init($url . http_build_query($get));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $json = curl_exec($ch);
        curl_close($ch);

        $count = json_decode($json, true);

        return $count;
    }
}
