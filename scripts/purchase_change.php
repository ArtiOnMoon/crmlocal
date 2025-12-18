<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security ();
if(check_access('acl_purchase', 2)) exit('Access denied.');
//Check dates
$po_id=clean($_POST['po_id']);
if ($_POST['po_invoice_date']!=='') $invoice_date='"'.clean($_POST['po_invoice_date']).'"'; else $invoice_date='NULL';
if ($_POST['po_ship_date']!=='') $shipment_date='"'.clean($_POST['po_ship_date']).'"'; else $shipment_date='NULL';
if ($_POST['po_no']!=='') $po_no='"'.clean($_POST['po_no']).'"'; else $po_no='NULL';
//print_r($_POST['po_con_base_id']);
//print_r($_POST['po_con_text']);
//print_r($_POST['po_con_price']);
//print_r($_POST['po_con_qty']);
//print_r($_POST['po_con_discount']);
//print_r($_POST['po_con_note']);
//exit();

$db =  db_connect();
$db->autocommit(false);
$query= 'UPDATE purchase SET '
        . 'po_status="'.clean($_POST['po_status']).'", '
        . 'po_ship_date='.$shipment_date.', '
        . 'po_awb="'.clean($_POST['po_awb']).'", '
        . 'po_no='.$po_no.', '
        . 'po_date="'.clean($_POST['po_date']).'", '
        //. 'po_invoice="'.clean($_POST['po_invoice']).'", '
        //. 'po_invoice_date='.$invoice_date.', '
        . 'po_invoice_to="'.clean($_POST['po_invoice_to']).'", '
        . 'po_invoice_to_flag="'.clean($_POST['po_invoice_to_flag']).'", '
        . 'po_currency="'.$_POST['currency'].'", '
        . 'po_our_comp='.clean($_POST['po_our_comp']).', '
        . 'po_delivery1="'.clean($_POST['po_delivery1']).'", '
        . 'po_delivery2="'.clean($_POST['po_delivery2']).'", '
        . 'po_delivery3="'.clean($_POST['po_delivery3']).'", '
        . 'po_delivery4="'.clean($_POST['po_delivery4']).'", '
        . 'po_pic_name="'.clean($_POST['po_pic_name']).'", '
        . 'po_pic_phone="'.clean($_POST['po_pic_phone']).'", '
        . 'po_note="'.clean($_POST['po_note']).'", '
        . 'po_print_note="'.clean($_POST['po_print_note']).'", '
        . 'modified="'.$_SESSION['uid'].'", '
        . 'po_total="'.clean($_POST['total']).'", '
        . 'po_supplier="'.clean($_POST['po_supplier']).'" '
        . 'WHERE po_id="'.$po_id.'" ';
if (!$db->query($query)) {
    echo '1';
    echo $db->error;
    echo $query;
    $db->rollback();
    $db->close();
    exit();
}

//Очистка старых записей
if(!$db->query('DELETE FROM purchase_content WHERE po_con_po_id="'.$po_id.'"')){
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
};
// Содержание заявки
if(isset($_POST['po_con_text']))$length=count($_POST['po_con_text']);ELSE $length=0;
if ($length>=1){
    for ($i=0;$i<$length;$i++){
        $base_id = ($_POST['po_con_base_id'][$i]=='')? 'Null': '"'.$_POST['po_con_base_id'][$i].'"';
        $query='INSERT INTO purchase_content VALUES(DEFAULT,"'.$po_id.'",'
        .$base_id.',"'
        .clean($_POST['po_con_text'][$i]).'","'
        .$_POST['po_con_price'][$i].'","'
        .$_POST['po_con_qty'][$i].'","'
        .$_POST['po_con_discount'][$i].'","'
        .$_POST['po_con_note'][$i].'")';
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