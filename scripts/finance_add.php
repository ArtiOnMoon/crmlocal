<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
session_start();
if(check_access('acl_invoices', 2)) exit('Access denied.');
if(isset($_POST['auto_number']))$number='';
else $number=clean($_POST['fin_number']);

if ($_POST['fin_type']=='1'){
    $fin_pay_out='NULL';
    $fin_pay_in='"'.clean($_POST['fin_pay_in']).'"';
}
else {
    $fin_pay_out='"'.clean($_POST['fin_pay_out']).'"';
    $fin_pay_in='NULL';
}
if($_POST['fin_paid']=='' || !is_numeric($_POST['fin_paid']))$fin_paid=0;
else $fin_paid=clean($_POST['fin_paid']);

$db =  db_connect();
$query= 'INSERT INTO finance SET '
        . 'fin_number="'.$number.'", '
        . 'fin_type="'.clean($_POST['fin_type']).'", '
        . 'fin_our_comp="'.clean($_POST['our_comp']).'", '
        . 'fin_customer="'.clean($_POST['fin_customer']).'", '
        . 'fin_date="'.clean($_POST['fin_date']).'", '
        . 'fin_currency="'.clean($_POST['fin_currency']).'", '
        . 'fin_pay_in='.$fin_pay_in.', '
        . 'fin_pay_out='.$fin_pay_out.', '
        . 'fin_paid="'.$fin_paid.'", '
        . 'fin_mod="'.$_SESSION['uid'].'", '
        . 'fin_note="'.clean($_POST['fin_note']).'"';
echo $query;
if (!$db->query($query)){
    echo('<font color="red">Problem: </font>'.$db->error);
    exit();
    
}
header('Location: /invoice_out_view.php?saved=1&invoice_id='.$id);