<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';

startSession();
security ();

if(check_access('acl_sales', 2, clean($_POST['sales_our_comp']))) exit('Access denied.');

$sale_id = clean($_POST['sale_id']);

if (clean($_POST['sales_no'])=='') $sales_no=$sale_id;
else $sales_no=clean($_POST['sales_no']);
@$invoice_date= ($_POST['sales_invoice_date']==='') ? 'Null':'"'.clean($_POST['sales_invoice_date']).'"';
@$sales_payment=($_POST['sales_payment']==='') ? '0':''.clean($_POST['sales_payment']).'';
@$sales_pay_date=($_POST['sales_pay_date']==='') ? 'Null':'"'.clean($_POST['sales_pay_date']).'"';
@$sale_shipment_dew=($_POST['sale_shipment_dew']==='') ? 'Null':'"'.clean($_POST['sale_shipment_dew']).'"';
@$sales_invoice_from= (clean($_POST['sales_invoice_from'])==='') ? 'Null':'"'.clean($_POST['sales_invoice_from']).'"';
@$sales_invoice_to= (clean($_POST['sales_invoice_to'])==='') ? 'Null':'"'.clean($_POST['sales_invoice_to']).'"';
@$sales_shipped_from= (clean($_POST['sales_shipped_from'])==='') ? '0':'"'.clean($_POST['sales_shipped_from']).'"';
@$sales_shipped_to= (clean($_POST['sales_shipped_to'])==='') ? '0':'"'.clean($_POST['sales_shipped_to']).'"';
@$sales_our_bank_details= (clean($_POST['sales_our_bank_details'])==='') ? 'Null':'"'.clean($_POST['sales_our_bank_details']).'"';
@$sales_ship_date=($_POST['sales_ship_date']==='') ? 'Null':'"'.clean($_POST['sales_ship_date']).'"';
@$sales_qte_date=($_POST['sales_qte_date']==='') ? 'Null':'"'.clean($_POST['sales_qte_date']).'"';
if (isset($_POST['sales_inv_instructions']))$sales_inv_instructions=1; ELSE $sales_inv_instructions=0;
if (isset($_POST['sales_cn_flag']))$sales_cn_flag=1; ELSE $sales_cn_flag=0;
@$sales_po_comp = ($_POST['sales_po_comp']==='') ? 'Null':'"'.clean($_POST['sales_po_comp']).'"';

$db =  db_connect();
$db->autocommit(false);
$query= 'UPDATE sales SET '
        . 'sales_no = "'.$sales_no.'", '
        . 'sales_status = "'.clean($_POST['sale_status']).'", '
        . 'sales_date = "'.clean($_POST['sales_date']).'", '
        . 'sales_descr = "'.clean($_POST['sales_descr']).'", '
        . 'sales_qte_note = "'.clean($_POST['sales_qte_note']).'", '
        . 'sales_customer= "'.clean($_POST['sales_customer']).'", '
        . 'sales_cust_po = "'.clean($_POST['sales_cust_po']).'", '
        . 'sales_request = "'.clean($_POST['sales_request']).'", '
        . 'sales_payment = "'.$sales_payment.'", '
        . 'sale_shipment_dew = '.$sale_shipment_dew.', '
        . 'sales_vessel_id = "'.clean($_POST['sales_vessel_id']).'", '
        . 'sales_pay_date = '.$sales_pay_date.', '
//        . 'sales_po_comp = '.$sales_po_comp.', '
//        . 'sales_po_num = "'.clean($_POST['sales_po_num']).'", '
        . 'sales_qte_date = '.$sales_qte_date.', '
        . 'sales_invoice = "'.clean($_POST['sales_invoice']).'", '
        . 'sales_shipped_to_flag = '.clean($_POST['sales_shipped_to_flag']).', '
        . 'sales_shipped_from_flag = '.clean($_POST['sales_shipped_from_flag']).', '
        . 'sales_ship_date = '.$sales_ship_date.', '
        . 'sales_pay_terms = "'.clean($_POST['sales_pay_terms']).'", '
        . 'sales_shipped_from = '.$sales_shipped_from.', '
        . 'sales_shipped_name = "'.clean($_POST['sales_shipped_name']).'", '
        . 'sales_shipped_addr1 = "'.clean($_POST['sales_shipped_addr1']).'", '
        . 'sales_shipped_addr2 = "'.clean($_POST['sales_shipped_addr2']).'", '
        . 'sales_shipped_country = "'.clean($_POST['sales_shipped_country']).'", '
        . 'sales_shipped_vat = "'.clean($_POST['sales_shipped_vat']).'", '
        . 'sales_shipped_from_name = "'.clean($_POST['sales_shipped_from_name']).'", '
        . 'sales_shipped_from_addr1 = "'.clean($_POST['sales_shipped_from_addr1']).'", '
        . 'sales_shipped_from_addr2 = "'.clean($_POST['sales_shipped_from_addr2']).'", '
        . 'sales_shipped_from_country = "'.clean($_POST['sales_shipped_from_country']).'", '
        . 'sales_shipped_from_vat = "'.clean($_POST['sales_shipped_from_vat']).'", '        
        . 'sales_shipped_to = '.$sales_shipped_to.', '
        . 'sales_shipped_on = "'.clean($_POST['sales_shipped_on']).'", '
        . 'sales_delevery_terms = "'.clean($_POST['sales_delevery_terms']).'", '
        . 'sales_total = "'.round((float)clean($_POST['total']),2).'", '
        . 'sales_total_vat = "'.round((float)clean($_POST['sales_total_vat']),2).'", '
        . 'sales_total_cfm = "'.round((float)clean($_POST['sales_total_cfm']),2).'", '
        . 'sales_vat_cfm = "'.round((float)clean($_POST['sales_vat_cfm']),2).'", '
        . 'sales_currency = "'.clean($_POST['sales_currency']).'", '
        . 'modified = "'.$_SESSION['uid'].'", '
        . 'sales_awb = "'.clean($_POST['sales_awb']).'" '
        . 'where sales_id="'.$sale_id.'"';
$result=$db->query($query);
if (!$result) {
    echo $db->error;
    echo $query;
    $db->rollback();
    $db->close();
    exit();
}
//Sales_id into sales_no
//if (!isset($_POST['sales_no'])) {
//    if(!$db->query('update sales set sales_no="'.$sale_id.'" where sales_id="'.$_POST['sale_id'].'"')){
//        echo $db->error;
//        $db->rollback();
//        $db->close();
//        exit();
//    };
//}
//Sale CONTENT
//Очистка старых записей
if(!$db->query('delete from sales_content where scont_sale_id="'.$sale_id.'"')){
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
};
// Содержание заявки
@$length=count($_POST['scont_text']);
if ($length>=1){
    for ($i=0;$i<$length;$i++){
        $base_id = ($_POST['scont_base_id'][$i]=='')? 'Null': '"'.$_POST['scont_base_id'][$i].'"';
        $query='insert into sales_content VALUES(DEFAULT,"'.$sale_id.'",'
        .$base_id.',"'
        .clean($_POST['scont_text'][$i]).'","'
        .round($_POST['scont_price'][$i],2).'","'
        .$_POST['scont_qty'][$i].'","'
        .$_POST['scont_cfm_qty'][$i].'","'
        .$_POST['scont_discount'][$i].'","'
        .$_POST['scont_currency'][$i].'","'
        .round($_POST['scont_currency_rate'][$i],4).'","'
        .$_POST['scont_vat'][$i].'","'
        .$_POST['scont_has_serial'][$i].'","'
        .$_POST['scont_serials'][$i].'","'
        .$_POST['scont_box_no'][$i].'","'
        .$_POST['scont_delivery'][$i].'")';
        if(!$db->query($query)){
            echo $sale_id;
            echo $db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}
//SALES PACKAGE
//Очистка старых записей
if(!$db->query('DELETE FROM sales_package WHERE sales_pack_sale_id="'.$sale_id.'"')){
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
};
// Информация
if (isset($_POST['sales_pack_box_no']) and is_countable($_POST['sales_pack_box_no'])) $length=count($_POST['sales_pack_box_no']); else $length = 0;
if ($length>=1){
    for ($i=0;$i<$length;$i++){
        if ($_POST['sales_pack_width'][$i]!=='')$sales_pack_width='"'.clean($_POST['sales_pack_width'][$i]).'"';else $sales_pack_width='NULL';
        if ($_POST['sales_pack_depth'][$i]!=='')$sales_pack_depth='"'.clean($_POST['sales_pack_depth'][$i]).'"';else $sales_pack_depth='NULL';
        if ($_POST['sales_pack_height'][$i]!=='')$sales_pack_height='"'.clean($_POST['sales_pack_height'][$i]).'"';else $sales_pack_height='NULL';
        if ($_POST['sales_pack_weight'][$i]!=='')$sales_pack_weight='"'.clean($_POST['sales_pack_weight'][$i]).'"';else $sales_pack_weight='NULL';
        if ($_POST['sales_pack_content'][$i]!=='')$sales_pack_content='"'.clean($_POST['sales_pack_content'][$i]).'"';else $sales_pack_content='NULL';
        
        $query='INSERT INTO sales_package VALUES(DEFAULT,"'.$sale_id.'","'
        .clean($_POST['sales_pack_box_no'][$i]).'",'
        .$sales_pack_width.','
        .$sales_pack_depth.','
        .$sales_pack_height.','
        .$sales_pack_weight.','
        .$sales_pack_content.')';
        if(!$db->query($query)){
            echo 'Package error:',$query;
            echo $db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}

$db->commit();
$db->close();
exit('true');
//header('Location: /sales.php');