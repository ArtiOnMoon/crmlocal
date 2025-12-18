<?php
require_once 'functions/fns.php';
startSession();
security();
if(check_access('acl_sales', 2)) exit('Access denied.');
if (isset($_POST['price_deleted'])) $deleted=1; else $deleted=0;
$db =  db_connect();
$query= 'update price set '
        . 'class = "'.clean($_POST['stock_class']).'", '
        . 'pn = "'.clean($_POST['pn']).'", '
        . 'description = "'.clean($_POST['description']).'", '
        . 'currency = "'.clean($_POST['currency']).'", '
        . 'price = "'.clean($_POST['price']).'", '
        . 'price_discount = "'.clean($_POST['discount']).'", '
        . 'date = "'.clean($_POST['date']).'", '
        . 'manufacturer_id = "'.clean($_POST['new_customer']).'", '
        . 'user = "'.$_SESSION['valid_user'].'", '
        . 'price_deleted = "'.$deleted.'" '
        . 'WHERE id = "'.clean($_POST['id']).'"';
$result=$db->query($query);
if (!$result) echo '<font color="red">Problem: </font>'.$db->error; else {
    header('Location: /price.php');
}