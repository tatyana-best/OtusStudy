<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Bizproc\Result\ResultDto;
use Bitrix\Main\Localization\Loc;

/** @property-write string|null ErrorMessage */
class CBPGetCompanyFromDadataActivity extends CBPActivity
{
	const EXECUTION_MAX_DEPTH = 1;
	private static $executionDepth = [];

	public function __construct($name)
	{
		//parent::__construct($name);
        $this->arProperties = array(
            'INN' => '',
        );

        $this->SetPropertiesTypes(
            array(
                'INN' => array(
                    'Type' => FieldType::STRING
                ),
                'Responsible' => ''
            ));
	}

	public function execute()
	{
		$documentId = $this->getDocumentId();
		$documentType = $this->getCreatedDocumentType();

		$fieldValue = $this->Fields;
		if (!is_array($fieldValue))
		{
			$fieldValue = [];
		}

		$documentService = $this->workflow->GetService('DocumentService');
		$resultFields = $this->prepareFieldsValues($documentType, $fieldValue);

		$executionKey = $this->getWorkflowTemplateId();

		self::increaseExecutionDepth($executionKey);
		try
		{
			$this->ItemId = $documentService->createDocument($documentId, $resultFields);
		}
		catch (Exception $e)
		{
			$this->writeToTrackingService($e->getMessage(), 0, CBPTrackingType::Error);
			$this->ErrorMessage = $e->getMessage();
		}

		if ($this->ItemId)
		{
			$this->fixResult($this->makeResultFromId($this->ItemId));
		}

		self::resetExecutionDepth($executionKey);

		return CBPActivityExecutionStatus::Closed;
	}

	protected function getCreatedDocumentType(): array
	{
		return $this->getDocumentType();
	}

	public function makeResultFromId(int $id): ResultDto
	{
		$type = $this->getCreatedDocumentType();
		$documentId = $type;
		$documentId[2] = $id;

		/** @var CBPDocumentService $documentService */
		$documentService = CBPRuntime::GetRuntime()->GetService('DocumentService');
		$documentId = $documentService->normalizeDocumentId($documentId, $type[2]);

		$resultValue = [
			'DOCUMENT_ID' => $documentId,
			'DOCUMENT_TYPE' => $type,
		];

		return new ResultDto(get_class($this), $resultValue);
	}

	protected function reInitialize()
	{
		parent::reInitialize();
		$this->ErrorMessage = null;
		$this->ItemId = 0;
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

		$runtime = CBPRuntime::getRuntime();

		$arProperties = ['Fields' => []];

		$documentService = $runtime->GetService('DocumentService');
		$arDocumentFields = $documentService->getDocumentFields($documentType);

		foreach ($arDocumentFields as $fieldKey => $fieldValue)
		{
			if (empty($fieldValue['Editable']))
			{
				continue;
			}

			$arFieldErrors = [];
			$r = $documentService->getFieldInputValue(
				$documentType,
				$fieldValue,
				$fieldKey,
				$arCurrentValues,
				$arFieldErrors
			);

			if (is_array($arFieldErrors) && !empty($arFieldErrors))
			{
				$arErrors = array_merge($arErrors, $arFieldErrors);
			}

			if ($fieldValue['BaseType'] == 'user')
			{
				if ($r === 'author')
				{
					//HACK: We can't resolve author for new document - setup target user as author.
					$r = '{=Template:TargetUser}';
				}
				elseif (is_array($r))
				{
					$qty = count($r);
					if ($qty == 0)
					{
						$r = null;
					}
					elseif ($qty == 1)
					{
						$r = $r[0];
					}
				}
			}

			if (!empty($fieldValue['Required']) && ($r == null))
			{
				$arErrors[] = [
					'code' => 'emptyRequiredField',
					'message' => Loc::getMessage('BPCDA_FIELD_REQUIED', ['#FIELD#' => $fieldValue['Name']]),
				];
			}

			if ($r != null)
			{
				$arProperties['Fields'][$fieldKey] = $r;
			}
		}

		if (count($arErrors) > 0)
		{
			return false;
		}

		$arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
		$arCurrentActivity['Properties'] = $arProperties;

		return true;
	}

	protected function prepareFieldsValues(array $documentType, array $values): array
	{
		$documentService = $this->workflow->getRuntime()->getDocumentService();

		$documentFields = $documentService->getDocumentFields($documentType);
		$documentFieldsAliasesMap = CBPDocument::getDocumentFieldsAliasesMap($documentFields);

		$resultFields = [];
		foreach ($values as $key => $value)
		{
			if (!isset($documentFields[$key]) && isset($documentFieldsAliasesMap[$key]))
			{
				$key = $documentFieldsAliasesMap[$key];
			}

			$property = $documentFields[$key] ?? null;

			if ($property && $value)
			{
				$fieldTypeObject = $documentService->getFieldTypeObject($documentType, $property);
				if ($fieldTypeObject)
				{
					$fieldTypeObject->setDocumentId($this->getDocumentId());
					$fieldTypeObject->setValue($value);
					$value = $fieldTypeObject->externalizeValue(
						\Bitrix\Bizproc\FieldType::VALUE_CONTEXT_DOCUMENT,
						$fieldTypeObject->getValue()
					);
				}
			}

			if (is_null($value))
			{
				$value = '';
			}

			$resultFields[$key] = $value;
		}

		return $resultFields;
	}

	private static function increaseExecutionDepth($key)
	{
		if (!isset(self::$executionDepth[$key]))
		{
			self::$executionDepth[$key] = 0;
		}
		self::$executionDepth[$key]++;

		if (self::$executionDepth[$key] > self::EXECUTION_MAX_DEPTH)
		{
			throw new Exception(Loc::getMessage('BPCDA_RECURSION_ERROR_1'));
		}
	}

	private static function resetExecutionDepth($key)
	{
		self::$executionDepth[$key] = 0;
	}
}
