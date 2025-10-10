<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 */

use Bitrix\Currency\CurrencyTable;
use Bitrix\Currency\UserField\Types\MoneyType;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Random;
use Bitrix\Main\SystemException;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Config\Option;

class CurrencyListComponent extends \CBitrixComponent
{
    protected $request;
    
    public function onIncludeComponentLang(): void
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    public function onPrepareComponentParams($params)
    {
        if ($params['CURRENCY_LIST'] === '')
        {
            $params['CURRENCY_LIST'] = "RUB";
        }

        if (intval($params["CURRENCY_LIMIT"]) == 0) {
            $params['CURRENCY_LIMIT'] = 4;
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

    protected function checkModules(): bool
    {
        return Loader::includeModule('currency');
    }

    protected function prepareData(): void
    {
        $this->$request = Application::getInstance()->getContext()->getRequest();

        if (isset($this->$request['report_list'])) {
            $page = explode('page-', $this->$request['report_list']);
            $page = $page[1];
        } else {
            $page = 1;
        }

        $this->arResult = [];

        $rsCurrency = CurrencyTable::getList([
            "select" => ["*"],
            "filter" => ["CURRENCY" => $this->arParams["CURRENCY_LIST"]],
        ]);

        $arCurrency = [];
        if ($currency = $rsCurrency->fetch()) {
            $arCurrency = $currency;
        }

        $this->arResult['NUM_PAGE'] = $this->arParams["CURRENCY_LIMIT"];
        $this->arResult['LIST'] = $this->getList($this->getRows($arCurrency), $page, $this->arParams["CURRENCY_LIMIT"]);
        $this->getColumns();
    }

    private function getList($data, $page = 1, $limit = 1)
    {
        $offset = $limit * ($page-1);

        $list = [];
        for ($i = $offset; $i < ($offset + $limit); $i++) {
            if ($data[$i]) {
                $list[] = $data[$i];
            }
        }

        return $list;
    }

    protected function getColumns()
    {
        $columns = [
            [
                "id" => "field",
                "name" => "Поле"
            ],
            [
                "id" => "value",
                "name" => "Значение"
            ]
        ];

        $this->arResult["COLUMNS"] = $columns;
    }

    protected function getRows(array $arData)
    {
        $rows = [];
        $dates = ["DATE_UPDATE", "DATE_CREATE"];
        foreach ($arData as $key => $currency) {
            if (!in_array($key, $dates)) {
                if ($currency) {
                    $rows[] = [
                        "id" => $key,
                        "columns" => [
                            "field" => $key,
                            "value" => $currency
                        ]
                    ];
                }
            } else {
                $rows[] = [
                    "id" => $key,
                    "columns" => [
                        "field" => $key,
                        "value" => $currency->format("d.m.Y")
                    ]
                ];
            }
        }

        $this->arResult['COUNT'] = count($rows);

        return $rows;
    }


    public function executeComponent()
    {
        if ($this->checkModules())
        {
            $this->prepareData();
            $this->includeComponentTemplate();
        }
        else
        {
            ShowError(Loc::getMessage('CURRENCY_LIST_MODULE_NOT_INSTALLED'));
        }
    }
}
