<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetTitle('Отладка и логирование. Домашнее задание.');

?>

<div>
    <h2>Задание 1</h2>
    <ul>
        <li><a href="/local/Otus/Debug.php" target="_blank">Запустить файл /local/otus/debug.php и сделать запись текущей даты в лог</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2FOtus%2FDebug.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Открыть файл /otus/debug.php в админке</a></li>
        <li><a href="/log.txt" target="_blank">Посмотреть файл с логами /log.txt</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flog.txt&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Открыть файл с логами в админке /log.txt</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Fbitrix%2Fphp_interface%2Fdbconn.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Файл dbconn.php в админке</a></li>
    </ul>
</div>
<div>
    <h2>Задание 2</h2>
    <ul>
        <li><a href="/local/examples.php" target="_blank">Запустить файл /local/examples.php, чтобы вызвать ошибку</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2Fexamples.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Открыть файл /local/examples.php в админке</a></li>
        <li><a href="/local/logs/debug.txt" target="_blank">Посмотреть файл /local/logs/debug.txt с логами ошибок</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2Flogs%2Fdebug.txt&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Открыть файл /local/logs/debug.txt с логами в админке</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Fbitrix%2F.settings_extra.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Файл .settings_extra.php в админке</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2Fphp_interface%2Fclasses%2FOtus%2FDebug%2FFileExceptionHandlerLogCustom.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Файл с классом /local/php_interface/classes/Otus/Debug/FileExceptionHandlerLogCustom.php в админке</a></li>
        <li><a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2Fphp_interface%2Finit.php&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y" target="_blank">Файл /local/php_inteface/init.php в админке</a></li>
        <li><a href="/bitrix/admin/fileman_admin.php?PAGEN_1=1&SIZEN_1=20&lang=ru&site=s1&path=%2Flocal%2Fphp_interface%2Fclasses%2FMyDebug&show_perms_for=0&fu_action=" target="_blank">Папка /local/php_interface/classes/MyDebug/ с моими классами в админке (создала для проверки работы автозагрузчика)</a></li>
    </ul>
</div>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";