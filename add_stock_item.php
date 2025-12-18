<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_stock', 2)) exit('Access denied.');
if (clean($_POST['warranty'])==='') $warranty='NULL'; else $warranty='"'.clean($_POST['warranty']).'"';
if (clean($_POST['new_stock_date_rec'])==='') $date_rec='NULL'; else $date_rec='"'.clean($_POST['new_stock_date_rec']).'"';
if  (clean($_POST['is_complect'])==='0') $compl='';
elseif  (clean($_POST['is_complect'])==='1') $compl=', is_complect=1';
elseif  (clean($_POST['is_complect'])==='2') $compl=', is_complect=2, complect_id='.clean($_POST['complect_id']);

$db =  db_connect();
$query= 'insert into stock set '
        . 'class = "'.clean($_POST['stock_class']).'", '
        . 'status = "'.clean($_POST['stock_item_status']).'", '
        . 'type_or_pn = "'.clean($_POST['type_or_pn']).'", '
        . 'descr = "'.clean($_POST['new_stock_desc']).'", '
        . 'note = "'.clean($_POST['new_stock_note']).'", '
        . 'serial = "'.clean($_POST['new_stock_serial']).'", '
        . 'stock = "'.clean($_POST['stock']).'", '
        . 'manufacturer = "'.clean($_POST['new_customer']).'", '
        . 'supplier = "'.clean($_POST['supplier']).'", '
        . 'customs_dec = "'.clean($_POST['customs_dec']).'", '
        . 'cond = "'.clean($_POST['new_stock_cond']).'", '
        . 'purchase_order = "'.clean($_POST['new_purchase_order']).'", '
        . 'date_receipt = '.$date_rec.', '
        . 'net_price = "'.clean($_POST['new_stock_price']).'", '
        . 'min_price = "'.clean($_POST['min_price']).'", '
        . 'currency = "'.clean($_POST['currency']).'", '
        . 'freight = "'.clean($_POST['new_stock_freight']).'", '
        . 'warranty = '.$warranty.', '
        . 'place = "'.clean($_POST['new_stock_place']).'"';
$query.=$compl;
if (clean($_POST['on_balance'])==='1') $query.=', on_balance="1";';
if ($db->query($query)){
    header('Location: /stock.php');
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
    echo '<meta http-equiv="Refresh" content="5; url=/stock.php">';
    echo '<p>You will be redirected to previous page in 5 seconds...';   
}