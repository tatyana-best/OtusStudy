<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Наши доктора и их процедуры');

use Bitrix\Main\Diag\Debug;
use Otus\Model\AbstractIblocksModel;
use Otus\Model\Updates\Doctors;
use Otus\Model\Lists\DoctorsList;
use Otus\Model\Lists\ProceduresList;
use \Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

$code = '';
if (isset($_GET["CODE"]) && $_GET["CODE"] != '') {
    $code = $_GET["CODE"];
}

$arDoctors = DoctorsList::getTableElementList();
$elemId = DoctorsList::getElementIdByCode($code);
$arProcedures = DoctorsList::getMultyPropertyValues($elemId, 'PROCEDURE');
$arProceduresList = ProceduresList::getTableElementList();

?>

<style>
    button {
        background: none;
        border: none;
        text-decoration: none;
        color: #2067b0;
        cursor: pointer;
    }

    li {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .links {
        margin-top: 30px;
        display: flex;
        gap: 50px;
    }

    .color {
        width: 40px;
        height: 40px;
    }

    img {
        width: 40px;
    }
</style>

<?php if (!$code):?>
<h2><?=Loc::getMessage("MAIN_TITLE");?></h2>
<ul>
    <?foreach($arDoctors as $doctor):?>
        <li>
            <span><?=$doctor['ID'];?></span>
            <img src="<?=$doctor['PREVIEW_PICTURE'];?>">
            <a href="<?=$doctor['PATH'];?>">
                <?=$doctor['NAME'] . " (" . $doctor['PROFILE'] . ")"?>
            </a>
            <a href="/local/Otus/Doctors/delete_doctor?ID=<?=$doctor['ID']?>&IBLOCK_ID=16" style="color:red;font-weight:bold;">
                <?=Loc::getMessage("DOCTOR_DELETE");?>
            </a>
        </li>
    <?php endforeach;?>
</ul>
<div class="links">
    <a href="/local/Otus/Doctors/procedure_list"><?=Loc::getMessage("PROCEDURE_LIST");?></a>
    <a href="/local/Otus/Doctors/add_doctor"><?=Loc::getMessage("ADD_DOCTOR");?></a>
</div>

<?php elseif ($code == 'procedure_list'):?>
    <h2><?=Loc::getMessage("PROCEDURE_LIST_TITLE");?></h2>
    <ul>
        <?foreach($arProceduresList as $proc):?>
            <li>
                <span><?=$proc['ID'];?></span>
                <?php foreach($proc['COLOR'] as $color):?>
                    <span class="color" style="background:<?=$color;?>"></span>
                <?php endforeach;?>
                <?=$proc['NAME'] . " (" . $proc['PRICE'] . ")"?>
                <a href="/local/Otus/Doctors/delete_procedure?ID=<?=$proc['ID']?>&IBLOCK_ID=17>" style="color:red;font-weight:bold;">
                    <?=Loc::getMessage("PROCEDURE_DELETE");?>
                </a>
            </li>
        <?php endforeach;?>
    </ul>
    <div class="links">
        <a href="/local/Otus/Doctors/"><?=Loc::getMessage("BACK");?></a>
    </div>

<?php elseif ($code == 'delete_procedure'):?>
    <?php
        Doctors::deleteElement($_GET['ID'], $_GET['IBLOCK_ID']);
        header("Location: " . '/local/Otus/Doctors/procedure_list');
    ?>

<?php elseif ($code == 'delete_doctor'):?>
    <?php
        Doctors::deleteElement($_GET['ID'], $_GET['IBLOCK_ID']);
        header("Location: " . '/local/Otus/Doctors/');
    ?>

<?php elseif ($code == 'delete_procedure_doctor'):?>
    <?php
        Doctors::deleteProcedureFromDoctor($_GET['ID'], $_GET['PROP_ID']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
    ?>

<?php elseif ($code == 'add_procedure_doctor'):?>
    <h2><?=Loc::getMessage("DOCTOR_ADD_PROCEDURE_TITLE");?></h2>
    <form action="/local/Otus/Doctors/add_procedure_doctor" method="POST">
        <input type="hidden" name="DOCTOR_ID" value="<?=$_POST['DOCTOR_ID']?>">
        <input type="hidden" name="CODE" value="<?=$_POST['CODE']?>">
        <select name="PROCEDURE">
            <option selected value="0"></option>
            <?php foreach($arProceduresList as $proc):?>
            <option value="<?=$proc['ID'];?>">
                <?=$proc['NAME'];?>
            </option>
            <?php endforeach;?>
        </select><br><br>
        <button type="submit"><?=Loc::getMessage("SAVE");?></button><br><br><br>
    </form><br><br>
    <a href='/local/Otus/Doctors/<?=$_POST['CODE'];?>'><?=Loc::getMessage("DOCTOR_PROCEDURE_LIST");?></a>
    <?php if (isset($_POST["PROCEDURE"]) && $_POST["PROCEDURE"] && isset($_POST["DOCTOR_ID"]) && $_POST["DOCTOR_ID"]):?>
        <?php
        echo "<div>" . Loc::getMessage("DOCTOR_PROCEDURE_ADDED") . "</div>";
        Doctors::addProcedureToDoctor(intval($_POST['PROCEDURE']), intval($_POST['DOCTOR_ID']));
    endif;?>

<?php elseif ($code == 'add_doctor'):?>
    <h2><?=Loc::getMessage("DOCTOR_ADD_TITLE");?></h2>
    <form action="/local/Otus/Doctors/add_doctor" method="POST">
        <label for="FIO"><?=Loc::getMessage("ENTER_FIO");?>
            <input type="text" name="FIO" id="FIO" value="">
        </label><br><br>
        <label for="PROFILE"><?=Loc::getMessage("ENTER_PROFILE");?>
            <input type="text" name="PROFILE" id="PROFILE" value="">
        </label><br><br>
        <button type="submit"><?=Loc::getMessage("SAVE");?></button><br><br><br>
    </form>
    <a href='/local/Otus/Doctors/'><?=Loc::getMessage("BACK");?></a>
    <?php if (isset($_POST["FIO"]) && $_POST["FIO"] && isset($_POST["PROFILE"]) && $_POST["PROFILE"]):?>
    <?php
        echo "<div>" . Loc::getMessage("DOCTOR_ADDED") . "</div>";
        Doctors::addDoctor($_POST['FIO'], $_POST['PROFILE']);
    endif;?>

<?php else:?>
    <?php $arDoctorName = DoctorsList::getElementNameByCode($code);?>
    <h2><?=Loc::getMessage("PROCEDURE_TITLE");?> <?=$arDoctorName;?></h2>
    <ul>
        <?php foreach($arProcedures as $procedure):?>
            <li>
                <?php foreach($procedure['COLOR'] as $color):?>
                    <span class="color" style="background:<?=$color;?>"></span>
                <?php endforeach;?>
                <span><?=$procedure['NAME'] . " (" . $procedure['PRICE'] . ")"?></span>
                <a href="/local/Otus/Doctors/delete_procedure_doctor?ID=<?=$elemId;?>&PROP_ID=<?=$procedure['ID'];?>" style="color:red;font-weight:bold;">
                    <?=Loc::getMessage("PROCEDURE_DOCTOR_DELETE");?>
                </a>
            </li>
        <?php endforeach;?>
    </ul>
    <div class="links">
        <a href="/local/Otus/Doctors/"><?=Loc::getMessage("BACK");?></a>
        <form action="/local/Otus/Doctors/add_procedure_doctor" method="POST">
            <input type="hidden" name="DOCTOR_ID" value="<?=$elemId;?>">
            <input type="hidden" name="CODE" value="<?=$code;?>">
            <button type="submit"><?=Loc::getMessage("ADD_PROCEDURE_DOCTOR");?></button>
        </form>
    </div>
<?php endif;?>

<?
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
