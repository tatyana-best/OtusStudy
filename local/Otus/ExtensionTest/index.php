<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\UI\Extension;

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Расширение для демонстрации');

Extension::load(['otus.greetingmessage']);
Extension::load('otus.fruits');

?>

<div id="fruits"></div>

<script>
    BX.ready(() => {
        let m = new BX.Otus.Greetingmessage();
        m.helloWorld();

        const container = document.getElementById('fruits');
        const fruits = [
            {
                name: 'apple',
                img: '/fruit.png',
            },
            {
                name: 'one more apple',
                img: '/fruit.png',
            },
            {
                name: 'some else apple',
                img: '/fruit.png',
            }
        ];
        new BX.Otus.Fruits({
            container,
            fruits
        });
    });
</script>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
