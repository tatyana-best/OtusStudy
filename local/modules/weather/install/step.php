<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()) {
    return;
}

if ($errorException = $APPLICATION->getException()) {    
    CAdminMessage::showMessage(
        Loc::getMessage('WEATHER_INSTALL_TITLE').': '.$errorException->GetString()
    );
} else {    
    CAdminMessage::showNote(
        Loc::getMessage('WEATHER_INSTALL_SUCCESS')
    );
}
?>

<form action="<?= $APPLICATION->getCurPage(); ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>" />
    <input type="submit" value="<?= Loc::getMessage('WEATHER_RETURN_MODULES'); ?>">
</form>