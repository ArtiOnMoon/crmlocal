<?php
require_once '../functions/auth.php';
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/doc_fns.php';
require_once '../functions/service.php';
require_once '../classes/Order_name_engine.php';

security();

$service_num=$_GET['id'];
//Данные агента
$db=  db_connect();

$query='SELECT service.*,c1.cust_full_name as customer,customers.cust_full_name, customers.contact_phone,customers.email,customers.address,customers.address2, name, customers_contacts.email, phone, mob '
        . 'FROM service '
        . 'LEFT JOIN customers_contacts ON agent_contact_id=id '
        . 'LEFT JOIN customers ON cust_id=agent '
        . 'LEFT JOIN customers c1 ON c1.cust_id=comp_id '
        . 'WHERE service_id="'.$service_num.'"';
$result=$db->query($query);
echo $db->error;
$row = $result->fetch_assoc();

$on = new Order_name_engine();
$on->init($db);
$on->num = $row['service_no'];
$on->comp_id = $row['service_our_comp'];
$on->type = 'SR';
$on->get_order();

//$service_id = service_id_num($row['service_no'],$row['service_our_comp']);
//Данные судна
$query2='SELECT vessels.*, countries.name, countries.code FROM vessels LEFT JOIN countries ON vessels.flag=countries.id where vessel_id="'.$row['vessel_id'].'"';
$result=$db->query($query2);
$row2 = $result->fetch_assoc();

//Список инженеров

$query= 'SELECT su_uid,full_name from service_users, users WHERE su_uid=uid AND su_service_id = "'.$service_num.'"';
$u_list=$db->query($query);
$users='';
while($elem = $u_list->fetch_assoc()){
    $users.=$elem['full_name'].'<w:br />';
}

require '../vendor/autoload.php';
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../templates/service_report_v1.docx');

$templateProcessor->setValue('service_id',$on->order);
$templateProcessor->setValue('vessel_imo',$row2['vessel_name'].' \ '.$row2['imo']);
$templateProcessor->setValue('mmsi_call',$row2['mmsi'].' \ '.$row2['call_sign']);
$templateProcessor->setValue('flag_class',$row2['name'].' \ '.$row2['class_societies']);
$templateProcessor->setValue('engineers',$users);
$templateProcessor->setValue('agent',htmlspecialchars_decode($row['cust_full_name'],ENT_QUOTES));
$templateProcessor->setValue('port',htmlspecialchars_decode($row['location'],ENT_QUOTES));
$templateProcessor->setValue('cust_po',$row['customer'].' \ '.$row['PO']);
$templateProcessor->setValue('date',date('Y-m-d'));
$templateProcessor->setValue('date2',date('Y-m-d',time()+86400));//24*60*60

// Заполнение таблицы
$query='SELECT service_fault_descr.*, srv_eq_name, mnf_short_name, type_text,stock_cat_name FROM service_fault_descr '
        . 'LEFT JOIN service_equipment ON sfd_equip_id=srv_eq_id '
        . 'LEFT JOIN service_types ON sfd_type=service_types.id '
        . 'LEFT JOIN stock_cats ON stock_cats.id=srv_eq_cat '
        . 'LEFT JOIN manufacturers ON mnf_id=srv_eq_manuf '
        . 'WHERE sfd_serv_id="'.$service_num.'"';
$result=$db->query($query);
$values=array();
while($fd = $result->fetch_assoc()){
    $a=array('content'=>$fd['type_text'],'content2'=>$fd['stock_cat_name'].' '.$fd['mnf_short_name'].' '.$fd['srv_eq_name'].' '.$fd['sfd_equip_comment'],'content3'=>$fd['sfd_descr']);
    array_push($values,$a);
}
$templateProcessor->cloneRowAndSetValues('content', $values);


header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="'.$on->order.' - '.$row2['vessel_name'].' - SR.docx'.'"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
// Saving the document as OOXML file...
$templateProcessor->saveAs("php://output");