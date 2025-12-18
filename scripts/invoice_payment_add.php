<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$invoice_id=$_POST['invoice_id'];
$db =  db_connect();
$query= 'INSERT INTO invoices_payments SET '
        . 'pay_inv_id = "'.clean($_POST['pay_inv_id']).'",'
        . 'pay_num = "'.clean($_POST['pay_num']).'",'
        . 'pay_date = "'.clean($_POST['pay_date']).'",'
        . 'pay_amount="'.clean($_POST['pay_amount']).'"';
if (!$result=$db->query($query)){
    echo 'DB error. '.$db->error;
    exit();
}
exit('true');