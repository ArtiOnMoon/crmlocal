<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$db =  db_connect();
if(isset($_POST['pay_delete'])){
    $query= 'DELETE FROM invoices_payments WHERE pay_id="'.clean($_POST['pay_id']).'"'; 
}else $query= 'UPDATE invoices_payments SET '
        . 'pay_date = "'.clean($_POST['pay_date']).'",'
        . 'pay_num = "'.clean($_POST['pay_num']).'",'
        . 'pay_amount="'.clean($_POST['pay_amount']).'" '
        . 'WHERE pay_id="'.clean($_POST['pay_id']).'"';
if (!$result=$db->query($query)){
    echo 'DB error. '.$db->error;
    exit();
}
exit('true');