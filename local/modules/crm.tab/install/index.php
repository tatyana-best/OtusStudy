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
use Crm\Tab\Orm\BookTable;
use Crm\Tab\Orm\AuthorTable;
use Crm\Tab\Data\TestDataInstaller;

Loc::getMessage(__FILE__);

class crm_tab extends CModule
{
    public $MODULE_ID = 'crm.tab';
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
        $this->MODULE_DESCRIPTION = Loc::getMessage('TAB_INSTALL_MODULE_DESCRIPTION');
        $this->MODULE_NAME = Loc::getMessage('TAB_INSTALL_MODULE_NAME');
    }

    /**
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        if ($this->isVersionD7()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallFiles();
            //$this->InstallDB(); // скрыла, чтобы данные не удалять
            $this->InstallEvents();

        } else {
            throw new SystemException(Loc::getMessage('TAB_INSTALL_ERROR_VERSION'));
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
        //$this->UnInstallDB(); // скрыла, чтобы данные не удалять
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

        $this->installManyToManyTable();

        foreach ($entities as $entity) {
            $this->addEntityElements($entity);
        }

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
        $iblockList = IblockTable::getList([
            'select' => ['ID'],
            'filter' => ['CODE' => 'questions_answers'],
        ]);

        if ($iblock = $iblockList->fetch()) {
            throw new Exception('Ошибка создания инфоблока: ' . $iblock->LAST_ERROR);
        } else {
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

        if (!$ibp->Add($arFields)) {
            throw new Exception('Ошибка создания свойства инфоблока: ' . $ibp->LAST_ERROR);
        }
    }

    public function InstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Crm\\Tab\\Crm\\Handlers',
            'updateTabs'
        );
    }

    public function UnInstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Crm\\Tab\\Crm\\Handlers',
            'updateTabs'
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
        $this->unInstallManyToManyTable();

        foreach ($entities as $entity) {
            if (Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                $connection->dropTable($entity::getTableName());
            }
        }

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

    private function addEntityElements(string $entityClass): void
    {
        if ($entityClass === AuthorTable::class) {
            TestDataInstaller::addAuthors();
        } elseif ($entityClass === BookTable::class) {
            TestDataInstaller::addBooks();
        }
    }

    private function installManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'aholin_book_author';

        if (!$connection->isTableExists($tableName)) {
            $connection->queryExecute("
            CREATE TABLE {$tableName} (
                BOOK_ID int NOT NULL,
                AUTHOR_ID int NOT NULL,
                PRIMARY KEY (BOOK_ID, AUTHOR_ID)
            )
        ");
        }
    }

    /**
     * @throws SqlQueryException
     */
    private function unInstallManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'aholin_book_author';

        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    private function getEntities(): array
    {
        return [
            AuthorTable::class,
            BookTable::class,
        ];
    }

    public function getPath($notDocumentRoot = false): string
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public function isVersionD7(): bool
    {
        return version_compare(ModuleManager::getVersion('main'), '20.00.00', '>');
    }
}