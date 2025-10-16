<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class questions extends CModule
{
    public $iblockId = 0;

    public function __construct() {
        if (is_file(__DIR__.'/version.php')) {
            include_once(__DIR__.'/version.php');
            $this->MODULE_ID = get_class($this);
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            $this->MODULE_NAME = Loc::getMessage('QUESTIONS_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage('QUESTIONS_DESCRIPTION');
        } else {
            CAdminMessage::showMessage(
                Loc::getMessage('QUESTIONS_FILE_NOT_FOUND').' version.php'
            );
        }
    }

    function doInstall()
    {
        global $APPLICATION;

        if (CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            $this->installFiles();
            $this->installDB();
            ModuleManager::registerModule($this->MODULE_ID);
            $this->installEvents();
        } else {
            CAdminMessage::showMessage(
                Loc::getMessage('QUESTIONS_INSTALL_ERROR')
            );
            return;
        }

        $APPLICATION->includeAdminFile(
            Loc::getMessage('QUESTIONS_INSTALL_TITLE').' «'.Loc::getMessage('QUESTIONS_NAME').'»',  __DIR__.'/step.php'
        );

    }

    function installFiles()
    {
        CopyDirFiles(
            __DIR__.'/components',
            Application::getDocumentRoot().'/local/components/'.$this->MODULE_ID.'/',
            true,
            true
        );
    }

    function installDB()
    {
        $this->createIblockType();
        $this->createIBlock();
    }

    private function createIblockType()
    {
        $iblockType = new CIBlockType;

        $arFields = [
            'ID' => 'questions',
            'SECTIONS' => 'Y',
            'IN_RSS' => 'N',
            'SORT' => 100,
            'LANG' => [
                'ru' => [
                    'NAME' => 'Вопросы и ответы',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы'
                ],
                'en' => [
                    'NAME' => 'Questions and answers',
                    'SECTION_NAME' => 'Sections',
                    'ELEMENT_NAME' => 'Elements'
                ]
            ]
        ];

        if (!$iblockType->Add($arFields)) {
            throw new Exception('Ошибка создания типа инфоблока: ' . $iblockType->LAST_ERROR);
        }
    }

    private function createIBlock()
    {
        $iblock = new CIBlock;

        $arFields = [
            'ACTIVE' => 'Y',
            'NAME' => 'Вопросы и ответы',
            'CODE' => 'questions_answers',
            'API_CODE' => 'questions',
            'IBLOCK_TYPE_ID' => 'questions',
            'SITE_ID' => ['s1'],
            'SORT' => 100,
            'GROUP_ID' => ['1' => 'X', 'AU' => 'R'],
            'VERSION' => 2,
            'LIST_MODE' => 'S',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'INDEX_ELEMENT' => 'Y',
            'INDEX_SECTION' => 'N',
            'FIELDS' => [
                'CODE' => [
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => [
                        'UNIQUE' => 'Y',
                        'TRANSLITERATION' => 'Y',
                        'TRANS_LEN' => 100,
                        'TRANS_CASE' => 'L',
                        'TRANS_SPACE' => '-',
                        'TRANS_OTHER' => '-',
                        'TRANS_EAT' => 'Y',
                        'USE_GOOGLE' => 'N',
                    ]
                ]
            ]
        ];

        $this->iblockId = $iblock->Add($arFields);

        if (!$this->iblockId) {
            throw new Exception('Ошибка создания инфоблока: ' . $iblock->LAST_ERROR);
        }

        $this->createIBlockProperty($this->iblockId);
    }

    private function createIBlockProperty($iblockId)
    {
        $ibp = new CIBlockProperty;

        $arFields = [
            'NAME' => 'Ответ',
            'ACTIVE' => 'Y',
            'SORT' => 100,
            'CODE' => 'ANSWER',
            'PROPERTY_TYPE' => 'S',
            'IBLOCK_ID' => $iblockId
        ];

        $ibp->Add($arFields);
    }

    function doUninstall()
    {
        global $APPLICATION;

        $this->uninstallFiles();
        $this->uninstallDB();
        $this->uninstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->includeAdminFile(
            Loc::getMessage('QUESTIONS_UNINSTALL_TITLE').' «'.Loc::getMessage('QUESTIONS_NAME').'»', __DIR__.'/unstep.php'
        );
    }

    function unInstallDB()
    {
        $this->deleteIBlock();
        $this->deleteIBlockType();
    }

    private function deleteIBlock()
    {
        $rsIBlocks = CIBlock::GetList([], [
            'TYPE' => 'questions',
            'CODE' => 'questions_answers'
        ]);

        if ($arIBlock = $rsIBlocks->Fetch()) {
            CIBlock::Delete($arIBlock['ID']);
        }
    }

    private function deleteIBlockType()
    {
        CIBlockType::Delete('questions');
    }

    function installEvents()
    {
    }

    function unInstallEvents()
    {
    }

    function unInstallFiles()
    {
        Directory::deleteDirectory(
            Application::getDocumentRoot().'/bitrix/js/'.$this->MODULE_ID
        );

        Directory::deleteDirectory(
            Application::getDocumentRoot().'/bitrix/css/'.$this->MODULE_ID
        );

        Directory::deleteDirectory(
            Application::getDocumentRoot().'/local/components/'.$this->MODULE_ID
        );

        Option::delete($this->MODULE_ID);
    }
}
