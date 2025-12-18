<?php
require_once '../../functions/db.php';
require_once '../../functions/main.php';
require_once '../../classes/Order_name_engine.php';

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);

$comp_list = $on->comp_list;

//PO

$query1='SELECT * FROM invoices WHERE 1';
$result = $db->query($query1);

while ($row = $result->fetch_assoc()){
    if ($row['invoice_order_num']=='' OR strlen($row['invoice_order_num'])>5){
        $invoice='';
    }
    else if($row['invoice_order_comp']=='' OR strlen($row['invoice_order_comp'])>5){
        $invoice = '';
    }
    else if($row['invoice_order_type']=='' OR $row['invoice_order_type']=='0'){
        $invoice ='';
    }
    else {
        $invoice = $row['invoice_order_type'].'-'.$comp_list[$row['invoice_order_comp']].'-'.numberFormat($row['invoice_order_num'],5);
    }
    $query = 'UPDATE invoices '
            . 'SET invoice_order_num=\''.$invoice.'\' '
            . 'WHERE invoice_id='.$row['invoice_id'];
    $db->query($query);
//    echo $query.'<br>';
}

echo 'Success';

//TEST
//for ($i=0;$i<1000;$i++){
//    $row = $result2->fetch_assoc();
//    if ($row['stock_so']==''){
//        $stock_so='';
//    }
//    else if($row['stock_so_comp']=='' OR strlen($row['stock_so'])>5){
//        $stock_so = $row['stock_so'];
//    }
//    else {
//        $stock_so = $row['stock_so_type'].'-'.$comp_list[$row['stock_so_comp']].'-'.numberFormat($row['stock_so'],5);
//    }
//    $query = 'UPDATE stock_new '
//            . 'SET stock_so=\''.$stock_so.'\''
//            . 'WHERE stock_id='.$row['stock_id'];
//    echo $query.'<br>';
//}