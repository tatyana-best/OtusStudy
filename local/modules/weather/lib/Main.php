<?php
  
namespace Weather;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

class Main {

    public $moduleId;

    public function __construct()
    {
        $this->moduleId = pathinfo(dirname(__DIR__))['basename'];
    }

    public function getJsonDataWeather()
    {
        $options = [            
            'text_color' => Option::get($this->moduleId, 'text_color', '#bf3030'),
            'color' => Option::get($this->moduleId, 'color', '#ADFF2F'),                
            'groups' => Option::get($this->moduleId, 'groups', '1,2'),           
        ];

        if (Option::get($this->moduleId, 'icons', 'Y') == 'Y') {
            $options['icons'] = 'Y';
        } else {
            $options['icons'] = 'N';
        }

        // получить ключ: https://yandex.ru/pogoda/b2b/console/api-page

        $url = 'https://api.weather.yandex.ru/v2/forecast?';

        $headers = [
            'X-Yandex-API-Key: bcf02bb7-f890-42cd-b09a-388498b33a68',
            'Access-Control-Allow-Origin: *',
            'Content-Type: application/json; charset=utf-8',          
        ];
        
        $get = array(
            'lat'  => Option::get($this->moduleId, 'lat', '55.45'),
            'lon' => Option::get($this->moduleId, 'lon', '37.36')
        );
         
        $ch = curl_init($url . http_build_query($get));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $json = curl_exec($ch);
        curl_close($ch);       


        $arrWeather = json_decode($json, true);
        $arGeo = [];
        foreach ($arrWeather['info'] as $key => $item) {
            if ($key == 'tzinfo'){
                $arGeo['country'] = explode('/', $item['name'])[0];
                $arGeo['province'] = explode('/', $item['name'])[1];
            }
        }

        $arrResult = array_merge($arrWeather['fact'], $arGeo, $options);
        $options = json_encode($arrResult);

        return $options;
    }

    public function isUserPermission()
    {
        global $USER;
        $userId = $USER->GetID();
        $arGroup = \CUser::GetUserGroup($userId);        
        $group_access = explode(',', Option::get($this->moduleId, 'groups', '1,2'));

        $check = false;
        foreach ($group_access as $val) {
            if (in_array($val, $arGroup)) {
                $check = true;
                break;
            }
        }
            
        return $check;
    }

    public static function appendJavaScriptAndCSS()
    {
        $obj = new self();
        if (Option::get($obj->moduleId, 'switch_on', 'Y') == 'Y') {
            global $APPLICATION;

            if ($APPLICATION->GetCurPage() == '/stream/') {
                if ($obj->isUserPermission()) {
                    Asset::getInstance()->addCss('/bitrix/css/' . $obj->moduleId.'/style.css');
                    
                    if (Option::get($obj->moduleId, 'jquery_on', 'Y') == 'Y') {
                        \CJSCore::init(array('jquery3'));
                    }

                    Asset::getInstance()->addString(
                        "<script id='".$obj->moduleId."-params' data-params='" . $obj->getJsonDataWeather()."'></script>",
                        true
                    );

                    Asset::getInstance()->addJs('/bitrix/js/' . $obj->moduleId.'/script.js');
                } else {                    
                    return false;
                }          
            }
        } else {
            return false;
        }
    }
}
