<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main\Localization\Loc;

/** @property-write string|null ErrorMessage */
class CBPGetCompanyFromDadataActivity extends CBPActivity
{
	public function __construct($name)
	{
        parent::__construct($name);

        $this->arProperties = array(
            'Title' => '',
            'INN' => '',
            'Responsible' => '',

            //Возвращаемые параметры
            'CompanyName' => '',
            'CompanyType' => '',
            'CompanySphere' => '',
            'CompanyCount' => 0,
            'CompanyEmail' => '',
            'CompanyPhone' => '',
            'CompanyAddressFact' => '',
            'CompanyAddressUr' => '',
            'CompanyReq' => '',
            'ErrorMessage' => '',
        );

        $this->SetPropertiesTypes(
            array(
                'INN' => array(
                    'Type' => 'string'
                ),
                'Responsible' => array(
                    'Type' => 'user'
                ),
                'CompanyName' => [
                    'Type' => 'string'
                ],
                'CompanyType' => [
                    'Type' => 'string'
                ],
                'CompanySphere' => [
                    'Type' => 'string'
                ],
                'CompanyCount' => [
                    'Type' => 'int'
                ],
                'CompanyEmail' => [
                    'Type' => 'string'
                ],
                'CompanyPhone' => [
                    'Type' => 'string'
                ],
                'CompanyAddressFact' => [
                    'Type' => 'string'
                ],
                'CompanyAddressUr' => [
                    'Type' => 'string'
                ],
                'CompanyReq' => [
                    'Type' => 'string'
                ],
                'ErrorMessage' => [
                    'Type' => 'string'
                ],
            )
        );
	}

	public function execute()
	{
        $inn = $this->INN;

        if (strlen($inn) != 10 && strlen($inn) != 12) {
            $this->ErrorMessage = Loc::getMessage('ERROR_INN_LENGTH') . ' ';
        }

        if (!is_numeric($inn)) {
            $this->ErrorMessage .= Loc::getMessage('ERROR_INN_LENGTH') . ' ';
        }

        if (!$this->ErrorMessage) {
            $this->setResultFields($inn);
        }

        return CBPActivityExecutionStatus::Closed;
	}

    public function setResultFields($inn)
    {
        $arR = $this->getCompanyData($inn);

        if (!$arR) {
            $this->ErrorMessage = Loc::getMessage('ERROR_COMPANY_NOT_FOUND') . ' ';
        } else {
            $this->CompanyName = $arR['data']['name']['full_with_opf'];
            $this->CompanyType = $arR['value'];
            $this->CompanySphere = $arR['data']['opf']['full'];
            $this->CompanyCount = $arR['data']['employee_count'];
            $this->CompanyEmail = $arR['data']['emails'];
            $this->CompanyPhone = $arR['data']['phones'];
            $this->CompanyAddressFact = $arR['data']['address']['value'];
            $this->CompanyAddressUr = $arR['data']['address']['unrestricted_value'];
            $this->CompanyReq = 'ИНН: ' . $arR['data']['inn'] . ', ОГРН: ' . $arR['data']['ogrn'] . ', ОКАТО: ' . $arR['data']['okato'];
        }
    }

    public function getCompanyData($inn)
    {
        $data = array(
            "query" => $inn,
        );

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token b10073aafa25d617f46c89ab538dd4693589c7b5",
        );

        $ch = curl_init('https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, JSON_UNESCAPED_UNICODE);

        if ($res['suggestions']) {
            return $res['suggestions'][0];
        } else {
            return false;
        }
    }

	public static function GetPropertiesDialog(
		$documentType,
		$activityName,
		$arWorkflowTemplate,
		$arWorkflowParameters,
		$arWorkflowVariables,
		$arCurrentValues = null,
		$formName = '',
		$popupWindow = null
	)
	{
        if (! is_array($arCurrentValues)) {
            $arCurrentValues = array(
                'INN' => '',
                'Responsible' => '',
            );

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate, $activityName);
            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues,
                    $arCurrentActivity['Properties']);
                $arCurrentValues['Responsible'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Responsible'], $arWorkflowTemplate, $documentType);
            }
        }

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(__FILE__, "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName
            ));
	}

	public static function GetPropertiesDialogValues(
		$documentType,
		$activityName,
		&$arWorkflowTemplate,
		&$arWorkflowParameters,
		&$arWorkflowVariables,
		$arCurrentValues,
		&$arErrors
	)
	{
		$arErrors = [];

        if (empty($arCurrentValues['INN']))
        {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_NO_INN')
            );
        }

		if (count($arErrors) > 0)
		{
			return false;
		}

        $arProperties = array(
            'INN' => $arCurrentValues['INN'],
            'Responsible' => CBPHelper::UsersStringToArray(
                $arCurrentValues['Responsible'],
                $documentType,
                $arErrors
            ),
        );

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName
        );
        $arCurrentActivity['Properties'] = $arProperties;

        return true;
	}
}
