<?php
require_once 'functions/fns.php';
startSession();
security ();
$db =  db_connect();
if(check_access('acl_purchase', 2)) exit('Access denied.');
//Check dates
if ($_POST['invoice_date']!=='') $invoice_date='"'.clean($_POST['invoice_date']).'"'; else $invoice_date='NULL';
if ($_POST['shipment_date']!=='') $shipment_date='"'.clean($_POST['shipment_date']).'"'; else $shipment_date='NULL';
if ($_POST['content']!=='NULL') {
    //$content='\''.$_POST['content'].'\'';
    $content= mysqli_real_escape_string($db,$_POST['content']);
}
else $content='NULL';
$query= 'update purchase set '
        . 'status = "'.clean($_POST['purchase_status']).'", ' 
        . 'AWB="'.clean($_POST['new_AWB']).'", '
        . 'customer="'.clean($_POST['new_customer']).'", '
        . 'po="'.clean($_POST['po']).'", '
        . 'sales_order="'.clean($_POST['sales_order']).'", '
        . 'po_date="'.clean($_POST['po_date']).'", '
        . 'shipment_date='.$shipment_date.', '
        . 'shipper="'.clean($_POST['shipper']).'", '
        . 'order_ackn="'.clean($_POST['order_ackn']).'", '
        . 'our_company="'.clean($_POST['our_company']).'", '
        . 'currency="'.$_POST['currency'].'", '
        . 'content=\''.$content.'\', '
        . 'note="'.clean($_POST['note']).'", '
        . 'delivery_addr="'.clean($_POST['delivery_addr']).'", '
        . 'invoice_date='.$invoice_date.', '
        . 'modified="'.$_SESSION['valid_user'].'", '
        . 'invoice="'.clean($_POST['invoice']).'" '
        . 'where id="'.clean($_POST['id']).'"';
$result=$db->query($query);
if (!$result) echo '<p>FAILED</p>'.$db->error; else {
    header('Location: /purchase.php');
}

do_page_end();