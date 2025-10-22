<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";

use UDTTest\TaskOne\ReadJson;
use UDTTest\TaskTwo\ReadCsv;
use UDTTest\TaskThree\Example;
use UDTTest\TaskFour\ReadAndFilterJson;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;


session_start();

$rand = rand(0, 100000000);

if (!isset($_SESSION['check'])) {
    $_SESSION['check'] = 0;
}

/**
 * @global  \CMain $APPLICATION
 */

$APPLICATION->SetTitle(Loc::getMessage('TITLE'));

Asset::getInstance()->addCss("/local/UDTTest/style.css");
Asset::getInstance()->addJs("/local/UDTTest/script.js");

CJSCore::Init(array("jquery3"));

Loc::loadMessages(__FILE__);

$readJson = new ReadJson();
$users = $readJson->getUsers();
$deals = $readJson->getClosedDeals();
?>
<h2><?=Loc::getMessage('TASK_ONE')?></h2>
<h3><?=Loc::getMessage('GET_USERS')?></h3>
<?php if ($users):?>
    <table>
        <thead>
            <tr>
                <td><?=Loc::getMessage('USER_ID')?></td>
                <td><?=Loc::getMessage('USER_NAME')?></td>
                <td><?=Loc::getMessage('USER_EMAIL')?></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user):?>
                <tr>
                    <td><?=$user['ID']?></td>
                    <td><?=$user['NAME']?></td>
                    <td><?=$user['EMAIL']?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php else:?>
    <div><?=Loc::getMessage('USERS_ARE_NOT_EXIST')?></div>
<?php endif;?>
<h3><?=Loc::getMessage('GET_CLOSED_DEALS')?></h3>
<?php if ($deals):?>
    <table>
        <thead>
        <tr>
            <td><?=Loc::getMessage('DEAL_ID')?></td>
            <td><?=Loc::getMessage('DEAL_TITLE')?></td>
            <td><?=Loc::getMessage('DEAL_STATUS')?></td>
            <td><?=Loc::getMessage('DEAL_AMOUNT')?></td>
        </tr>
        </thead>
        <tbody>
        <?php foreach($deals as $deal):?>
            <tr>
                <td><?=$deal['ID']?></td>
                <td><?=$deal['TITLE']?></td>
                <td><?=$deal['STATUS']?></td>
                <td><?=$deal['AMOUNT']?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php else:?>
    <div><?=Loc::getMessage('CLOSED_DEALS_ARE_NOT_EXIST')?></div>
<?php endif;?>

<h2><?=Loc::getMessage('TASK_TWO')?></h2>
<h3><?=Loc::getMessage('CREATE_TABLE')?></h3>
<?php
$readCsv = new ReadCsv();

$createTable = $readCsv->createTableProducts();
if ($createTable) {
    echo Loc::getMessage('TABLE_CREATED') . "<br>";
} else {
    echo Loc::getMessage('TABLE_EXISTS') . "<br>";
}

$fullTable = $readCsv->fullProductsWithDataFromCsv();
if ($fullTable) {
    echo Loc::getMessage('TABLE_IS_FULL') . "<br>";
} else {
    echo Loc::getMessage('TABLE_FULL') . "<br>";
}

?>

<form action="" method="POST">
    <input type="hidden" name="check" value="<?=$rand;?>">
    <input type="text" name="name" placeholder="Введите название товара" value=""><br>
    <input type="text" name="art" placeholder="Введите артикул товара" value=""><br>
    <input type="text" name="price" placeholder="Введите цену товара" value=""><br>
    <input type="text" name="quantity" placeholder="Введите количество товара" value=""><br>
    <button type="submit" name="add">Добавить товар</button><button type="submit" name="change">Изменить товар</button>
</form>

<?php
$name = isset($_POST["name"]) ? $_POST["name"] : '';
$art = isset($_POST["art"]) ? $_POST["art"] : '';
$price = isset($_POST["price"]) ? $_POST["price"] : 0;
$quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : 0;
$add = isset($_POST['add']) ?? false;
$change = isset($_POST['change']) ?? false;

$arFields = [
    'name' => $name,
    'art' => $art,
    'price' => $price,
    'quantity' => $quantity
];

$messages = [];
if ($name && $art && $price && $quantity && $add && $_POST['check'] != $_SESSION['check']) {
    $_SESSION['check'] = $_POST['check'];

    $insertIntoProducts = $readCsv->insertOneIntoProducts($arFields);

    if ($insertIntoProducts) {
        $messages['RECORD_ADDED'] = Loc::getMessage('RECORD_ADDED');
    } else {
        $messages['RECORD_EXISTS'] = Loc::getMessage('RECORD_EXISTS');
    }
} else {
    $messages['FULL_REQUIRED_FIELDS'] = Loc::getMessage('FULL_REQUIRED_FIELDS');
}

if ($name && $art && $price && $quantity && $change && $_POST['check'] != $_SESSION['check']) {
    $_SESSION['check'] = $_POST['check'];
    
    $updateIntoProducts = $readCsv->updateOneIntoProducts($arFields);

    if ($updateIntoProducts) {
        $messages['RECORD_CHANGED'] = Loc::getMessage('RECORD_CHANGED');
    } else {
        $messages['RECORD_CHANGED_ERROR'] = Loc::getMessage('RECORD_CHANGED_ERROR') . "<br>";
    }
} else {
    $messages['FULL_REQUIRED_FIELDS'] = Loc::getMessage('FULL_REQUIRED_FIELDS') . "<br>";
}

foreach ($messages as $message) {
    if ($message) {
        echo $message . "<br>";
    }
}

$products = $readCsv->getAllProducts();
?>

<?php if ($products):?>
    <table>
        <thead>
        <tr>
            <td><?=Loc::getMessage('PRODUCT_NAME')?></td>
            <td><?=Loc::getMessage('PRODUCT_ART')?></td>
            <td><?=Loc::getMessage('PRODUCT_PRICE')?></td>
            <td><?=Loc::getMessage('PRODUCT_QUANTITY')?></td>
        </tr>
        </thead>
        <tbody>
        <?php foreach($products as $key => $product):?>
            <?php if ($key != 0):?>
                <tr>
                    <td><?=$product['name']?></td>
                    <td><?=$product['art']?></td>
                    <td><?=$product['price']?></td>
                    <td><?=$product['quantity']?></td>
                </tr>
            <?php endif;?>
        <?php endforeach;?>
        </tbody>
    </table>
<?php else:?>
    <div><?=Loc::getMessage('PRODUCTS_ARE_NOT_EXIST')?></div>
<?php endif;?>

<?php
$products = new Example();
$arProd = $products->findAll();
?>
<h2><?=Loc::getMessage('TASK_THREE')?></h2>
<h3><?=Loc::getMessage('GET_PRODUCTS_NAME')?></h3>
<?php if ($arProd):?>
    <table>
        <thead>
        <tr>
            <td><?=Loc::getMessage('PRODUCT_NAME')?></td>
        </tr>
        </thead>
        <tbody>
        <?php foreach($arProd as $prod):?>
            <tr>
                <td><?=$prod?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php else:?>
    <div><?=Loc::getMessage('PRODUCTS_ARE_NOT_EXIST')?></div>
<?php endif;?>

<?php
    $readJson = new ReadAndFilterJson();
    $deals = $readJson->readFromJson();
    $statuses = $readJson->getStatuses();
?>
<h2><?=Loc::getMessage('TASK_FOUR')?></h2>
<h3><?=Loc::getMessage('GET_DEALS')?></h3>
<?php if ($deals):?>
    <div>
        <div>Выберите статус</div>
        <?php $i = 0; foreach($statuses as $status):?>
            <input type="checkbox" id="status[<?=$i?>]" name="status" value="<?=$status?>">
            <label for="status[<?=$i?>]"><?=$status?></label><br>
        <?php $i ++; endforeach;?>
    </div>
    <table class="deals-with-filter">
        <thead>
        <tr>
            <td><?=Loc::getMessage('DEAL_ID')?></td>
            <td><?=Loc::getMessage('DEAL_TITLE')?></td>
            <td><?=Loc::getMessage('DEAL_STATUS')?></td>
            <td><?=Loc::getMessage('DEAL_AMOUNT')?></td>
        </tr>
        </thead>
        <tbody>
        <?php foreach($deals as $deal):?>
            <tr class="<?=$deal['status']?>">
                <td><?=$deal['id']?></td>
                <td><?=$deal['title']?></td>
                <td><?=$deal['status']?></td>
                <td><?=$deal['amount']?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php else:?>
    <div><?=Loc::getMessage('DEALS_ARE_NOT_EXIST')?></div>
<?php endif;?>

<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
