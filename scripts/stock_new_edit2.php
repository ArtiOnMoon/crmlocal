<?php
require_once '../functions/db.php';
require_once '../functions/stock_fns.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security();
if(check_access('acl_stock', 2)) exit('Access denied.');
if ($_POST['item_list']=='')exit ('Error, 0 id received.');
($_POST['stock_so_comp']=='')? $stock_so_comp='NULL' : $stock_so_comp='"'.clean($_POST['stock_so_comp']).'"';
($_POST['stock_po_comp']=='')? $stock_po_comp='NULL' : $stock_po_comp='"'.clean($_POST['stock_po_comp']).'"';
if (clean($_POST['stock_date_receipt'])==='') $date='NULL'; else $date='"'.clean($_POST['stock_date_receipt']).'"';
if (clean($_POST['stock_sale_date'])==='') $stock_sale_date='NULL'; else $stock_sale_date='"'.clean($_POST['stock_sale_date']).'"';
if (clean($_POST['stock_officialy_sold'])==='') $stock_officialy_sold=0; else $stock_officialy_sold=1;

$flag=0;
$query='UPDATE stock_new SET ';
if (isset($_POST['input_status'])){$query.='stock_status="'.clean($_POST['stat']).'",';$flag=1;}
if (isset($_POST['input_supplier'])){$query.='stock_supplier="'.clean($_POST['suppl']).'",';$flag=1;}
if (isset($_POST['input_stock'])){$query.='stock_stock_id="'.clean($_POST['stock']).'",';$flag=1;}
if (isset($_POST['input_place'])){$query.='stock_place="'.clean($_POST['place']).'",';$flag=1;}
if (isset($_POST['input_date_receipt'])){$query.='stock_date_receipt = '.$date.',';$flag=1;}
if (isset($_POST['input_date_sale'])){$query.='stock_sale_date = '.$stock_sale_date.',';$flag=1;}
if (isset($_POST['input_note'])){$query.='stock_note="'.clean($_POST['stock_note']).'",';$flag=1;}
if (isset($_POST['input_condition'])){$query.='stock_condition="'.clean($_POST['cond']).'",';$flag=1;}
if (isset($_POST['input_po'])){$query.='stock_po_type="'.clean($_POST['stock_po_type']).'", stock_po_comp='.$stock_po_comp.', stock_po="'.clean($_POST['stock_po']).'",';$flag=1;}
if (isset($_POST['input_so'])){
    $query.='stock_so_type = "'.clean($_POST['stock_so_type']).'", stock_so_comp='.$stock_so_comp.', stock_so="'.clean($_POST['stock_so']).'",';
    $flag=1;
}
if (isset($_POST['input_complect'])){$query.='stock_compl_id="'.clean($_POST['stock_compl_id']).'",';$flag=1;}
if (isset($_POST['input_stock_ccd'])){$query.='stock_ccd="'.clean($_POST['stock_ccd']).'",';$flag=1;}
if (isset($_POST['input_stock_currency'])){$query.='stock_currency="'.clean($_POST['stock_currency']).'",';$flag=1;}
if (isset($_POST['input_stock_price'])){$query.='stock_price="'.clean($_POST['stock_price']).'",';$flag=1;}
if (isset($_POST['input_stock_freight'])){$query.='stock_freight="'.clean($_POST['stock_freight']).'",';$flag=1;}
if (isset($_POST['input_stock_sold'])){$query.='stock_officialy_sold="'.$stock_officialy_sold.'",';$flag=1;}
$query=substr($query,0,-1);
$query.=' WHERE stock_id IN ('.clean($_POST['item_list']).')';
$db=db_connect();
if ($flag===1){
    if(!$result=$db->query($query)) {
        echo $db->error; 
        echo $query;
        exit();
    }
}

//TRANSFERS
if (isset($_POST['transfers_flag'])){
    $list= explode(',', clean($_POST['item_list']));
    foreach ($list as $value) {
        $query= 'INSERT INTO stock_transfer SET '
        . 'stock_id = "'.$value.'", '
        . 'from_stock = "'.clean($_POST['from_stock']).'", '
        . 'to_stock = "'.clean($_POST['to_stock']).'", '
        . 'ship_date = "'.clean($_POST['ship_date']).'", '
        . 'receipt_date = "'.clean($_POST['receipt_date']).'", '
        . 'awb = "'.clean($_POST['awb']).'", '
        . 'shipped_on = "'.clean($_POST['shipped_on']).'", '
        . 'note = "'.clean($_POST['note']).'"';
        if (!$db->query($query)){
            echo '<font color="red">Problem: </font>'.$db->error;
            $db->close();
        }
    }
}

echo 'true';
$db->close();