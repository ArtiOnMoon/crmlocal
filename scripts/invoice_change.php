<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security ();
if(check_access('acl_invoices', 2)) exit('Access denied.');

$invoice_id=clean($_POST['invoice_id']);
if ($_POST['invoice_date']!=='') $invoice_date='"'.clean($_POST['invoice_date']).'"'; else $invoice_date='NULL';
$invoice_shipped_from= (clean($_POST['invoice_shipped_from'])==='') ? '0':'"'.clean($_POST['invoice_shipped_from']).'"';
$invoice_shipped_to= (clean($_POST['invoice_shipped_to'])==='') ? '0':'"'.clean($_POST['invoice_shipped_to']).'"';
$invoice_ship_req=(isset($_POST['invoice_ship_req'])) ? 0:1;
//$invoice_order_type=(clean($_POST['invoice_order_type'])==='') ? 'NULL':'"'.clean($_POST['invoice_order_type']).'"';
//$invoice_order_comp=(clean($_POST['invoice_order_comp'])==='') ? 'NULL':'"'.clean($_POST['invoice_order_comp']).'"';
$invoice_order_num=(clean($_POST['invoice_order_num'])==='') ? 'NULL':'"'. strtoupper(trim(clean($_POST['invoice_order_num']))).'"';
$invoice_ship_date=(clean($_POST['invoice_ship_date'])==='') ? 'NULL':'"'.clean($_POST['invoice_ship_date']).'"';
$invoice_our_bank_det=(clean($_POST['invoice_our_bank_det'])=='') ? 'NULL':'"'.clean($_POST['invoice_our_bank_det']).'"';
$inv_instructions = $_POST['inv_instructions'] ?? 0;

$db =  db_connect();
$db->autocommit(false);
$query= 'UPDATE invoices SET '
        . 'invoice_type="'.clean($_POST['invoice_type']).'", '
        . 'invoice_date='.$invoice_date.', '
        . 'invoice_num="'.clean($_POST['invoice_num']).'", '
        . 'invoice_status="'.clean($_POST['invoice_status']).'", '
        . 'invoice_our_comp="'.clean($_POST['invoice_our_comp']).'", '
        . 'invoice_our_bank_det='.$invoice_our_bank_det.', '
        . 'invoice_customer="'.clean($_POST['invoice_customer']).'", '
//        . 'invoice_order_type='.$invoice_order_type.', '
//        . 'invoice_order_comp='.$invoice_order_comp.', '
        . 'invoice_order_num='.$invoice_order_num.', '
        . 'invoice_cust_ref="'.clean($_POST['invoice_cust_ref']).'", '
        . 'invoice_currency="'.$_POST['invoice_currency'].'", '
        . 'invoice_total="'.clean($_POST['invoice_total']).'", '
        . 'invoice_pay_terms="'.clean($_POST['invoice_pay_terms']).'", '
        . 'invoice_note="'.clean($_POST['invoice_note']).'", '
        . 'invoice_shipped_from = '.$invoice_shipped_from.', '
        . 'invoice_shipped_name = "'.clean($_POST['invoice_shipped_name']).'", '
        . 'invoice_shipped_addr1 = "'.clean($_POST['invoice_shipped_addr1']).'", '
        . 'invoice_shipped_addr2 = "'.clean($_POST['invoice_shipped_addr2']).'", '
        . 'invoice_shipped_country = "'.clean($_POST['invoice_shipped_country']).'", '
        . 'invoice_shipped_vat = "'.clean($_POST['invoice_shipped_vat']).'", '
        . 'invoice_shipped_from_name = "'.clean($_POST['invoice_shipped_from_name']).'", '
        . 'invoice_shipped_from_addr1 = "'.clean($_POST['invoice_shipped_from_addr1']).'", '
        . 'invoice_shipped_from_addr2 = "'.clean($_POST['invoice_shipped_from_addr2']).'", '
        . 'invoice_shipped_from_country = "'.clean($_POST['invoice_shipped_from_country']).'", '
        . 'invoice_shipped_from_vat = "'.clean($_POST['invoice_shipped_from_vat']).'", '        
        . 'invoice_shipped_to = '.$invoice_shipped_to.', '
        . 'invoice_shipped_to_flag = '.clean($_POST['invoice_shipped_to_flag']).', '
        . 'invoice_shipped_from_flag = '.clean($_POST['invoice_shipped_from_flag']).', '
        . 'invocie_delevery_terms = "'.clean($_POST['invocie_delevery_terms']).'", '
        . 'invoice_ship_date = '.$invoice_ship_date.', '
        . 'invoice_shipped_on = "'.clean($_POST['invoice_shipped_on']).'", '
        . 'invoice_vat_remarks = "'.clean($_POST['invoice_vat_remarks']).'", '
        . 'invocie_delevery_terms = "'.clean($_POST['invocie_delevery_terms']).'", '
        . 'invoice_awb = "'.clean($_POST['invoice_awb']).'", '
        . 'inv_instructions = "'.$inv_instructions.'", '
        . 'inv_inst_comp_name = "'.clean($_POST['inv_inst_comp_name']).'", '
        . 'inv_inst_comp_name2 = "'.clean($_POST['inv_inst_comp_name2']).'", '
        . 'inv_inst_comp_addr1 = "'.clean($_POST['inv_inst_comp_addr1']).'", '
        . 'inv_inst_comp_addr2 = "'.clean($_POST['inv_inst_comp_addr2']).'", '
        . 'inv_inst_comp_country = "'.clean($_POST['inv_inst_comp_country']).'", '
        . 'inv_inst_comp_vat = "'.clean($_POST['inv_inst_comp_vat']).'", '
        . 'inv_inst_comp_email = "'.clean($_POST['inv_inst_comp_email']).'", '
        . 'invoice_ship_req = '.$invoice_ship_req.', '
        . 'invoice_modified="'.$_SESSION['uid'].'" '
        . 'WHERE invoice_id="'.$invoice_id.'" ';
if (!$db->query($query)) {
    echo '1. '.$db->error;
    echo $query;
    $db->rollback();
    $db->close();
    exit();
}

//Очистка старых записей
if(!$db->query('DELETE FROM invoices_content WHERE inv_con_inv_id="'.$invoice_id.'"')){
    echo '2. '.$db->error;
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
}
// Содержание заявки
if(isset($_POST['inv_con_text']))$length=count($_POST['inv_con_text']);ELSE $length=0;
if ($length>=1){
    for ($i=0;$i<$length;$i++){
//        $inv_con_type = ($_POST['inv_con_type'][$i]=='')? '0': '"'.$_POST['inv_con_type'][$i].'"';
        $base_id = ($_POST['inv_con_base_id'][$i]=='')? 'Null': '"'.$_POST['inv_con_base_id'][$i].'"';
        $query='INSERT INTO invoices_content VALUES(DEFAULT,"'.$invoice_id.'",'
//        .'"'.$inv_con_type.'",'
        .$base_id.',"'
        .clean($_POST['inv_con_text'][$i]).'","'
        .$_POST['inv_con_price'][$i].'","'
        .$_POST['inv_con_qty'][$i].'","'
        .$_POST['inv_con_discount'][$i].'","'
        .$_POST['inv_con_note'][$i].'")';
        if(!$db->query($query)){
            echo '4';
            echo $db->error;
            echo $query;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}

$db->commit();
$db->close();
exit('true');