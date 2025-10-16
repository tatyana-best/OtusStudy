<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

/**
 * @var CMain $APPLICATION
 */

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
Loader::includeModule($module_id);

$aTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('QUESTIONS_OPTIONS_TAB_GENERAL'),
        'TITLE' => Loc::getMessage('QUESTIONS_OPTIONS_TAB_GENERAL'),
        'OPTIONS' => [
            [
                'switch_on',
                Loc::getMessage('QUESTIONS_OPTIONS_SWITCH_ON'),
                'Y',
                ['checkbox']
            ],
        ]
    ],
    [
        'DIV' => 'edit2',
        'TAB' => Loc::getMessage('QUESTIONS_OPTIONS_TAB_ADDITIONAL'),
        'TITLE' => Loc::getMessage('QUESTIONS_OPTIONS_TAB_ADDITIONAL'),
        'OPTIONS' => [
            Loc::getMessage('QUESTIONS_OPTIONS_SECTION_VIEW'),
            [
                'text_color',
                Loc::getMessage('QUESTIONS_OPTIONS_TEXT_COLOR'),
                '#bf3030',
                ['text', 5]
            ],
            [
                'color',
                Loc::getMessage('QUESTIONS_OPTIONS_COLOR'),
                '#ADFF2F',
                ['text', 5]
            ],
            [
                'margin_top',
                Loc::getMessage('QUESTIONS_MARGIN_TOP'),
                '30px',
                ['text', 8]
            ],
            [
                'margin_bottom',
                Loc::getMessage('QUESTIONS_MARGIN_BOTTOM'),
                '30px',
                ['text', 8]
            ],
            Loc::getMessage('QUESTIONS_OPTIONS_SECTION_GROPS'),
            [
                'groups',
                Loc::getMessage('QUESTIONS_OPTIONS_GROUPS'),
                '1,2',
                ['text', 10]
            ],
        ]
    ]
];

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->begin();
?>

    <form action="<?=$APPLICATION->getCurPage(); ?>?mid=<?=$module_id; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
        <?= bitrix_sessid_post(); ?>
        <?php
        foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->beginNextTab();
                __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
            }
        }
        $tabControl->buttons();
        ?>
        <input type="submit" name="apply"
               value="<?= Loc::GetMessage('QUESTIONS_OPTIONS_INPUT_APPLY'); ?>" class="adm-btn-save" />
        <input type="submit" name="default"
               value="<?= Loc::GetMessage('QUESTIONS_OPTIONS_INPUT_DEFAULT'); ?>" />
    </form>

<?php
$tabControl->end();


if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) {
                continue;
            }
            if ($arOption['note']) {
                continue;
            }
            if ($request['apply']) {
                $optionValue = $request->getPost($arOption[0]);
                if ($arOption[0] == 'switch_on') {
                    if ($optionValue == '') {
                        $optionValue = 'N';
                    }
                }

                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
            } elseif ($request['default']) {
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->getCurPage().'?mid='.$module_id.'&lang='.LANGUAGE_ID);
}
