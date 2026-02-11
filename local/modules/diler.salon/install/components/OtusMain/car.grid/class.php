<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Diler\Salon\Orm\CarTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\Config\Option;
use Bitrix\Iblock\IblockTable;
use \Bitrix\Main\Context;
use Bitrix\UI\Buttons\Color;

Loader::includeModule('diler.salon');
Loader::includeModule('iblock');

class CarGrid extends \CBitrixComponent implements Controllerable
{
    public function configureActions(): array
    {
        return [];
    }

    /**
     * @param array $fields
     * @return array
     */
    private function getElementActions(array $fields): array
    {
        $arActions = [];
        $arActions[] = [
            'onclick' => sprintf('BX.OtusMain.CarGrid.addBook(%d)', $fields['ID']),
            'text' => 'Заявки',
            'default' => true,
        ];

        return $arActions;
    }

    /**
     * @return array[]
     */
    protected function getButtons(): array
    {
        return [
            [
                'click' => 'BX.OtusMain.CarGrid.addBook',
                'text' => 'Добавить книгу',
                'color' => Color::PRIMARY_DARK,
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getHeaders(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
                'color' => Option::get('diler.salon', 'color')
            ],
            [
                'id' => 'MARKA',
                'name' => Loc::getMessage('CAR_GRID_MARKA_LABEL'),
                'sort' => 'MARKA',
                'default' => true,
                'color' => Option::get('diler.salon', 'text_color')
            ],
            [
                'id' => 'MODEL',
                'name' => Loc::getMessage('CAR_GRID_MODEL_LABEL'),
                'sort' => 'MODEL',
                'default' => true,
                'color' => Option::get('diler.salon', 'color')
            ],
            [
                'id' => 'NUMBER',
                'name' => Loc::getMessage('CAR_GRID_NUMBER_LABEL'),
                'sort' => 'NUMBER',
                'default' => true,
                'color' => Option::get('diler.salon', 'text_color')
            ],
            [
                'id' => 'YEAR',
                'name' => Loc::getMessage('CAR_GRID_YEAR_LABEL'),
                'sort' => 'YEAR',
                'default' => true,
                'color' => Option::get('diler.salon', 'color')
            ],
            [
                'id' => 'COLOR',
                'name' => Loc::getMessage('CAR_GRID_COLOR_LABEL'),
                'sort' => 'COLOR',
                'default' => true,
                'color' => Option::get('diler.salon', 'text_color')
            ],
            [
                'id' => 'KM',
                'name' => Loc::getMessage('CAR_GRID_KM_LABEL'),
                'sort' => 'KM',
                'default' => true,
                'color' => Option::get('diler.salon', 'color')
            ],
        ];
    }

    /**
     * @return void
     */
    public function executeComponent(): void
    {
        if (!$this->isUserPermission()) {
            echo Loc::getMessage('CAR_GRID_NOT_PERMISSION');
            return;
        }

        $this->prepareGridData();
        $this->arResult['BUTTONS'] = $this->getButtons();
        $this->includeComponentTemplate();
    }

    /**
     * @return void
     */
    private function prepareGridData(): void
    {
        $request = Context::getCurrent()->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPostList()->toArray();
        }
        $this->arResult['HEADERS'] = $this->getHeaders();

        $this->arResult['FILTER_ID'] = 'CAR_GRID';

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

        $carIdsQuery = CarTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort'])
        ;

        $countQuery = CarTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
        ;
        $nav->setRecordCount($countQuery->queryCountTotal());

        $carIds = array_column($carIdsQuery->exec()->fetchAll(), 'ID');

        if (!empty($carIds)) {
            $cars = CarTable::getList([
                'filter' => ['ID' => $carIds, 'CONTACT_ID' => $postData['PARAMS']['params']['DEAL_ID']] + $filter,
                'select' => [
                    'ID',
                    'MARKA',
                    'MODEL',
                    'NUMBER',
                    'YEAR',
                    'COLOR',
                    'KM',
                ],
                'order' => $sort['sort'],
            ]);

            $this->arResult['GRID_LIST'] = $this->prepareGridList($cars);
        } else {
            $this->arResult['GRID_LIST'] = [];
        }

        $this->arResult['NAV'] = $nav;
        $this->arResult['UI_FILTER'] = $this->getFilterFields();
    }

    /**
     * @return bool
     */
    public function isUserPermission(): bool
    {
        global $USER;

        $userId = $USER->GetID();
        $arGroup = \CUser::GetUserGroup($userId);
        $group_access = explode(',', Option::Get('diler.salon', 'groups', '21, 1'));

        $check = false;
        foreach ($group_access as $val) {
            if (in_array($val, $arGroup)) {
                $check = true;
                break;
            }
        }

        return $check;
    }

    /**
     * @param array $filterData
     * @return array
     */
    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter['%MARKA'] = $filterData['FIND'];
        }

        if (!empty($filterData['MARKA'])) {
            $filter['%MARKA'] = $filterData['MARKA'];
        }

        if (!empty($filterData['YEAR_from'])) {
            $filter['>=YEAR'] = $filterData['YEAR_from'];
        }

        if (!empty($filterData['YEAR_to'])) {
            $filter['<=YEAR'] = $filterData['YEAR_to'];
        }

        if (!empty($filterData['MODEL'])) {
            $filter['=MODEL'] = $filterData['MODEL'];
        }

        if (!empty($filterData['KM_from'])) {
            $filter['>=KM'] = $filterData['KM_from'];
        }

        if (!empty($filterData['KM_to'])) {
            $filter['<=KM'] = $filterData['KM_to'];
        }

        if (!empty($filterData['NUMBER'])) {
            $filter['=NUMBER'] = $filterData['NUMBER'];
        }

        if (!empty($filterData['COLOR'])) {
            $filter['=COLOR'] = $filterData['COLOR'];
        }

        return $filter;
    }

    /**
     * @param Result $cars
     * @return array
     */
    private function prepareGridList(Result $cars): array
    {
        $gridList = [];
        $groupedCars = [];

        while ($car = $cars->fetch()) {
            $carId = $car['ID'];

            if (!isset($groupedCars[$carId])) {
                $groupedCars[$carId] = [
                    'ID' => $car['ID'],
                    'MARKA' => $car['MARKA'],
                    'YEAR' => $car['YEAR'],
                    'MODEL' => $car['MODEL'],
                    'COLOR' => $car['COLOR'],
                    'NUMBER' => $car['NUMBER'],
                    'KM' => $car['KM'],
                ];
            }
        }

        foreach ($groupedCars as $car) {
            $gridList[] = [
                'data' => [
                    'ID' => $car['ID'],
                    'MARKA' => $car['MARKA'],
                    'YEAR' => $car['YEAR'],
                    'MODEL' => $car['MODEL'],
                    'COLOR' => $car['COLOR'],
                    'NUMBER' => $car['NUMBER'],
                    'KM' => $car['KM'],
                ],
                'actions' => $this->getElementActions(['ID' => $car['ID']]),
            ];
        }

        return $gridList;
    }

    /**
     * @return array[]
     */
    private function getFilterFields(): array
    {
        return [
            [
                'id' => 'MARKA',
                'name' => Loc::getMessage('CAR_GRID_MARKA_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'YEAR',
                'name' => Loc::getMessage('CAR_GRID_YEAR_LABEL'),
                'type' => 'number',
                'default' => true,
            ],
            [
                'id' => 'MODEL',
                'name' => Loc::getMessage('CAR_GRID_MODEL_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'NUMBER',
                'name' => Loc::getMessage('CAR_GRID_NUMBER_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'COLOR',
                'name' => Loc::getMessage('CAR_GRID_COLOR_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'KM',
                'name' => Loc::getMessage('CAR_GRID_KM_LABEL'),
                'type' => 'number',
                'default' => true,
            ],
        ];
    }
}
