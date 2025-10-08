<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var CMain $APPLICATION
 */

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()){
    return;
}

if ($errorException = $APPLICATION->getException()) {
    CAdminMessage::showMessage(
        Loc::getMessage('ANNOUNCEMENT_UNINSTALL_FAILED').': '.$errorException->GetString()
    );
} else {
    CAdminMessage:showNote(
        Loc::getMessage('ANNOUNCEMENT_UNINSTALL_SUCCESS')
    );
}
?>

<form action="<?= $APPLICATION->getCurPage(); ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>" />
    <input type="submit" value="<?= Loc::getMessage('ANNOUNCEMENT_RETURN_MODULES'); ?>">
</form>
