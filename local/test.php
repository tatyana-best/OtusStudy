<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Примеры');
require_once __DIR__ . "/php_interface/functions.php";
use Bitrix\Main\Diag\Debug;
use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Otus\UserTypes\Booking;
//Debug::startTimeLabel("foo");
$ar = [];
for ($i=0; $i < 10; $i++) { 
    //sleep(2);
    $ar[] = $i;
}
//Debug::endTimeLabel("foo");

//dump($ar);
//sage($ar);
echo "<pre>".print_r($ar, true)."</pre>";
pr($ar);

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

pr(Booking::getProceduresOfDoctor(16, 36));


require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
