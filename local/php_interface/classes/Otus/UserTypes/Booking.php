<?php

namespace Otus\UserTypes;

use \Bitrix\Iblock\Elements\ElementDoctorsTable;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Iblock;
use \Bitrix\Iblock\Elements\ElementBookingTable;

Loader::includeModule('iblock');

class Booking
{
    const BOOKING_IBLOCK_ID = 28;
    const DOCTORS_IBLOCK_ID = 16;

    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE'        => 'E', // тип поля
            'USER_TYPE'            => 'iblock_booking', // код типа пользовательского свойства
            'DESCRIPTION'          => 'Бронирование', // название типа пользовательского свойства
            'GetPropertyFieldHtml' => array(self::class, 'GetPropertyFieldHtml'), // метод отображения свойства
            'GetSearchContent' => array(self::class, 'GetSearchContent'), // метод поиска
            'GetAdminListViewHTML' => array(self::class, 'GetAdminListViewHTML'),  // метод отображения значения в списке
            'GetPublicEditHTML' => array(self::class, 'GetPropertyFieldHtml'), // метод отображения значения в форме редактирования
            'GetPublicViewHTML' => array(self::class, 'GetPublicViewHTML'), // метод отображения значения
        );
    }


    public static function PrepareSettings($arFields)
    {
        //return array("_BLANK" => ($arFields["USER_TYPE_SETTINGS"]["_BLANK"] == "Y" ? "Y" : "N"));
        if (is_array($arFields["USER_TYPE_SETTINGS"]) && $arFields["USER_TYPE_SETTINGS"]["_BLANK"] == "Y"){
            return array("_BLANK" =>  "Y");
        }else{
            return array("_BLANK" =>  "N");
        }
    }


    public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
    {
        $strResult = '';

        $iblockId = $arProperty['IBLOCK_ID'];
        $elementId = $arProperty['ELEMENT_ID'];

        $arProc = self::getProceduresOfDoctor($iblockId, $elementId);

        \CUtil::InitJSCore(array('ajax' , 'popup', 'jquery3', 'date'));

        foreach ($arProc as $arProcItem) {
            $strResult .= "<a class='proc_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "'>" . $arProcItem['NAME'] . "</a>";

            $strResult .= "<script>
            BX.ready(function() {
                let FormFields_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . " = BX.create({
                    tag: 'div',
                    props: { className: 'formFieldsContainer' },
                    children: [
                        BX.create({
                            tag: 'div',
                            props: { className: 'form-group' },
                            children: [
                                BX.create({
                                    tag: 'input',
                                    attrs: {
                                        id: 'reviewName_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "',
                                        placeholder: 'Ваши фамилия и имя',
                                        name: 'reviewName_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "'
                                    },
                                    props: { className: 'form-control' }
                                })
                            ]
                        }),
                        BX.create({
                            tag: 'div',
                            props: { className: 'form-group' },
                            children: [
                                BX.create({
                                    tag: 'input',
                                    attrs: {
                                        id: 'dateTime_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "',
                                        name: 'dateTime_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "',
                                        type: 'text',
                                        placeholder: 'Выберите дату и время'
                                    },
                                    events: { click: function(){BX.calendar({node: this, field: this, bTime: true})}},
                                    props: { className: 'form-control' }
                                })
                            ]
                        }),
                      
                    ]
                });
                let addAnswer_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . " = new BX.PopupWindow(
                'my_answer_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "',
                null, 
                 {
                    content: BX( 'ajax-add-answer_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "'),
                    closeIcon: {right: '20px', top: '20px'},
                    titleBar: {content: BX.create('span', {html: '<b>Запись на прием</b>', 'props': {'className': 'access-title-bar'}})}, 
                    zIndex: 0,
                    offsetLeft: 0,
                    offsetTop: 0,
                    draggable: {restrict: false},
                    buttons: [
                        new BX.PopupWindowButton({
                            text: 'Записаться',
                            className: 'popup-window-button-accept',
                            events: {click: function() {
                                booking();
                                this.popupWindow.close();
                            }}
                        }),
                        new BX.PopupWindowButton({
                            text: 'Закрыть',
                            className: 'webform-button-link-cancel',
                            events: {click: function() {
                                this.popupWindow.close();
                            }}
                        })
                    ]
                });
                
                function booking() {
                    const bookingData = {
                        iblock: " . self::BOOKING_IBLOCK_ID . ",
                        docIblock: " . $iblockId . ",
                        proc: " . $arProcItem['ID_PROC'] . ",
                        doc: " . $arProcItem['ID_DOC'] . ",
                        name: $('#reviewName_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "').val(),
                        dateTime: $('#dateTime_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "').val()
                    };
                    
                    BX.ajax({
                        url: '/local/ajax/booking.php',
                        method: 'POST',
                        dataType: 'json',
                        data: bookingData,
                        onsuccess: function (\$data) {                            
                            let getData = JSON.parse(JSON.stringify(\$data));
                            BX.UI.Notification.Center.notify({
                                content: getData.message + '<br>' + getData.error
                            });                      
                        },
                        onfailure: function (\$data) {
                            console.error();
                        }
                    });
                }
                $('.proc_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "').click(function(){
                    addAnswer_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . ".show();
                    BX.append(FormFields_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . ", $('#popup-window-content-my_answer_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . "')[0]);              
                    
                });
            });             
            </script>";

            $strResult .= "<style>
                .popup-window.popup-window-with-titlebar {
                    padding: 25px;
                    width: 300px;
                    height: 320px;
                    font-size: 16px;
                }
                
                .popup-window-titlebar {
                    height: 35px;
                }
                
                .proc_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . " {
                    cursor: pointer;
                    display: block;
                    padding: 10px;
                    background: #E6E6FA;
                }
                
                #reviewName_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . ",
                #dateTime_" . $arProcItem['ID_PROC'] . "_" . $arProcItem['ID_DOC'] . " {
                    border-radius: 7px;
                    height: 25px;
                    padding: 10px;
                    border: 1px solid #E6E6FA;
                    margin-top: 10px;
                    width: 188px;
                }
            </style>";
        }

        return $strResult;
    }

    public static function getProceduresOfDoctor($iblockId, $elementId): array
    {
        $elements = ElementDoctorsTable::getList([
            'select' => ['NAME', 'PROTSEDURY.ELEMENT', 'ID'],
            'filter' => ['IBLOCK_ID' => $iblockId, 'ID' => $elementId]])
            ->fetchCollection();

        $arResult = [];
        foreach ($elements as $item) {
            $i = 0;
            foreach ($item->getProtsedury()->getAll() as $value) {
                $arResult[$i]['ID_PROC'] = $value->getElement()->getId();
                $arResult[$i]['ID_DOC'] = $elementId;
                $arResult[$i]['NAME'] = $value->getElement()->getName();
                $i ++;
            }
        }

        return $arResult;
    }

    public static function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName)
    {
        $iblock = self::DOCTORS_IBLOCK_ID;
        $element = $arProperty['PROPERTY_VALUE_ID'];

        $arBook = self::getProceduresOfDoctor($iblock, $element);
        $strResult = self::getStrProperty($arBook);

        return $strResult;
    }


    public static function GetSearchContent($arProperty, $value, $strHTMLControlName)
    {
        if (trim($value['VALUE']) != '') {
            return $value['VALUE'] . ' ' . $value['DESCRIPTION'];
        }

        return '';
    }

    public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName)
    {
        global $APPLICATION;

        $iblock = self::DOCTORS_IBLOCK_ID;
        if (!isset($_REQUEST['IBLOCK_ID'])) {
            $element = explode('/', $APPLICATION->getCurPage())[6];
        } else {
            $element = $_REQUEST['ID'];
        }

        $arBook = self::getProceduresOfDoctor($iblock, $element);
        $strResult = self::getStrProperty($arBook);

        return $strResult;
    }

    public static function getStrProperty($arProperty)
    {
        $strResult = '';
        $arResult = [];
        foreach ($arProperty as $value) {
            $elements = ElementBookingTable::getList([
                'select' => ['NAME', 'DOCTOR.ELEMENT', 'PROCEDURE.ELEMENT', 'BOOKING_TIME', 'ID'],
                'filter' => [
                    'IBLOCK_ID' => self::BOOKING_IBLOCK_ID,
                    'DOCTOR.VALUE' => $value['ID_DOC'],
                    'PROCEDURE.VALUE' => $value['ID_PROC'],
                ],
                'count_total' => 1
            ])->fetchCollection();

            foreach ($elements as $item) {
                $arResult[$value['ID_PROC']]['ITEMS'][$item['ID']]['BOOKING_TIME'] = $item->getBookingTime()->getValue();
                $arResult[$value['ID_PROC']]['PROCEDURE'] = $value['NAME'];
                $arResult[$value['ID_PROC']]['ITEMS'][$item['ID']]['PATIENT'] = $item->getName();
            }

            if (!$arResult[$value['ID_PROC']]) {
                $arResult[$value['ID_PROC']]['ITEMS'][$item['ID']]['BOOKING_TIME'] = 'Не забронировано';
                $arResult[$value['ID_PROC']]['PROCEDURE'] = $value['NAME'];
                $arResult[$value['ID_PROC']]['ITEMS'][$item['ID']]['PATIENT'] = '';
            }
        }
        $strResult .= "<div>";
        foreach ($arResult as $proc) {
            $strResult .= "<div style='margin-top: 20px;font-weight:bold;color:green;'>" . $proc['PROCEDURE'] . "</div>";
            foreach ($proc['ITEMS'] as $book) {
                if ($book['PATIENT']) {
                    $strResult .= "<div style='margin-left: 20px;color:maroon;'>" . $book["BOOKING_TIME"] . ': ' . $book["PATIENT"] . "</div>";
                } else {
                    $strResult .= "<div style='margin-left: 20px;color:maroon;'>" . $book["BOOKING_TIME"] . "</div>";
                }
            }
        }
        $strResult .= "</div>";

        return $strResult;
    }
}
