<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

?>

<?php $this->AddEditAction('add_element', $arResult['ADD_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_ADD"));?>
<div id="<?=$this->GetEditAreaId('add_element');?>" class="questions" style="margin-top:<?=$arResult['MARGIN_TOP'];?>;margin-bottom:<?=$arResult['MARGIN_BOTTOM'];?>">
    <?php foreach ($arResult['QUESTIONS'] as $key => $question):?>
        <?php
        $this->AddEditAction($key, $question['EDIT_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($key, $question['DELETE_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => 'Вы действительно хотите удалить элемент?'));
        ?>
        <div id="<?=$this->GetEditAreaId($key);?>" class="questions_question" style="background:<?=$arResult['COLOR'];?>;border:1px solid <?=$arResult['TEXT_COLOR'];?>">
            <div style="color:<?=$arResult['TEXT_COLOR'];?>;"><?=$question['NAME']?></div>
            <div><?=$question['ANSWER']?></div>
        </div>
    <?php endforeach;?>
</div>
