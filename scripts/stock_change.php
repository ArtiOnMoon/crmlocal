<?php
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/main.php';
startSession();
security();

if(check_access('acl_stock', 2)) exit('Access denied.');
$del=(!$_POST['stock_deleted'])?0:1;
if (clean($_POST['stock_warranty_to'])==='') $warranty='NULL'; else $warranty='"'.clean($_POST['stock_warranty_to']).'"';
if (clean($_POST['stock_date_receipt'])==='') $date='NULL'; else $date='"'.clean($_POST['stock_date_receipt']).'"';
if (clean($_POST['stock_sale_date'])==='') $stock_sale_date='NULL'; else $stock_sale_date='"'.clean($_POST['stock_sale_date']).'"';
if (($stock_sale_date==='NULL') and ($_POST['stock_item_status']==='3'))$stock_sale_date='"'.date('Y-m-d').'"';
if (clean($_POST['stock_compl_id'])==='') $stock_compl_id='NULL'; else $stock_compl_id='"'.clean($_POST['stock_compl_id']).'"';
if (clean($_POST['stock_price'])==='') $price='0'; else $price='"'.clean($_POST['stock_price']).'"';
if (clean($_POST['stock_officialy_sold'])==='') $stock_officialy_sold=0; else $stock_officialy_sold=1;
if (clean($_POST['stock_freight'])==='') $freight='0'; else $freight='"'.clean($_POST['stock_freight']).'"';
($_POST['stock_so_comp']=='')? $stock_so_comp='NULL' : $stock_so_comp='"'.clean($_POST['stock_so_comp']).'"';
($_POST['stock_po_comp']=='')? $stock_po_comp='NULL' : $stock_po_comp='"'.clean($_POST['stock_po_comp']).'"';
if (isset($_POST['stock_is_compl'])) $stock_is_compl=1; else $stock_is_compl=0;

$db =  db_connect();
$query= 'UPDATE stock_new SET '
        . 'stock_status = "'.clean($_POST['stock_item_status']).'", '
        . 'stock_note = "'.clean($_POST['stock_note']).'", '
        . 'stock_serial = "'.clean($_POST['stock_serial']).'", '
        . 'stock_compl_id = '.$stock_compl_id.', '
        . 'stock_is_compl = "'.$stock_is_compl.'", '
        . 'stock_po = "'.strtoupper(trim(clean($_POST['stock_po']))).'", '
        . 'stock_so = "'.strtoupper(trim(clean($_POST['stock_so']))).'", '
        . 'stock_so_comp = '.$stock_so_comp.', '
        . 'stock_po_type = "'.clean($_POST['stock_po_type']).'", '
        . 'stock_officialy_sold = "'.$stock_officialy_sold.'", '
        . 'stock_so_type = "'.clean($_POST['stock_so_type']).'", '
        . 'stock_po_comp = '.$stock_po_comp.', '
        . 'stock_supplier = "'.clean($_POST['stock_supplier']).'", '
        . 'stock_stock_id = "'.clean($_POST['stock']).'", '
        . 'stock_condition = "'.clean($_POST['stock_condition']).'", '
        . 'stock_date_receipt = '.$date.', '
        . 'stock_sale_date = '.$stock_sale_date.', '
        . 'stock_price = '.$price.', '
        . 'stock_freight = '.$freight.', '
        . 'stock_ccd = "'.clean($_POST['stock_ccd']).'", '
        . 'stock_currency = "'.clean($_POST['stock_currency']).'", '
        . 'stock_our_company = "'.clean($_POST['stock_our_company']).'", '
        . 'stock_warranty_to = '.$warranty.', '
        . 'stock_deleted = "'.$del.'", '
        . 'stock_mod_by = "'.$_SESSION['uid'].'", '
        . 'stock_place = "'.clean($_POST['stock_place']).'"'
        .' WHERE stock_id = "'.clean($_POST['stock_id']).'"';
$result=$db->query($query);
if (!$result) {
    echo 'Error 1'.$db->error;
    exit();
}
//Проверка статуса
if ($_POST['stock_item_status']=="3" AND $stock_is_compl===1){
    $query= 'UPDATE stock_new SET '
            . 'stock_status="3", '
            . 'stock_sale_date = '.$stock_sale_date.', '
            . 'stock_so = "'.clean($_POST['stock_so']).'", '
            . 'stock_so_comp = '.$stock_so_comp.', '
            . 'stock_so_type = "'.clean($_POST['stock_so_type']).'" '
            .' WHERE stock_compl_id = "'.clean($_POST['stock_id']).'" AND stock_status IN (1,2)';
    $result=$db->query($query);
    if (!$result) {
        echo 'Error 2'.$db->error;
        exit();
    }
}
echo 'true';