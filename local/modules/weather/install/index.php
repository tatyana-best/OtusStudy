<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class weather extends CModule
{	
	public function __construct() {
        if (is_file(__DIR__.'/version.php')) {
            include_once(__DIR__.'/version.php');
            $this->MODULE_ID = get_class($this);
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            $this->MODULE_NAME = Loc::getMessage('WEATHER_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage('WEATHER_DESCRIPTION');
        } else {
            CAdminMessage::showMessage(
                Loc::getMessage('WEATHER_FILE_NOT_FOUND').' version.php'
            );
        }
    }
	
	function doInstall()
	{
		global $APPLICATION;

        if (CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            $this->installFiles();
            $this->installDB();
            $this->installEvents();
            ModuleManager::registerModule($this->MODULE_ID);
        } else {
            CAdminMessage::showMessage(
                Loc::getMessage('WEATHER_INSTALL_ERROR')
            );
            return;
        }

        $APPLICATION->includeAdminFile(
            Loc::getMessage('WEATHER_INSTALL_TITLE').' «'.Loc::getMessage('WEATHER_NAME').'»',  __DIR__.'/step.php'
        );

	}

	function installFiles()
	{		
        CopyDirFiles(
            __DIR__.'/assets/scripts',
            Application::getDocumentRoot().'/bitrix/js/'.$this->MODULE_ID.'/',
            true,
            true
        );

        CopyDirFiles(
            __DIR__.'/assets/styles',
            Application::getDocumentRoot().'/bitrix/css/'.$this->MODULE_ID.'/',
            true,
            true
        );
	}

	function installDB()
	{
		return;
	}
	
	function doUninstall()
	{
		global $APPLICATION;

        $this->uninstallFiles();
        $this->uninstallDB();
        $this->uninstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->includeAdminFile(
            Loc::getMessage('WEATHER_UNINSTALL_TITLE').' «'.Loc::getMessage('WEATHER_NAME').'»', __DIR__.'/unstep.php'
        );	
	}
	
	function unInstallDB()
	{
		return;
	}
	
	function installEvents()
	{
		EventManager::getInstance()->registerEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            $this->MODULE_ID,
            'Weather\\Main',
            'appendJavaScriptAndCSS'
        );
	}
	
	function unInstallEvents()
	{
		EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            $this->MODULE_ID,
            'Weather\\Main',
            'appendJavaScriptAndCSS'
        );
	}
	
	function unInstallFiles()
	{		
        Directory::deleteDirectory(
            Application::getDocumentRoot().'/bitrix/js/'.$this->MODULE_ID
        );
        
        Directory::deleteDirectory(
            Application::getDocumentRoot().'/bitrix/css/'.$this->MODULE_ID
        );
        
        Option::delete($this->MODULE_ID);
	}	
}
