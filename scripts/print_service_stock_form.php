<?php
require_once '../functions/auth.php';
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/doc_fns.php';
require_once '../functions/service.php';
require_once '../classes/Order_name_engine.php';
security();

function condition_decode($val){
    if($val=='1') return 'new';
    elseif ($val=='2') return 'used';
    elseif ($val=='3') return 'defect';
    elseif ($val=='4') return 'restored';
    return 'not set';
}

$service_id=$_GET['id'];
//Данные агента
$db=  db_connect();
$query='SELECT service.*,c1.cust_full_name as customer,customers.cust_full_name, customers.contact_phone,customers.email,customers.address,customers.address2, name, customers_contacts.email, phone, mob '
        . 'FROM service '
        . 'LEFT JOIN customers_contacts ON agent_contact_id=id '
        . 'LEFT JOIN customers ON cust_id=agent '
        . 'LEFT JOIN customers c1 ON c1.cust_id=comp_id '
        . 'WHERE service_id="'.$service_id.'"';
$result=$db->query($query);
echo $db->error;
$row = $result->fetch_assoc();

$on = new Order_name_engine();
$on->init($db);
$on->num = $row['service_no'];
$on->comp_id = $row['service_our_comp'];
$on->type = 'SR';

$service_num = $on->get_order();
//echo $service_num;
//Данные судна
$query2='SELECT vessels.*, countries.name, countries.code FROM vessels LEFT JOIN countries ON vessels.flag=countries.id where vessel_id="'.$row['vessel_id'].'"';
$result = $db->query($query2);
$row2 = $result->fetch_assoc();

//Список инженеров

//$query= 'SELECT su_uid,full_name from service_users, users WHERE su_uid=uid AND su_service_id = "'.$service_num.'"';
//$u_list=$db->query($query);
//$users='';
//while($elem = $u_list->fetch_assoc()){
//    $users.=$elem['full_name'].'<w:br />';
//}

require '../vendor/autoload.php';
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../templates/service_stock_form.docx');

$templateProcessor->setValue('order_no',$service_num);
$templateProcessor->setValue('print_date',Date('Y-m-d'));


// Заполнение таблицы
$query='SELECT stock_new.*, stock_nmnc.*, mnf_short_name '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'WHERE stock_so="'.$on->order.'"';
//echo $query;
$result=$db->query($query);
$values=array();
while($fd = $result->fetch_assoc()){
    $a=array('id'=>$fd['stock_id'],
        'maker'=>$fd['mnf_short_name'],
        'descr'=>$fd['stnmc_descr'],
        'model'=>$fd['stnmc_type_model'],
        'pn'=>$fd['stnmc_pn'],
        'sn'=>$fd['stock_serial'],
        'comment'=>condition_decode($fd['stock_condition']));
    array_push($values,$a);
}
$templateProcessor->cloneRowAndSetValues('id', $values);


header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="'.$on->order.' - Spare parts.docx'.'"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
// Saving the document as OOXML file...
$templateProcessor->saveAs("php://output");