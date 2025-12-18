<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../classes/Order_name_engine.php';

startSession();
security ();
if(check_access('acl_sales', 2)) exit('Access denied.');

$db=db_connect();
$db->autocommit(false);

$on = new Order_name_engine();
$on -> init($db);


$sales_our_comp = clean($_POST['sales_our_comp']);
//$sales_invoice_to= (clean($_POST['sales_invoice_to'])==='') ? 'Null':'"'.clean($_POST['sales_invoice_to']).'"';
$sales_payment=($_POST['sales_payment']==='') ? '0':''.clean($_POST['sales_payment']).'';
$sales_pay_date=($_POST['sales_pay_date']==='') ? 'Null':'"'.clean($_POST['sales_pay_date']).'"';
$sale_shipment_dew=($_POST['sale_shipment_dew']==='') ? 'Null':'"'.clean($_POST['sale_shipment_dew']).'"';
$sales_po_comp = ($_POST['sales_po_comp']==='') ? 'Null':'"'.clean($_POST['sales_po_comp']).'"';
//if (isset($_POST['sales_inv_instructions']))$sales_inv_instructions=1; ELSE $sales_inv_instructions=0;

$result = $db->query('SELECT payment_terms FROM customers WHERE cust_id="'.$sales_our_comp.'"');
$row = $result->fetch_assoc();
echo $db->error;

//Obtaining next number
if(! $result = $db->query('SELECT MAX(sales_no) FROM sales WHERE sales_our_comp="'.$sales_our_comp.'"')){
    $message = json_encode(array('result' => 'false', 'error' =>$db->error, 'code'=>'0'));
    $db->rollback();
    $db->close();
    exit($message);
}
$sales_no = $result->fetch_row()[0] + 1;

$query= 'INSERT INTO sales SET '
        . 'sales_no = "'.$sales_no.'", '
        . 'sales_status = "'.clean($_POST['sale_status']).'", '
        . 'sales_our_comp = "'.$sales_our_comp.'", '
        . 'sales_cust_po = "'.clean($_POST['sales_cust_po']).'", '
        . 'sales_request = "'.clean($_POST['sales_request']).'", '
        . 'sales_payment = "'.$sales_payment.'", '
        . 'sales_pay_date = '.$sales_pay_date.', '
        . 'sale_shipment_dew = '.$sale_shipment_dew.', '
        . 'sales_date = "'.clean($_POST['sales_date']).'", '
        . 'sales_descr = "'.clean($_POST['sales_descr']).'", '
        . 'sales_qte_note = "'.clean($_POST['sales_qte_note']).'", '
        . 'sales_vessel_id = "'.clean($_POST['sales_vessel_id']).'", '
//        . 'sales_po_comp = '.$sales_po_comp.', '
//        . 'sales_po_num = "'.clean($_POST['sales_po_num']).'", '
        . 'sales_customer = "'.clean($_POST['sales_customer']).'", '
        . 'sales_total = "'.clean($_POST['total']).'", '
        . 'sales_total_vat = "'.round(clean($_POST['sales_total_vat']),2).'", '
        . 'sales_vat_cfm = "'.round(clean($_POST['sales_vat_cfm']),2).'", '
        . 'sales_total_cfm = "'.round(clean($_POST['sales_total_cfm']),2).'", '
        . 'sales_currency = "'.clean($_POST['sales_currency']).'", '
        . 'sales_pay_terms = "'.$row['payment_terms'].'", '
        //. 'sales_invoice_to_flag = '.clean($_POST['sales_invoice_to_flag']).', '
        //. 'sales_invoice_to = '.$sales_invoice_to.', '
        . 'modified = "'.$_SESSION['uid'].'"';
if (!$db->query($query)) {
    $message = json_encode(array('result' => 'false', 'error' =>$db->error, 'code'=>'1'));
    $db->rollback();
    $db->close();
    exit($query);
}
$sale_id=$db->insert_id;
//$sales_no=(int)clean($_POST['sales_no']);
//$sales_our_comp=clean($_POST['sales_our_comp']);
//
////Sales_id into sales_no
//if (!isset($_POST['sales_no'])) {
//    if($result=$db->query('SELECT MAX(sales_no) AS max FROM sales WHERE sales_our_comp = "'.clean($_POST['sales_our_comp']).'"')){
//        $row = $result->fetch_assoc();
//        $db->query('UPDATE sales SET sales_no='.($row['max']+1).' WHERE sales_id='.$sale_id);
//        $sales_no=$row['max']+1;
//    } else {
//        echo $db->error;
//        $db->rollback();
//        $db->close();
//        exit(json_encode(array('result' => 'false')));
//    }
//}

try{
    $on -> type = 'SL';
    $on -> comp_id = $sales_our_comp;
    $on -> num = $sales_no;
    $on ->get_order();
    $on ->check_order($on->order);
} catch (Error $ex){
    $db->rollback();
    $db->close();
    exit(json_encode(array('result' => 'false','error' =>$ex->getMessage(),'code'=>'2')));
}

// Содержание заявки
if(isset($_POST['scont_text']))$length=count($_POST['scont_text']);else $length=0;
//@$length=count($_POST['scont_text']);
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
            $message = json_encode(array('result' => 'false', 'error' =>$db->error,'code'=>'3'));
            $db->rollback();
            $db->close();
            exit($message);
        }
    }
}
$db->commit();
$db->close();
$arr = array('result' => 'true', 'order' => $on->order);
exit(json_encode($arr));