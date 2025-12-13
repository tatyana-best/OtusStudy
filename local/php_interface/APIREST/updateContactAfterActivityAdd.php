<?php

$activityId = $_REQUEST['data']['FIELDS']['ID'];
$urlGetContact = 'https://b24mybeget.ru/rest/1/ak1lo5p03z1vxabz/crm.activity.binding.list';
$urlUpdateContact = 'https://b24mybeget.ru/rest/1/ak1lo5p03z1vxabz/crm.contact.update';
$fieldDateLastContact = 'UF_CRM_1765617770023';
$fieldTest = 'UF_CRM_1765618389735';
$testText = 'проверка, все ок!!!';

updateContact(
    $activityId,
    $urlUpdateContact,
    $urlGetContact,
    $fieldDateLastContact,
    $fieldTest,
    $testText
);

function updateContact(
    $activityId,
    $urlUpdateContact,
    $urlGetContact,
    $fieldDateLastContact,
    $fieldTest,
    $testText
)
{
    $data = ['activityId' => $activityId];
    $contactId = 0;
    $connect = connectWithCurl($urlGetContact, $data);
    if ($connect['result']) {
        foreach ($connect['result'] as $result) {
            if ($result['entityTypeId'] == 3) {
                $contactId = $result['entityId'];
            }
        }
    }

    if ($contactId !== 0) {
        $data = array(
            'id' => $contactId,
            'fields' => [
                $fieldTest => $testText,
                $fieldDateLastContact => date('Y-m-d'),
            ]
        );

        $connect = connectWithCurl($urlUpdateContact, $data);
        if (!$connect['result']) {
            file_put_contents(getcwd() . '/hook.txt', "Ошибка при обновлении контакта. " . $connect['error']['error_message'], FILE_APPEND);
        };
    } else {
        file_put_contents(getcwd() . '/hook.txt', "Ошибка при получении id контакта. ", FILE_APPEND);
    }
}

function connectWithCurl($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($res, JSON_UNESCAPED_UNICODE);

    return $res;
}
