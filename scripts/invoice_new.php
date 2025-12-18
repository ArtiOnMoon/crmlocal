<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
startSession();
security ();
if(check_access('acl_invoices', 2)) exit('Access denied.');
//Check dates
if ($_POST['invoice_date']!=='') $invoice_date='"'.clean($_POST['invoice_date']).'"'; else $invoice_date='NULL';
//if ($_POST['invoice_pay_date']!=='') $invoice_pay_date='"'.clean($_POST['invoice_pay_date']).'"'; else $invoice_pay_date='NULL';

$invoice_order_type=(clean($_POST['invoice_order_type'])==='') ? 'NULL':'"'.clean($_POST['invoice_order_type']).'"';
$invoice_order_comp=(clean($_POST['invoice_order_comp'])==='') ? 'NULL':'"'.clean($_POST['invoice_order_comp']).'"';
$invoice_order_num=(clean($_POST['invoice_order_num'])==='') ? 'NULL':'"'.clean($_POST['invoice_order_num']).'"';
$invoice_shipped_from= (clean($_POST['invoice_shipped_from'])==='') ? '0':'"'.clean($_POST['invoice_shipped_from']).'"';
$invoice_shipped_to= (clean($_POST['invoice_shipped_to'])==='') ? '0':'"'.clean($_POST['invoice_shipped_to']).'"';
$invoice_ship_req=(isset($_POST['invoice_ship_req'])) ? 0:1;
$invoice_ship_date=(clean($_POST['invoice_ship_date'])==='') ? 'NULL':'"'.clean($_POST['invoice_ship_date']).'"';
$invoice_our_bank_det=(clean($_POST['invoice_our_bank_det'])=='') ? 'NULL':'"'.clean($_POST['invoice_our_bank_det']).'"';
$invoice_shipped_to_flag = (isset($_POST['invoice_shipped_to_flag'])) ? 1:0;
$invoice_shipped_from_flag = (clean($_POST['invoice_shipped_from_flag'])=='') ? '0':'"'.clean($_POST['invoice_shipped_from_flag']).'"';


$db =  db_connect();
$db->autocommit(false);
$query= 'INSERT INTO invoices SET '
        . 'invoice_type="'.clean($_POST['invoice_type']).'", '
        . 'invoice_date='.$invoice_date.', '
        . 'invoice_num="'.clean($_POST['invoice_num']).'", '
        . 'invoice_status="'.clean($_POST['invoice_status']).'", '
        . 'invoice_our_comp="'.clean($_POST['invoice_our_comp']).'", '
        . 'invoice_our_bank_det='.$invoice_our_bank_det.', '
        . 'invoice_customer="'.clean($_POST['invoice_customer']).'", '
        . 'invoice_cust_ref="'.clean($_POST['invoice_cust_ref']).'", '
        . 'invoice_order_type='.$invoice_order_type.', '
        . 'invoice_order_comp='.$invoice_order_comp.', '
        . 'invoice_order_num='.$invoice_order_num.', '
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
        . 'invoice_shipped_to_flag = "'.$invoice_shipped_to_flag.'", '
        . 'invoice_shipped_from_flag = '.$invoice_shipped_from_flag.', '
        . 'invocie_delevery_terms = "'.clean($_POST['invocie_delevery_terms']).'", '
        . 'invoice_ship_date = '.$invoice_ship_date.', '
        . 'invoice_shipped_on = "'.clean($_POST['invoice_shipped_on']).'", '
        . 'invoice_vat_remarks = "'.clean($_POST['invoice_vat_remarks']).'", '
        . 'invoice_awb = "'.clean($_POST['invoice_awb']).'", '
        . 'invoice_modified="'.$_SESSION['uid'].'"';
//$arr = array('result' => 'false','error' => $query);
//    echo json_encode($arr);
//    exit();
if (!$db->query($query)) {
    $arr = array('result' => 'false','error' => $db->error);
    echo json_encode($arr);
    $db->rollback();
    $db->close();
    exit();
}
$invoice_id=$db->insert_id;

// Содержание заявки
if(isset($_POST['inv_con_text']))$length=count($_POST['inv_con_text']);ELSE $length=0;
if ($length>=1){
    for ($i=0;$i<$length;$i++){
        $base_id = ($_POST['inv_con_base_id'][$i]=='')? 'Null': '"'.$_POST['inv_con_base_id'][$i].'"';
        $inv_con_type = ($_POST['inv_con_type'][$i]=='')? '0': '"'.$_POST['inv_con_type'][$i].'"';
        $query='INSERT INTO invoices_content VALUES(DEFAULT,"'.$invoice_id.'",'
//        .$inv_con_type.','
        .$base_id.',"'
        .clean($_POST['inv_con_text'][$i]).'","'
        .$_POST['inv_con_price'][$i].'","'
        .$_POST['inv_con_qty'][$i].'","'
        .$_POST['inv_con_discount'][$i].'","'
        .$_POST['inv_con_note'][$i].'")';
        if(!$db->query($query)){
            $arr = array('result' => 'false','error' => $db->error);
            echo json_encode($arr);
            $db->rollback();
            $db->close();
            exit();
        }
    }
}
//Создание ссылки - Больше не требуется!
//if($_POST['create_link']=='1'){
//    $comp_id=clean($_POST['link_comp']);
//    $number=clean($_POST['link_num']);
//    $type=clean($_POST['link_type']);
//    if ($type<4)$number=(int)$number;
//    $comp_id1=clean($_POST['invoice_our_comp']);
//    $number1=clean($_POST['invoice_num']);
//    $type1=4;
//    if ($type1<4)$number1=(int)$number1;
//    $query= 'SELECT id FROM cross_docs WHERE '
//        . 'comp1="'.$comp_id.'" AND type1="'.$type.'" AND num1="'.$number.'" AND comp2="'.$comp_id1.'" AND type2="'.$type1.'" AND num2="'.$number1.'" '
//        . 'UNION '
//        . 'SELECT id FROM cross_docs WHERE '
//        . 'comp1="'.$comp_id1.'" AND type1="'.$type1.'" AND num1="'.$number1.'" AND comp2="'.$comp_id.'" AND type2="'.$type.'" AND num2="'.$number.'"';
//    if(!$result=$db->query($query)){
//        $arr = array('result' => 'false','error' => $db->error,'error_text'=>'Error creating link');
//        echo json_encode($arr);
//        $db->rollback();
//        $db->close();
//        exit();
//    }
//    if($result->num_rows===0){
//        $query= 'INSERT INTO cross_docs SET '
//        . 'comp1="'.$comp_id.'",'
//        . 'type1="'.$type.'",'
//        . 'num1="'.$number.'",'
//        . 'comp2="'.$comp_id1.'",'
//        . 'type2="'.$type1.'",'
//        . 'num2="'.$number1.'"';
//        if(!$result=$db->query($query)){
//            $arr = array('result' => 'false','error' => $db->error,'error_text'=>'Error creating link');
//            echo json_encode($arr);
//            $db->rollback();
//            $db->close();
//            exit();
//        }
//    }
//}

$db->commit();
$db->close();
$arr = array('result' => 'true', 'invoice_our_comp' => clean($_POST['invoice_our_comp']), 'invoice_num' => clean($_POST['invoice_num']),'invoice_id' => $invoice_id);
echo json_encode($arr);
exit();