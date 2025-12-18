<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security ();

if (!access_check([],[],2)) exit ('Access denied');
$id=clean($_POST['id']);
$db =  db_connect();
$query= 'insert into invoices_out set '
        . 'customer="'.clean($_POST['new_customer']).'", '
        . 'po="'.clean($_POST['po']).'", '
        . 'currency="'.clean($_POST['currency']).'", '
        . 'our_comp="'.clean($_POST['our_company']).'", '
        . 'status="'.clean($_POST['invoice_status']).'", '
        . 'order_id="'.clean($_POST['order_id']).'", '
        . 'inv_type="'.clean($_POST['cn_type']).'", '
        . 'inv_note="'.clean($_POST['inv_note']).'", '
        . 'invoice_date="'.clean($_POST['invoice_date']).'", '
        . 'invoice_total="'.(double)clean($_POST['invoice_total']).'", '
        . 'pay_terms="'.clean($_POST['pay_terms']).'"';
if ($_POST['rates']!=='NULL') $query.=', rates=\''.mysqli_real_escape_string($db,$_POST['rates']).'\'';
else $query.=', rates=NULL';
if ($_POST['spare']!=='NULL') $query.=', spares=\''.mysqli_real_escape_string($db,$_POST['spare']).'\'';
else $query.=', spares=NULL';
if (isset($_POST['invoice_num']))$query.=', invoice_num="'.clean($_POST['invoice_num']).'"';
else $query.=', invoice_num="'.$id.'"';

if ($db->query($query)){
    header('Location: /invoice_out_view.php?saved=1&invoice_id='.$id);
}
else{
    echo('<font color="red">Problem: </font>'.$db->error);
    exit();
}