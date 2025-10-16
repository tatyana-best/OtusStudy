<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\SystemException;
use Bitrix\Iblock\Elements\ElementQuestionsTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class Questions extends \CBitrixComponent
{
    public $ibCode = 'questions_answers';
    public $moduleCode = 'questions';
    public $addElementLink = '';

    public function onPrepareComponentParams($params)
    {
        return $params;
    }

    protected function setCache(): bool
    {
        global $USER;

        return $this->startResultCache(false, $USER->GetGroups());
    }

    protected function checkModules(): bool
    {
        if (!Loader::includeModule($this->moduleCode) || !Loader::includeModule('iblock'))
        {
            return false;
        }

        return true;
    }

    public function isUserPermission(): bool
    {
        global $USER;

        $userId = $USER->GetID();
        $arGroup = \CUser::GetUserGroup($userId);
        $group_access = explode(',', Option::Get($this->moduleCode, 'groups', '1,2'));

        $check = false;
        foreach ($group_access as $val) {
            if (in_array($val, $arGroup)) {
                $check = true;
                break;
            }
        }

        return $check;
    }

    public function getQuestionsAndAnswers(): array
    {
        $elements = ElementQuestionsTable::query()
            ->addSelect('NAME')
            ->addSelect('ANSWER')
            ->addSelect('ID')
            ->fetchCollection();

        $arQuestion =[];
        foreach ($elements as $key => $item) {
            $arButtons = CIBlock::GetPanelButtons(
                $this->ibCode, $key, 0, ['SECTION_BUTTONS' => false, 'SESSID' => false]
            );

            $arQuestion[$key]['EDIT_LINK'] = $arButtons['edit']['edit_element']['ACTION_URL'];
            $arQuestion[$key]['DELETE_LINK'] = $arButtons['edit']['delete_element']['ACTION_URL'];
            $this->addElementLink = $arButtons['edit']['add_element']['ACTION_URL'];

            $arQuestion[$key]['ID'] = $item->getId();
            $arQuestion[$key]['NAME'] = $item->getName();
            $arQuestion[$key]['ANSWER'] = $item->getAnswer()->getValue();
        }

        return $arQuestion;
    }

    public static function getIblockIdByCode(string $code): int
    {
        return IblockTable::getList([
            'filter' => [
                'CODE' => $code,
            ],
            'select' => [
                'ID',
            ],
            'limit' => 1,
            'cache' => [
                'ttl' => 360000000,
            ],
        ])->fetch()['ID'] ?? 0;
    }

    public function executeComponent()
    {
        if (!$this->checkModules()) {
            echo Loc::getMessage('QUESTIONS_MODULE_IS_NOT_INSTALLED');
            return;
        }

        if (!$this->isUserPermission()) {
            echo Loc::getMessage('QUESTIONS_NOT_PERMISSION');
            return;
        }

        if (Option::Get($this->moduleCode, 'switch_on') != 'Y') {
            echo Loc::getMessage('QUESTIONS_MODULE_NOT_ACTIVE');
            return;
        }

        if (!$this->getIblockIdByCode($this->ibCode)) {
            echo Loc::getMessage('QUESTIONS_IBLOCK_NOT_EXISTS');
            return;
        }


        $this->arResult = [
            'IBLOCK' => $this->getIblockIdByCode($this->ibCode),
            'TEXT_COLOR' => Option::Get($this->moduleCode, 'text_color'),
            'COLOR' => Option::Get($this->moduleCode, 'color'),
            'MARGIN_TOP' => Option::Get($this->moduleCode, 'margin_top'),
            'MARGIN_BOTTOM' => Option::Get($this->moduleCode, 'margin_bottom'),
            'QUESTIONS' => $this->getQuestionsAndAnswers(),
            'ADD_LINK' => $this->addElementLink,
        ];

        if ($this->setCache()) {
            $this->includeComponentTemplate();
        }
    }
}
