<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main\Localization\Loc;

$arActivityDescription = [
	'NAME' => Loc::getMessage('GCD_DESCR_DESCR'),
	'DESCRIPTION' => Loc::getMessage('GCD_DESCR_NAME'),
	'TYPE' => 'activity',
	'CLASS' => 'GetCompanyFromDadataActivity',
	'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => array(
        'ID' => 'crm',
    ),
	'RETURN' => [
		'CompanyName' => [
			'NAME' => GetMessage('GCD_COMPANY_NAME'),
			'TYPE' => 'string',
		],
        'CompanyType' => [
            'NAME' => GetMessage('GCD_COMPANY_TYPE'),
            'TYPE' => 'string',
        ],
        'CompanySphere' => [
            'NAME' => GetMessage('GCD_COMPANY_SPHERE'),
            'TYPE' => 'string',
        ],
        'CompanyCount' => [
            'NAME' => GetMessage('GCD_COMPANY_COUNT'),
            'TYPE' => 'int',
        ],
        'Responsible' => [
            'NAME' => GetMessage('GCD_RESPONSIBLE'),
            'TYPE' => 'user',
        ],
        'CompanyEmail' => [
            'NAME' => GetMessage('GCD_COMPANY_EMAIL'),
            'TYPE' => 'int',
        ],
        'CompanyPhone' => [
            'NAME' => GetMessage('GCD_COMPANY_PHONE'),
            'TYPE' => 'int',
        ],
        'CompanyAddressFact' => [
            'NAME' => GetMessage('GCD_COMPANY_ADDRESS_FACT'),
            'TYPE' => 'int',
        ],
        'CompanyAddressUr' => [
            'NAME' => GetMessage('GCD_COMPANY_ADDRESS_UR'),
            'TYPE' => 'int',
        ],
        'CompanyReq' => [
            'NAME' => GetMessage('GCD_COMPANY_REQ'),
            'TYPE' => 'int',
        ],
		'ErrorMessage' => [
			'NAME' => Loc::getMessage('GCD_DESCR_ERROR_MESSAGE'),
			'TYPE' => 'string',
		],
	],
];
