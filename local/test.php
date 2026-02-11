<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
// require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
// $APPLICATION->SetTitle('Примеры');
// require_once __DIR__ . "/php_interface/functions.php";
// use Bitrix\Main\Diag\Debug;
// use Bitrix24\SDK\Services\ServiceBuilderFactory;
// use Otus\UserTypes\Booking;
// //Debug::startTimeLabel("foo");
// $ar = [];
// for ($i=0; $i < 10; $i++) { 
//     //sleep(2);
//     $ar[] = $i;
// }
//Debug::endTimeLabel("foo");

//dump($ar);
//sage($ar);
// echo "<pre>".print_r($ar, true)."</pre>";
// pr($ar);

//Debug::writeToFile(Debug::getTimeLabels());
//Debug::dump(Debug::getTimeLabels()); 

/*$data = array(
    'id' => 1,
    'fields' => [
        'TITLE'              => "Новое сделки имямямя ***!",
        'TYPE_ID'            => "GOODS",
        'STAGE_ID'           => "WON",
        'IS_RECCURING'       => "Y",
        'IS_RETURN_CUSTOMER' => "Y",
        'OPPORTUNITY'        => 2300.99,
        'IS_MANUAL_OPPORTUNITY' => "Y",
    ],
);

$ch = curl_init('https://b24mybeget.ru/rest/1/eho1rivr2an7isi9/crm.deal.update.json');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$res = curl_exec($ch);
curl_close($ch);

$res = json_encode($res, JSON_UNESCAPED_UNICODE);
print_r($res);*/

//pr(Booking::getProceduresOfDoctor(16, 36));

//print_r($_REQUEST);

//use Bitrix\Disk\File;
//use Bitrix\Main\Loader;
//use Bitrix\Crm\Service\Container;
//
//use Otus\Rest\GetProductsOfSP\GetProductsOfSomeSP;

//\Bitrix\Main\Loader::includeModule('crm');
//\Bitrix\Main\Loader::includeModule('iblock');
// use Rest\CustomRestMethod;

//echo "<pre>".print_r(GetProductsOfSomeSP::get(), true)."</pre>";

//\Bitrix\Main\Loader::includeModule('disk');

// $result = \Bitrix\Crm\ProductRowTable::getList([
//     'select' => [
//         'ID',
//         'PRODUCT_ID',
//         'PRODUCT_NAME',
//         'PRICE',
//         'QUANTITY',
//         'PROPERTY_93' => 'PROPERTY_93.VALUE'
//     ],
//     'filter' => [
//         '=OWNER_ID' => 1,
//         '=OWNER_TYPE' => 'T40e'
//     ],
//     'runtime' => [
//         new \Bitrix\Main\Entity\ReferenceField(
//             'PRODUCT',
//             Bitrix\Iblock\ElementTable::getEntity(),
//             [
//                 '=this.PRODUCT_ID' => 'ref.ID'
//             ]
//         )
//     ]
// ]);

/*$result = \Bitrix\Crm\ProductRowTable::getList([
    'select' => [
        'ID',
        'PRODUCT_ID',
        'PRODUCT_NAME',
        'PROPERTY_93'
    ],
    'filter' => [
        '=OWNER_ID' => 1,
        '=OWNER_TYPE' => 'T40e'
    ],
    'runtime' => [
        new \Bitrix\Main\Entity\ReferenceField(
            'PRODUCT',
            ElementTable::getEntity(),
            [
                '=this.PRODUCT_ID' => 'ref.ID'
            ],
            ['join_type' => 'LEFT']
        ),
        new \Bitrix\Main\Entity\ReferenceField(
            'PROPERTY_93',
            ElementPropertyTable::getEntity(),
            [
                '=this.PRODUCT_ID' => 'ref.IBLOCK_ELEMENT_ID',
                '=ref.IBLOCK_PROPERTY_ID' => new \Bitrix\Main\DB\SqlExpression('?', 93)
            ],
            ['join_type' => 'LEFT']
        )
    ]
]);*/

//\Bitrix\Main\Loader::includeModule('crm');
//\Bitrix\Main\Loader::includeModule('iblock');

/*$smartTypeId = $query['typeId'];
$spId = $query['spId'];           

$result = \Bitrix\Crm\ProductRowTable::getList([
    'select' => [
        'ID',
        'PRODUCT_ID',
        'PRODUCT_NAME',
        'PROPERTY_93'
    ],
    'filter' => [
        '=OWNER_ID' => 1,
        '=OWNER_TYPE' => 'T40e'
    ],
    'runtime' => [
        new \Bitrix\Main\Entity\ReferenceField(
            'PRODUCT',
            \Bitrix\Iblock\ElementTable::getEntity(),
            [
                '=this.PRODUCT_ID' => 'ref.ID'
            ],
            ['join_type' => 'LEFT']
        ),
        new \Bitrix\Main\Entity\ReferenceField(
            'PROPERTY_93',
            \Bitrix\Iblock\ElementPropertyTable::getEntity(),
            [
                '=this.PRODUCT_ID' => 'ref.IBLOCK_ELEMENT_ID',
                '=ref.IBLOCK_PROPERTY_ID' => new \Bitrix\Main\DB\SqlExpression('?', 93)
            ],
            ['join_type' => 'LEFT']
        )
    ]
]);

$arDate = '';
while ($row = $result->fetch()) {
    $arDate = $row['CRM_PRODUCT_ROW_PROPERTY_93_VALUE'];
} 


echo "<pre>".print_r($arDate, true)."</pre>";*/

/*file_put_contents(getcwd() . '/logs/log.txt', $_REQUEST, FILE_APPEND);

$data = array(
    'taskId'  => $_REQUEST['data']['FIELDS_AFTER']['TASK_ID'],
    'commentId' => $_REQUEST['data']['FIELDS_AFTER']['ID']
);		
file_put_contents(getcwd() . '/logs/log.txt', $data, FILE_APPEND);

$ch = curl_init('https://b24mybeget.ru/rest/1/amk6xfu7682d4h6d/tasks.task.result.list');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$res = curl_exec($ch);
curl_close($ch);

$res = json_decode($res, true)['result'];
file_put_contents(getcwd() . '/logs/log.txt', "<pre>".print_r($res, true) . "</pre>", FILE_APPEND);
$arFiles = [];
$taskResult = false;
foreach ($res as $k => $comment) {
    if ($comment['commentId'] == $data['commentId']) {
        $taskResult = true; 
    }
}

if ($taskResult == true) {
    foreach ($res as $k => $comment) {
        if (!empty($comment['files'])) {
            foreach ($comment['files'] as $key => $file) {
                //$arFiles[$file][$key] = $file;
                $fileOwe = File::loadById($file);
                if ($fileOwe) {
                    $arFiles[$file]["ID"] = $fileOwe->getId();
                    $arFiles[$file]["NAME"] = $fileOwe->getName();
                    $arFiles[$file]["SIZE"] = $fileOwe->getSize();
                    $arFiles[$file]["CREATED_BY"] = $fileOwe->getCreatedBy();
                    $arFiles[$file]["CREATE_TIME"] = $fileOwe->getCreateTime()->toString();
                }
            }
        }
    }
}

file_put_contents(getcwd() . '/logs/log.txt', "<pre>".print_r($arFiles, true) . "</pre>", FILE_APPEND); */


/*$data = array(
    'id'  => 218,
    //'commentId' => $_REQUEST['data']['FIELDS_AFTER']['ID']
);
//file_put_contents(getcwd() . '/logs/log.txt', $data, FILE_APPEND);

$ch = curl_init('https://b24mybeget.ru/rest/1/amk6xfu7682d4h6d/disk.file.get');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE)); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$res = curl_exec($ch);
curl_close($ch);

$res = json_decode($res, true)['result'];
file_put_contents(getcwd() . '/logs/log.txt', "<pre>***".print_r($res, true) . "</pre>", FILE_APPEND);
$arFiles = [];*/
//$taskResult = false;

//if ($taskResult == true) {
    // foreach ($res as $k => $comment) {
    //     if (!empty($comment['files'])) {
    //         foreach ($comment['files'] as $key => $file) {
    //             //$arFiles[$file][$key] = $file;
    //             $fileOwe = File::loadById($file);
    //             if ($fileOwe) {
    //                 $arFiles[$file]["ID"] = $fileOwe->getId();
    //                 $arFiles[$file]["NAME"] = $fileOwe->getName();
    //                 $arFiles[$file]["SIZE"] = $fileOwe->getSize();
    //                 $arFiles[$file]["CREATED_BY"] = $fileOwe->getCreatedBy();
    //                 $arFiles[$file]["CREATE_TIME"] = $fileOwe->getCreateTime()->toString();
    //             }
    //         }
    //     }
    // }
//}



//require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";

/*$data = array(
    "query" => "7812014560",
);

$headers = array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Authorization: Token b10073aafa25d617f46c89ab538dd4693589c7b5",
);

$ch = curl_init('https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);

$res = json_decode($res, JSON_UNESCAPED_UNICODE);

echo "<pre>" . print_r($res, true) . "</pre>";*/

use \Bitrix\Iblock\Elements\ElementRequestsTable;
use Bitrix\Crm\Service;
use \Bitrix\Main\Context;
use \Bitrix\Crm\Item;
use \Bitrix\Main\Loader;
use \Bitrix\Crm\DealTable;
use Bitrix\Iblock\ElementTable;
use \Bitrix\Crm\ProductRowTable;

//\Bitrix\Main\Loader::includeModule("crm");

\Bitrix\Main\Loader::includeModule('crm');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');

/*$dataClass = \Bitrix\Iblock\Iblock::wakeUp(30)->getEntityDataClass();
$element = $dataClass::getList([
    'select' => ['ID'],
    'filter' => ['DEALC.VALUE' => 9]
])->fetchAll();

$req = 0;
foreach ($element as $request){
    $req = $request['ID'];
}*/
//echo "<pre>" . print_r(\CCrmOwnerType::Deal, true) . "</pre>";
//$dealId = 16;
//$productRows = \Bitrix\Crm\ProductRowTable::getList([
//    'select' => ['PRODUCT_ID', 'PRODUCT_NAME', 'PRICE', 'QUANTITY', 'DISCOUNT_SUM'],
//    'filter' => [
//        '=OWNER_ID' => $dealId,
//        '=OWNER_TYPE' => \CCrmOwnerType::Deal,
//    ]
//]);

/*$productRows = \Bitrix\Crm\ProductRowTable::getList([
    'select' => [
        'ID',
        'QUANTITY',
        'PRODUCT_NAME',
        'PRICE',
        'QUANTITY',
        'DISCOUNT_SUM'
    ],
    'filter' => [
        '=OWNER_ID' => intval($dealId),
        '=OWNER_TYPE' => 'D'
    ],
]);

$totalSum = 0;
$totalDiscount = 0;
$totalQuantity = 0;

while ($product = $productRows->fetch()) {
//foreach ($productRows as $product) {
    echo "check";
    $quantity = (float)$product['QUANTITY'];
    $price = (float)$product['PRICE'];
    $discount = (float)$product['DISCOUNT_SUM'];

    $productSum = $price * $quantity;
    $totalSum += $productSum;
    $totalDiscount += $discount;
    $totalQuantity += $quantity;

    $productSumWithDiscount = $productSum - $discount;

    echo "Товар: " . $product['QUANTITY'] . "<br>";
    echo "Товар: " . $product['ID'] . "<br>";
    echo "Цена: " . $price . "<br>";
    echo "Количество: " . $quantity . "<br>";
    echo "Сумма: " . $productSum . "<br>";
    echo "Скидка: " . $discount . "<br>";
    echo "Итого по товару: " . $productSumWithDiscount  . "<br><br>";
}


$dbItems = \Bitrix\Iblock\ElementTable::getList(array(
    'select' => array('ID', 'NAME', 'IBLOCK_ID'),
    'filter' => array('IBLOCK_ID' => 15)
));
$items = [];
while ($arItem = $dbItems->fetch()){
    echo "<pre>" . print_r($arItem, true) . "</pre>";

    $dbPrice = \CPrice::GetBasePrice($arItem['ID']);
    if ($dbPrice) {
        echo "Цена: " . $dbPrice["PRICE"] . " " . $dbPrice["CURRENCY"] . "<br>";
    }

    $rsProduct = \CCatalogProduct::GetByID($arItem['ID']);
    if ($rsProduct) {
        echo "Количество: " . $rsProduct["QUANTITY"]  . "<br>";
    }

    $items [] = $arItem;
}

$PRODUCT_ID = 178;
$quantity = 50;

$arFields = array(
    'QUANTITY' => $quantity,
);

$rsProduct = new \CCatalogProduct();
if ($rsProduct->Update($PRODUCT_ID, $arFields)) {
    echo "Количество товара обновлено";
} else {
    echo "Ошибка обновления: " . $rsProduct->LAST_ERROR;
}*/

//$dbItems = \Bitrix\Iblock\ElementTable::getList(array(
//    'select' => array('ID', 'NAME',),
//    'filter' => array('IBLOCK_ID' => 15)
//));
//$items = [];
//while ($arItem = $dbItems->fetch()){
//    $name = $arItem['NAME'];
//    $dbPrice = \CPrice::GetBasePrice($arItem['ID']);
//    if ($dbPrice) {
//        $price = $dbPrice["PRICE"] . " " . $dbPrice["CURRENCY"];
//    }
//    $items[] = $name . ' (' . $price . ')';
//}
//
//echo "<pre>" . print_r($items, true) . "</pre>";
//echo "<pre>" . print_r(json_encode(['spare_id' => 175, 'count' => 10]), true) . "</pre>";
//$container = Service\Container::getInstance();
//$factory = $container->getFactory(1042);
//$initialFields = [
//    'TITLE' => 'Закупка запчасти ',
//    'UF_IS_AUTO' => true,
//    'UF_SPARES' => json_encode(['spare_id' => 175, 'count' => 10]),
//];
//$item = $factory->createItem($initialFields);
//$saveOperation = $factory->getAddOperation($item);
//$operationResult = $saveOperation->launch();
//if ($operationResult->isSuccess())
//{
//    echo "Добавлена заявка на установку количества запчасти 10";
//}
//else
//{
//    echo "Заявка на установку количества запчасти 10 не добавлена. Что-то пошло не так.";
//}

use Bitrix\Main\UserGroupTable;

$groupId = 20;
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

echo "<pre>" . print_r($arBuyers, true) . "</pre>";


