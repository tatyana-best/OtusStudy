<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";

use Otus\Model\Lists\CustomTablesList;

/**
 * @global  \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Примеры кастомных моделей собственных таблиц и инфоблоков. Выборка данных из них');

?>

<h2>Логическая модель БД 1</h2>
<ul>
    <li><a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=18&type=hospital&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y" target="_blank">Инфоблок Участки</a></li>
    <li><a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=16&type=lists&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y" target="_blank">Инфоблок Доктора</a></li>
    <li><a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=17&type=lists&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y" target="_blank">Инфоблок Процедуры</a></li>
    <li><a href="/bitrix/admin/fileman_admin.php?PAGEN_1=1&SIZEN_1=20&lang=ru&site=s1&path=%2Flocal%2Fphp_interface%2Fclasses%2FOtus%2FORM&show_perms_for=0&fu_action=" target="_blank">Таблицы ORM</a></li>
    <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2Fphp_interface%2Fclasses%2FOtus%2FModel%2FLists%2FCustomTablesList.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Класс, с помощью которого формируются результирующие массивы</a></li>
    <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2FOtus%2FCustomModels%2Findex.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Данная страница в админке</a></li>
</ul>
<img src="images/model1.jpg" style="width:100%;">

<h3>Пациенты и участки. Поле age - вычисляемое. Поле Адрес - это Улица + Квартира</h3>

<?php
pr(CustomTablesList::PatientsAndPlotsGetList());
?>

<h3>Визиты пациента по ID</h3>

<?php
pr(CustomTablesList::VisitsByIdGetList(2));
?>

<h3>Визиты всех пациентов</h3>

<?php
pr(CustomTablesList::VisitsGetList());
?>

<h3>Доктора и их процедуры</h3>

<?php
pr(CustomTablesList::doctorsProceduresList());
?>

<h3>Логическая модель БД 2</h3>
<ul>
    <li><a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=19&type=university&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y" target="_blank">Инфоблок Абитуриенты</a></li>
    <li><a href="/bitrix/admin/fileman_admin.php?PAGEN_1=1&SIZEN_1=20&lang=ru&site=s1&path=%2Flocal%2Fphp_interface%2Fclasses%2FOtus%2FORM&show_perms_for=0&fu_action=" target="_blank">Таблицы ORM</a></li>
    <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2Fphp_interface%2Fclasses%2FOtus%2FModel%2FLists%2FCustomTablesList.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Класс, с помощью которого формируются результирующие массивы</a></li>
    <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2FOtus%2FCustomModels%2Findex.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Данная страница в админке</a></li>
</ul>
<img src="images/model2.jpg" style="width:60%;">
<h3>Абитуриент по ID и предметы, которые он сдавал</h3>

<?php
pr(CustomTablesList::applicantsByIDList(91));
?>

<h3>Все абитуриенты и их предметы с помощью fetchCollection()</h3>

<?php
pr(CustomTablesList::applicantsList());
?>

<h3>Все абитуриенты и их предметы с помощью fetchAll()</h3>
<h3>Вопросы преподавателю:</h3>
<ul>
    <li>Я создала таблицу связи APPSUB с помощью обычного create table... Как можно было правильно это сделать?</li>
    <li>Как правильно можно создать в таблице связи дополнительные поля (у меня это поле RANG)?</li>
    <li>Если делать как я, то нужно ли для промежуточной таблицы (APPSUB) описывать ORM модель</li>
    <li>Как вывести дополнительные поля из таблицы связи APPSUB? Можно ли это сделать в одном запросе, например, в таком, какой я использовала в методе applicantsListByFetchAll()?</li>
    <li>Можно ли как-то запросом ORM, а не php создать вычисляемое поле, в котором рассчитывалась бы сумма баллов для каждого абитуриента?</li>
</ul>
<?php
pr(CustomTablesList::applicantsListByFetchAll());

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
