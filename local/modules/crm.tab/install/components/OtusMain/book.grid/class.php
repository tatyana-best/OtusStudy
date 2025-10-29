<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Crm\Tab\Orm\BookTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\Config\Option;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\Elements\ElementQuestionsTable;

Loader::includeModule('crm.tab');
Loader::includeModule('iblock');

class BookGrid extends \CBitrixComponent implements Controllerable
{
    public function configureActions(): array
    {
        return [];
    }

    private function getElementActions(): array
    {
        $arActions = [];
        $arActions[] =  array(
            'ICONCLASS' => 'view',
            'TITLE' => 'Ссылка на главную',
            'TEXT' => 'Otus',
            'ONCLICK' => "window.open('https://otus.ru/', '_blank');",
            'DEFAULT' => false
        );

        return $arActions;
    }

    private function getHeaders(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
                'color' => Option::get('crm.tab', 'color')
            ],
            [
                'id' => 'TITLE',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_TITLE_LABEL'),
                'sort' => 'TITLE',
                'default' => true,
                'color' => Option::get('crm.tab', 'text_color')
            ],
            [
                'id' => 'YEAR',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_PUBLISHING_YEAR_LABEL'),
                'sort' => 'YEAR',
                'default' => true,
                'color' => Option::get('crm.tab', 'color')
            ],
            [
                'id' => 'PAGES',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_PAGES_LABEL'),
                'sort' => 'PAGES',
                'default' => true,
                'color' => Option::get('crm.tab', 'text_color')
            ],
            [
                'id' => 'AUTHORS',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_AUTHORS_LABEL'),
                'default' => true,
                'color' => Option::get('crm.tab', 'color')
            ],
            [
                'id' => 'PUBLISH_DATE',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_PUBLISHING_DATE_LABEL'),
                'sort' => 'PUBLISH_DATE',
                'default' => true,
                'color' => Option::get('crm.tab', 'text_color')
            ],
        ];
    }

    private function getHeadersQ(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
                'color' => Option::get('crm.tab', 'color')
            ],
            [
                'id' => 'QUESTION',
                'name' => Loc::getMessage('QUESTIONS_GRID_QUESTION'),
                'sort' => 'QUESTION',
                'default' => true,
                'color' => Option::get('crm.tab', 'text_color')
            ],
            [
                'id' => 'ANSWER',
                'name' => Loc::getMessage('QUESTIONS_GRID_ANSWER'),
                'sort' => 'ANSWER',
                'default' => true,
                'color' => Option::get('crm.tab', 'color')
            ],
        ];
    }

    public function executeComponent(): void
    {
        if (!$this->isUserPermission()) {
            echo Loc::getMessage('QUESTIONS_NOT_PERMISSION');
            return;
        }

        $this->prepareGridData();
        $this->prepareGridDataQ();
        $this->includeComponentTemplate();
    }

    private function prepareGridData(): void
    {
        $this->arResult['HEADERS'] = $this->getHeaders();

        $this->arResult['FILTER_ID'] = 'BOOK_GRID';

        $gridOptions = new GridOptions($this->arResult['FILTER_ID']);
        $navParams = $gridOptions->getNavParams();

        $nav = new PageNavigation($this->arResult['FILTER_ID']);
        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $filterOption = new FilterOptions($this->arResult['FILTER_ID']);
        $filterData = $filterOption->getFilter([]);
        $filter = $this->prepareFilter($filterData);


        $sort = $gridOptions->getSorting([
            'sort' => [
                'ID' => 'DESC',
            ],
            'vars' => [
                'by' => 'by',
                'order' => 'order',
            ],
        ]);

        $bookIdsQuery = BookTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort'])
        ;

        $countQuery = BookTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
        ;
        $nav->setRecordCount($countQuery->queryCountTotal());

        $bookIds = array_column($bookIdsQuery->exec()->fetchAll(), 'ID');

        if (!empty($bookIds)) {
            $books = BookTable::getList([
                'filter' => ['ID' => $bookIds] + $filter,
                'select' => [
                    'ID',
                    'TITLE',
                    'YEAR',
                    'PAGES',
                    'PUBLISH_DATE',
                    'AUTHOR_ID' => 'AUTHORS.ID',
                    'AUTHOR_FIRST_NAME' => 'AUTHORS.FIRST_NAME',
                    'AUTHOR_LAST_NAME' => 'AUTHORS.LAST_NAME',
                    'AUTHOR_SECOND_NAME' => 'AUTHORS.SECOND_NAME',
                ],
                'order' => $sort['sort'],
            ]);

            $this->arResult['GRID_LIST'] = $this->prepareGridList($books);
        } else {
            $this->arResult['GRID_LIST'] = [];
        }

        $this->arResult['NAV'] = $nav;
        $this->arResult['UI_FILTER'] = $this->getFilterFields();
    }

    private function prepareGridDataQ(): void
    {
        $this->arResult['HEADERSQ'] = $this->getHeadersQ();

        $this->arResult['FILTER_IDQ'] = 'QUESTIONS';

        $gridOptions = new GridOptions($this->arResult['FILTER_IDQ']);
        $navParams = $gridOptions->getNavParams();

        $nav = new PageNavigation($this->arResult['FILTER_IDQ']);
        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $filterOption = new FilterOptions($this->arResult['FILTER_IDQ']);
        $filterData = $filterOption->getFilter([]);
        $filter = $this->prepareFilterQ($filterData);

        $sort = $gridOptions->getSorting([
            'sort' => [
                'ID' => 'DESC',
            ],
            'vars' => [
                'by' => 'by',
                'order' => 'order',
            ],
        ]);

        $questionIdsQuery = ElementQuestionsTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort'])
        ;

        $countQuery = ElementQuestionsTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
        ;

        $nav->setRecordCount($countQuery->queryCountTotal());

        $questionIds = array_column($questionIdsQuery->exec()->fetchAll(), 'ID');

        if (!empty($questionIds)) {
            $elements = ElementQuestionsTable::query()
                ->addSelect('NAME')
                ->addSelect('ANSWER')
                ->addSelect('ID')
                ->setFilter(['ID' => $questionIds])
                ->fetchCollection();

            $arQuestion =[];
            foreach ($elements as $key => $item) {
                $arQuestion[$key]['ID'] = $item->getId();
                $arQuestion[$key]['NAME'] = $item->getName();
                $arQuestion[$key]['ANSWER'] = $item->getAnswer()->getValue();
            }

            $this->arResult['GRID_LISTQ'] = $this->getQuestionsAndAnswers($arQuestion);
        } else {
            $this->arResult['GRID_LISTQ'] = [];
        }

        $this->arResult['NAVQ'] = $nav;
        $this->arResult['UI_FILTERQ'] = $this->getFilterFieldsQ();
    }

    public function getQuestionsAndAnswers($arQuestion): array
    {
        $gridList = [];
        foreach ($arQuestion as $question) {
            $gridList[] = [
                'data' => [
                    'ID' => $question['ID'],
                    'QUESTION' => $question['NAME'],
                    'ANSWER' => $question['ANSWER'],
                ],
                'actions' => $this->getElementActions(),
            ];
        }

        return $gridList;
    }

    public function isUserPermission(): bool
    {
        global $USER;

        $userId = $USER->GetID();
        $arGroup = \CUser::GetUserGroup($userId);
        $group_access = explode(',', Option::Get('crm.tab', 'groups', '1,2'));

        $check = false;
        foreach ($group_access as $val) {
            if (in_array($val, $arGroup)) {
                $check = true;
                break;
            }
        }

        return $check;
    }

    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter['%TITLE'] = $filterData['FIND'];
        }

        if (!empty($filterData['TITLE'])) {
            $filter['%TITLE'] = $filterData['TITLE'];
        }

        if (!empty($filterData['YEAR_from'])) {
            $filter['>=YEAR'] = $filterData['YEAR_from'];
        }

        if (!empty($filterData['YEAR_to'])) {
            $filter['<=YEAR'] = $filterData['YEAR_to'];
        }

        if (!empty($filterData['PUBLISH_DATE_from'])) {
            $filter['>=PUBLISH_DATE'] = $filterData['PUBLISH_DATE_from'];
        }

        if (!empty($filterData['PUBLISH_DATE_to'])) {
            $filter['<=PUBLISH_DATE'] = $filterData['PUBLISH_DATE_to'];
        }

        return $filter;
    }

    private function prepareFilterQ(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter['%NAME'] = $filterData['QUESTION'];
        }

        if (!empty($filterData['QUESTION'])) {
            $filter['%NAME'] = $filterData['QUESTION'];
        }
        if (!empty($filterData['ANSWER'])) {
            $filter['%ANSWER.VALUE'] = $filterData['ANSWER'];
        }

        return $filter;
    }

    private function prepareGridList(Result $books): array
    {
        $gridList = [];
        $groupedBooks = [];

        while ($book = $books->fetch()) {
            $bookId = $book['ID'];

            if (!isset($groupedBooks[$bookId])) {
                $groupedBooks[$bookId] = [
                    'ID' => $book['ID'],
                    'TITLE' => $book['TITLE'],
                    'YEAR' => $book['YEAR'],
                    'PAGES' => $book['PAGES'],
                    'PUBLISH_DATE' => $book['PUBLISH_DATE'],
                    'AUTHORS' => []
                ];
            }

            if ($book['AUTHOR_ID']) {
                $groupedBooks[$bookId]['AUTHORS'][] = implode(' ', array_filter([
                    $book['AUTHOR_LAST_NAME'],
                    $book['AUTHOR_FIRST_NAME'],
                    $book['AUTHOR_SECOND_NAME']
                ]));
            }
        }

        foreach ($groupedBooks as $book) {
            $gridList[] = [
                'data' => [
                    'ID' => $book['ID'],
                    'TITLE' => $book['TITLE'],
                    'YEAR' => $book['YEAR'],
                    'PAGES' => $book['PAGES'],
                    'AUTHORS' => implode(', ', $book['AUTHORS']),
                    'PUBLISH_DATE' => $book['PUBLISH_DATE']->format('d.m.Y'),
                ],
                'actions' => $this->getElementActions(),
            ];
        }

        return $gridList;
    }

    private function getFilterFields(): array
    {
        return [
            [
                'id' => 'TITLE',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_TITLE_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'YEAR',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_PUBLISHING_YEAR_LABEL'),
                'type' => 'number',
                'default' => true,
            ],
            [
                'id' => 'PUBLISH_DATE',
                'name' => Loc::getMessage('BOOK_GRID_BOOK_PUBLISHING_DATE_LABEL'),
                'type' => 'date',
                'default' => true,
            ],
        ];
    }

    private function getFilterFieldsQ(): array
    {
        return [
            [
                'id' => 'QUESTION',
                'name' => Loc::getMessage('QUESTION_QUESTION_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'ANSWER',
                'name' => Loc::getMessage('QUESTION_ANSWER_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
        ];
    }
}
