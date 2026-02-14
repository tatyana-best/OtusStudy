<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\SystemException;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\LoaderException;
use Diler\Salon\Orm\CarTable;
use Diler\Salon\Data\TestDataInstaller;

Loc::getMessage(__FILE__);

class diler_salon extends CModule
{
    public $MODULE_ID = 'diler.salon';
    public $MODULE_SORT = 500;
    public $MODULE_VERSION;
    public $MODULE_DESCRIPTION;
    public $MODULE_VERSION_DATE;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_DESCRIPTION = Loc::getMessage('DILER_INSTALL_MODULE_DESCRIPTION');
        $this->MODULE_NAME = Loc::getMessage('DILER_INSTALL_MODULE_NAME');
    }

    /**
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        if ($this->isVersionD7()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallAgents();
        } else {
            throw new SystemException(Loc::getMessage('DILER_INSTALL_ERROR_VERSION'));
        }
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     * @throws InvalidPathException
     */
    public function DoUninstall(): void
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @throws InvalidPathException
     */
    public function InstallFiles($params = []): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            CopyDirFiles($component_path, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
        } else {
            throw new InvalidPathException($component_path);
        }
    }

    /**
     * @throws LoaderException
     */
    public function InstallDB(): void
    {
        Loader::includeModule($this->MODULE_ID);

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                Base::getInstance($entity)->createDbTable();
            }
        }

        foreach ($entities as $entity) {
            $this->addEntityElements($entity);
        }
    }

    /**
     * @return void
     */
    public function InstallAgents(): void
    {
        \CAgent::AddAgent(
            "\\Diler\\Salon\\Agents\\getCountProducts::agentCountProducts();",
            "diler.salon",
            "N",
            86400,
            "",
            "Y",
            "31.01.2026 18:23:00",
            30
        );
    }

    /**
     * @return void
     */
    public function InstallEvents(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\Handlers',
            'updateTabs'
        );

        $eventManager->registerEventHandlerCompatible(
            'main',
            'onUserTypeBuildList',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\UserTypeCar',
            'GetUserTypeDescription',
        );

        $eventManager->registerEventHandlerCompatible(
            'main',
            'onUserTypeBuildList',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\UserTypeSpares',
            'GetUserTypeDescription',
        );

        $eventManager->registerEventHandler(
            'crm',
            'OnBeforeCrmDealAdd',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\Handlers',
            'OnBeforeCrmDealAddHandler'
        );

        $eventManager->registerEventHandlerCompatible(
            'rest',
            'OnRestServiceBuildDescription',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Rest\\CRUDMethods',
            'CRUDForCar',
        );
    }

    /**
     * @return void
     */
    public function UnInstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\Handlers',
            'updateTabs'
        );

        $eventManager->unRegisterEventHandler(
            'main',
            'onUserTypeBuildList',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\UserTypeCar',
            'GetUserTypeDescription'
        );

        $eventManager->unRegisterEventHandler(
            'main',
            'onUserTypeBuildList',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\UserTypeSpares',
            'GetUserTypeDescription',
        );

        $eventManager->unRegisterEventHandler(
            'crm',
            'OnBeforeCrmDealAdd',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Crm\\Handlers',
            'OnBeforeCrmDealAddHandler'
        );

        $eventManager->unRegisterEventHandler(
            'rest',
            'OnRestServiceBuildDescription',
            $this->MODULE_ID,
            '\\Diler\\Salon\\Rest\\CRUDMethods',
            'CRUDForCar',
        );
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     */
    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = Application::getConnection();

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                $connection->dropTable($entity::getTableName());
            }
        }
    }

    /**
     * Удаляет файлы, установленные компонентом
     * @throws InvalidPathException
     */
    public function UnInstallFiles(): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            $installed_components = new \DirectoryIterator($component_path);
            foreach ($installed_components as $component) {
                if ($component->isDir() && !$component->isDot()) {
                    $target_path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $component->getFilename();
                    if (Directory::isDirectoryExists($target_path)) {
                        Directory::deleteDirectory($target_path);
                    }
                }
            }
        } else {
            throw new InvalidPathException($component_path);
        }
    }

    /**
     * @param string $entityClass
     * @return void
     */
    private function addEntityElements(string $entityClass): void
    {
        if ($entityClass === CarTable::class) {
            TestDataInstaller::addCars();
        }
    }

    /**
     * @return class-string[]
     */
    private function getEntities(): array
    {
        return [
            CarTable::class,
        ];
    }

    /**
     * @param $notDocumentRoot
     * @return string
     */
    public function getPath($notDocumentRoot = false): string
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    /**
     * @return bool
     */
    public function isVersionD7(): bool
    {
        return version_compare(ModuleManager::getVersion('main'), '20.00.00', '>');
    }
}