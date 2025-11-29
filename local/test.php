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

use Bitrix\Disk\File;
use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

use Otus\Rest\GetProductsOfSP\GetProductsOfSomeSP;

\Bitrix\Main\Loader::includeModule('crm');
\Bitrix\Main\Loader::includeModule('iblock');
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

\Bitrix\Main\Loader::includeModule('crm');
\Bitrix\Main\Loader::includeModule('iblock');

$smartTypeId = $query['typeId'];
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


echo "<pre>".print_r($arDate, true)."</pre>";

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


$data = array(
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
$arFiles = [];
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
