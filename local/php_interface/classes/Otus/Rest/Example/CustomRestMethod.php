<?php

namespace Otus\Rest\Example;

class CustomRestMethod
{
    public static function addCustomRestMethods(): array
    {
        return [
            'iblock' => [
                'iblock.Element.add' => [
                    'callback' => ['Otus\\Rest\\Example\\NewIblockElement', 'iBlockElementAdd'],
                    'options' => [],
                ],
            ],
            'crm' => [
                'crm.contact.dealslist' => [
                    'callback' => ['Otus\\Rest\\Example\\GetDealsListOfContact', 'GetDealsOfContact'],
                    'options' => [],
                ],
                'crm.productsofsp.list' => [
                    'callback' => ['Otus\\Rest\\GetProductsOfSP\\GetProductsOfSomeSP', 'GetProducts'],
                    'options' => [],
                ],
            ],
        ];
    }
}
