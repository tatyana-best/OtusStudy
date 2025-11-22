<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use \Bitrix\Main\Context;
use \Bitrix\Iblock\Elements\ElementBookingTable;

Loader::includeModule("iblock");

$request = Context::getCurrent()->getRequest();

if ($request->isPost()) {
    $postData = $request->getPostList()->toArray();

    $name = $postData['name'] ?? '';
    $proc = $postData['proc'] ?? 0;
    $doc = $postData['doc'] ?? 0;
    $dateTime = $postData['dateTime'] ?? '';
    $iblockId = $postData['iblock'] ?? 0;
    $docIblock = $postData['docIblock'] ?? 0;

    if ($name && $proc && $doc && $dateTime && $iblockId && $docIblock) {
        $dateAddHours = new DateTime($dateTime);
        $dateAddHours->modify('-3 hours');
        $dateAddHours = $dateAddHours->format('Y-m-d H:i:s');
        $dateMinusHours = new DateTime($dateTime);
        $dateMinusHours->modify('-5 hours');
        $dateMinusHours = $dateMinusHours->format('Y-m-d H:i:s');
        $elements = ElementBookingTable::getList([
            'select' => ['NAME', 'DOCTOR', 'BOOKING_TIME', 'ID'],
            'filter' => [
                'IBLOCK_ID' => $iblockId,
                [
                    'LOGIC'=>'AND',
                    '>BOOKING_TIME.VALUE' => $dateMinusHours,
                    '<BOOKING_TIME.VALUE' => $dateAddHours,
                ],
                'DOCTOR.VALUE' => $doc
            ]
        ])->fetchCollection();

        $arResult = [];
        foreach ($elements as $item) {
            $arResult['time_fromIblock'] = $item->getBookingTime()->getValue();
            $arResult['id'] = $item->getId();
            $arResult['doctor'] = $item->getDoctor()->getValue();
        }

        if (!empty($arResult)) {
            $arRes = [
                'error' => 'Время у данного доктора уже зарезервировано',
                'message' => 'Запись не добавлена',
            ];
        } else {
            try {
                $arElementProps = [
                    'DOCTOR' => $doc,
                    'PROCEDURE' => $proc,
                    'BOOKING_TIME' => $dateTime,
                ];

                $arIblockFields = [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => $name,
                    'PROPERTY_VALUES' => $arElementProps
                ];

                $objIblockElement = new \CIBlockElement();
                $newElementId = $objIblockElement->Add($arIblockFields);

                $arRes = [
                    'message' => 'Запись добавлена',
                    'error' => '',
                ];
            } catch (\Exception $e) {
                $arRes = [
                    'message' => 'Запись не добавлена',
                    'error' => $e->getMessage()
                ];
            }
        }

        echo json_encode($arRes);
    }
}
