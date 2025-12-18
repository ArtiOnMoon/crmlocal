<?php
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/main.php';
startSession();
security ();
$db =  db_connect();
$query= 'INSERT INTO stock_transfer SET '
        . 'stock_id = "'.clean($_POST['stock_id']).'", '
        . 'from_stock = "'.clean($_POST['from_stock']).'", '
        . 'to_stock = "'.clean($_POST['to_stock']).'", '
        . 'ship_date = "'.clean($_POST['ship_date']).'", '
        . 'receipt_date = "'.clean($_POST['receipt_date']).'", '
        . 'awb = "'.clean($_POST['awb']).'", '
        . 'shipped_on = "'.clean($_POST['shipped_on']).'", '
        . 'note = "'.clean($_POST['note']).'"';
if ($db->query($query)){
    $db->close();
    exit ('true');
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
    $db->close();
}