<?php
require_once '../../functions/db.php';
require_once '../../functions/main.php';
require_once '../../classes/Order_name_engine.php';

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);

$comp_list = $on->comp_list;

//PO

$query1='SELECT stock_id, stock_po_type, stock_po_comp, stock_po FROM stock_new';
$result = $db->query($query1);

while ($row = $result->fetch_assoc()){
    if ($row['stock_po']==''){
        $stock_po='';
    }
    else if($row['stock_po_comp']=='' OR strlen($row['stock_po'])>5){
        $stock_po = $row['stock_po'];
    }
    else {
        $stock_po = $row['stock_po_type'].'-'.$comp_list[$row['stock_po_comp']].'-'.numberFormat($row['stock_po'],5);
    }
    $query = 'UPDATE stock_new '
            . 'SET stock_po=\''.$stock_po.'\''
            . 'WHERE stock_id='.$row['stock_id'];
    $db->query($query);
}

//SO

$query2='SELECT stock_id, stock_so_type, stock_so_comp, stock_so FROM stock_new';
$result2 = $db->query($query2);

while ($row = $result2->fetch_assoc()){
    if ($row['stock_so']==''){
        $stock_so='';
    }
    else if($row['stock_so_comp']=='' OR strlen($row['stock_so'])>5){
        $stock_so = $row['stock_so'];
    }
    else {
        $stock_so = $row['stock_so_type'].'-'.$comp_list[$row['stock_so_comp']].'-'.numberFormat($row['stock_so'],5);
    }
    $query = 'UPDATE stock_new '
            . 'SET stock_so=\''.$stock_so.'\''
            . 'WHERE stock_id='.$row['stock_id'];
    $db->query($query);
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