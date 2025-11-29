<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

?>

<tr>
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('GCD_RESPONSIBLE') ?>:</span>
    </td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField(
            'user',
            'Responsible',
            $arCurrentValues['Responsible'])
        ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class=""><?= Loc::getMessage('GCD_INN') ?>:</span>
    </td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField(
            'string',
            'INN',
            $arCurrentValues['INN'])
        ?>
    </td>
</tr>