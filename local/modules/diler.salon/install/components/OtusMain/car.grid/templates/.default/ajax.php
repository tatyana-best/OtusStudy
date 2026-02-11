<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main\Context;
use Diler\Salon\Orm\CarTable;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule("crm");
\Bitrix\Main\Loader::includeModule("diler.salon");

$request = Context::getCurrent()->getRequest();

if ($request->isPost()) {
    $postData = $request->getPostList()->toArray();
    $arRes = [];
    if ($postData['id']) {
        $carId = $postData['id'];
        $arRes = ['ID' => $carId];
        $arRes['MODEL'] = getCarInfo($carId)['MODEL'];
        $arRes['MARKA'] = getCarInfo($carId)['MARKA'];
        $arRes['NUMBER'] = getCarInfo($carId)['NUMBER'];
        $factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
        $items = $factory->getItems([
            "filter" => [
                "CATEGORY_ID" => 2,
                "UF_CAR" => $carId
            ],
            "select" => ["*"],
        ]);
        if ($items) {
            $arRes['MESSAGE'] = Loc::getMessage('CAR_GRID_DEALS_FOUND');
            foreach ($items as $item) {
                $arRes['ITEMS'][$item['ID']]['TITLE'] = $item->getTitle();
                $arRes['ITEMS'][$item['ID']]['DATE_CREATE'] = getDateStart($item->getBegindate());
                $arRes['ITEMS'][$item['ID']]['ASSIGNED_BY_ID'] = getUserFullName($item->get('ASSIGNED_BY_ID'));
                $arRes['ITEMS'][$item['ID']]['OPPORTUNITY'] = $item->get('OPPORTUNITY');
                $arRes['ITEMS'][$item['ID']]['STAGE_ID'] = getStageName($item->getStageId());
                $arRes['ITEMS'][$item['ID']]['CATEGORY'] = $item->getCategoryId();
                $arRes['ITEMS'][$item['ID']]['PRODUCTS'] = getProductsOfDeal($item['ID']);
            }
        } else {
            $arRes['MESSAGE'] = Loc::getMessage('CAR_GRID_DEALS_NOT_FOUND');;
        }
    } else {
        $arRes['MESSAGE'] = Loc::getMessage('CAR_GRID_ID_MISS');
    }

    echo json_encode($arRes);
}

function getDateStart($dateStart)
{
    $dateStart = new DateTime($dateStart);

    return $dateStart->format('d.m.Y');
}

/**
 * @param $userId
 * @return string
 */
function getUserFullName($userId)
{
    global $USER;

    $userSTR = '<a href="/company/personal/user/' . $USER->getId() . '/">' . $USER->GetFullName() . "</a>";

    return $userSTR;
}

function getStageName($stage)
{
    switch ($stage) {
        case 'C2:NEW':
            return 'Приемка';
            break;
        case 'C2:PREPAYMENT_INVOICE':
            return 'Ожидание запчастей';
            break;
        case 'C2:PREPARATION':
            return 'Диагностика';
            break;
        case 'C2:EXECUTING':
            return 'Ремонт';
            break;
        case 'C2:FINAL_INVOICE':
            return 'Проверка';
            break;
        case 'C2:WON':
            return 'Сделка успешна';
            break;
        case 'C2:FAIL':
            return 'Сделка отменена';
            break;
    }
}

/**
 * @param $dealId
 * @return array
 */
function getProductsOfDeal($dealId)
{
    $productRows = \Bitrix\Crm\ProductRowTable::getList([
        'select' => [
            'ID',
            'QUANTITY',
            'PRODUCT_NAME',
            'PRICE',
        ],
        'filter' => [
            '=OWNER_ID' => intval($dealId),
            '=OWNER_TYPE' => 'D'
        ],
    ]);

    $arResult = [];
    while ($product = $productRows->fetch()) {
        $arResult[$product['ID']]['NAME'] = $product['PRODUCT_NAME'];
        $arResult[$product['ID']]['QUANTITY'] = $product['QUANTITY'];
        $arResult[$product['ID']]['PRICE'] = $product['PRICE'];
    }

    return $arResult;
}

/**
 * @param $carId
 * @return array|mixed
 */
function getCarInfo($carId)
{
    $ar = CarTable::query()
        ->setSelect(['ID', 'MARKA', 'MODEL', 'NUMBER', 'CONTACT_ID'])
        ->setFilter(['ID' => $carId])
        ->fetchAll();

    $arResult = [];
    foreach ($ar as $value) {
        $arResult = $value;
    }

    return $arResult;
}
