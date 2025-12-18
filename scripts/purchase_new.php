<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../classes/Order_name_engine.php';

startSession();
security ();
if(check_access('acl_purchase', 2)) exit('Access denied.');
//Check dates
if ($_POST['po_invoice_date']!=='') $invoice_date='"'.clean($_POST['po_invoice_date']).'"'; else $invoice_date='NULL';
if ($_POST['po_ship_date']!=='') $shipment_date='"'.clean($_POST['po_ship_date']).'"'; else $shipment_date='NULL';
if (! isset($_POST['po_our_comp'])) {
    $arr = array('result' => 'false', 'id' =>'', 'error' =>'Our company not set');
    echo json_encode($arr);
    $db->rollback();
    $db->close();
    exit();
}

$db =  db_connect();
$db->autocommit(false);

$on = new Order_name_engine();
$on -> init($db);

//GET PO Number

if($result = $db->query('SELECT MAX(po_no) AS max FROM purchase WHERE po_our_comp="'.clean($_POST['po_our_comp']).'"'))
{
    $row = $result->fetch_assoc();
    $po_no=$row['max']+1;
} else {
    $arr = array('result' => 'false', 'id' =>'', 'error' => $db->error);
    echo json_encode($arr);
    $db->rollback();
    $db->close();
    exit();
}

$query= 'INSERT INTO purchase SET '
        . 'po_status="'.clean($_POST['po_status']).'", '
        . 'po_ship_date='.$shipment_date.', '
        . 'po_awb="'.clean($_POST['po_awb']).'", '
        . 'po_no='.$po_no.', '
        . 'po_date="'.clean($_POST['po_date']).'", '
        //. 'po_invoice="'.clean($_POST['po_invoice']).'", '
        . 'po_invoice_to="'.clean($_POST['po_invoice_to']).'", '
        . 'po_invoice_to_flag="'.clean($_POST['po_invoice_to_flag']).'", '
        . 'po_currency="'.$_POST['currency'].'", '
        . 'po_our_comp="'.clean($_POST['po_our_comp']).'", '
        //. 'po_invoice_date='.$invoice_date.', '
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
        . 'po_supplier="'.clean($_POST['po_supplier']).'"';
if (!$db->query($query)) {
    $arr = array('result' => 'false', 'id' =>'', 'error' => $db->error);
    echo json_encode($arr);
    $db->rollback();
    $db->close();
    exit();
}

$po_id=$db->insert_id;

try{
    $on -> type = 'PO';
    $on -> comp_id = clean($_POST['po_our_comp']);
    $on -> num = $po_no;
    $on ->get_order();
    $on ->check_order($on->order);
} catch (Error $ex){
    $db->rollback();
    $db->close();
    exit(json_encode(array('result' => 'false','error' =>$ex->getMessage(),'code'=>'2')));
}

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
            $arr = array('result' => 'false', 'id' =>'', 'error' => $db->error);
            echo json_encode($arr);
            $db->rollback();
            $db->close();
            exit();
        }
    }
}

$db->commit();
$db->close();
$arr = array('result' => 'true', 'id' => $po_id, 'error' => '','comp_id'=>clean($_POST['po_our_comp']),'po_no'=>$on->order);
echo json_encode($arr);
exit();