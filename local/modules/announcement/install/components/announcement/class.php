<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class Announcement extends \CBitrixComponent
{
    public function onPrepareComponentParams($params)
    {
        if (\Bitrix\Main\Loader::includeModule('announcement'))
        {
            $params['TEXT'] = (isset($params['TEXT']) ? trim($params['TEXT']) : COption::GetOptionString('announcement', 'text'));
            $params['TEXT_COLOR'] = (isset($params['TEXT_COLOR']) ? trim($params['TEXT_COLOR']) : COption::GetOptionString('announcement', 'text_color'));
            $params['COLOR'] = (isset($params['COLOR']) ? trim($params['COLOR']) : COption::GetOptionString('announcement', 'color'));
        }

        if ($params['CACHE_TYPE'] == 'Y' || ($params['CACHE_TYPE'] == 'A' && Option::get('main', 'component_cache_on', 'Y') == 'Y'))
        {
            $params['CACHE_TIME'] = intval(($params['CACHE_TIME']) ? $params['CACHE_TIME'] : 0);
        }
        else
        {
            $params['CACHE_TIME'] = 0;
        }

        return $params;
    }

    protected function setCache()
    {
        global $USER;

        return $this->startResultCache(false, $USER->GetGroups());
    }

    protected function checkModules()
    {
        if (!\Bitrix\Main\Loader::includeModule('announcement'))
        {
            echo Loc::getMessage('MODULE_IS_NOT_INSTALLED');

            return false;
        }

        return true;
    }

    public function isUserPermission()
    {
        global $USER;
        $userId = $USER->GetID();
        $arGroup = \CUser::GetUserGroup($userId);
        $group_access = explode(',', COption::GetOptionString('announcement', 'groups', '1,2'));

        $check = false;
        foreach ($group_access as $val) {
            if (in_array($val, $arGroup)) {
                $check = true;
                break;
            }
        }

        return $check;
    }

    public function executeComponent()
    {
        if (!$this->checkModules())
        {
            return;
        }

        if (!$this->isUserPermission()) {
            return;
        }

        if (Option::Get('announcement', 'switch_on') != 'Y') {
            return;
        }

        $this->arResult = array(
            'TEXT' => $this->arParams['TEXT'],
            'TEXT_COLOR' => $this->arParams['TEXT_COLOR'],
            'COLOR' => $this->arParams['COLOR'],
        );

        if ($this->setCache())
        {
            $this->includeComponentTemplate();
        }
    }
}
