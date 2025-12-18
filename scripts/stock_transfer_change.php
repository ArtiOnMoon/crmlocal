<?php
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/main.php';
startSession();
security ();
if (isset($_POST['is_deleted']))$is_deleted=1; ELSE $is_deleted=0;
if ($_POST['ship_date']=='')$ship_date="NULL";else $ship_date='"'.$_POST['ship_date'].'"';
if ($_POST['receipt_date']=='')$receipt_date="NULL";else $receipt_date='"'.$_POST['receipt_date'].'"';
$db =  db_connect();
$query= 'UPDATE stock_transfer SET '
        . 'from_stock = "'.clean($_POST['from_stock']).'", '
        . 'to_stock = "'.clean($_POST['to_stock']).'", '
        . 'ship_date = '.$ship_date.', '
        . 'receipt_date = '.$receipt_date.', '
        . 'awb = "'.clean($_POST['awb']).'", '
        . 'shipped_on = "'.clean($_POST['shipped_on']).'", '
        . 'note = "'.clean($_POST['note']).'", '
        . 'is_deleted = "'.$is_deleted.'" '
        . 'WHERE transfer_id= "'.clean($_POST['transfer_id']).'"';
if ($db->query($query)){
    $db->close();
    exit ('true');
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
    $db->close();
}