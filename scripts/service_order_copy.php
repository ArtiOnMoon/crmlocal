<?php
require_once '../functions/auth.php';
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/service.php';
startSession();
security();
if(check_access('acl_service', 2)) exit('Access denied.');
$id=$_GET['id'];
$db= db_connect();
$db->autocommit(false);
//COPY SERIVCE ORDER
$query='INSERT INTO 
    `service`(`service_our_comp`, `sr_form`, `service_date`, `vessel_id`, `comp_id`, `equipment`, `description`, `status`, `service_ext_cust`, `service_executor_id`, `srv_pay_type`, `invoice`, `modified`, `agent`, `agent_contact_id`, `ETA`, `ETD`, `location`, `PO`, `PO2`, `request`, `service_note`, `modified_date`, `paid_status`, `service_currency`, `service_total`, `srv_inv_from`, `srv_inv_comp_name`, `srv_inv_comp_name2`, `srv_inv_addr1`, `srv_inv_addr2`, `srv_inv_country`, `srv_inv_vat`, `srv_our_bank_details`, `srv_pay_terms`, `srv_inv_date`, `srv_inv_number`, `srv_cn_flag`, `srv_cn_number`, `srv_inv_email`, `inv_instructions`, `service_deleted`) 
    SELECT `service_our_comp`, `sr_form`, "'.date('Y-m-d').'", `vessel_id`, `comp_id`, `equipment`, `description`, 1, `service_ext_cust`, `service_executor_id`, `srv_pay_type`, `invoice`, `modified`, `agent`, `agent_contact_id`, `ETA`, `ETD`, `location`, `PO`, `PO2`, `request`, `service_note`, `modified_date`, `paid_status`, `service_currency`, `service_total`, `srv_inv_from`, `srv_inv_comp_name`, `srv_inv_comp_name2`, `srv_inv_addr1`, `srv_inv_addr2`, `srv_inv_country`, `srv_inv_vat`, `srv_our_bank_details`, `srv_pay_terms`, `srv_inv_date`, `srv_inv_number`, `srv_cn_flag`, `srv_cn_number`, `srv_inv_email`, `inv_instructions`, `service_deleted` 
    FROM service 
    WHERE service_id="'.$id.'"';
if (!$db->query($query)){
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
}
$new_id = $db->insert_id;
//Service_id into service_no
if($result=$db->query('SELECT MAX(service_no)AS max FROM service WHERE service_our_comp=(SELECT service_our_comp FROM service WHERE service_id='.$id.')')){
    $row = $result->fetch_assoc();
    $db->query('UPDATE service SET service_no='.($row['max']+1).' WHERE service_id='.$new_id);
} else {
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
}
//Копирование fault sescriptions
$query='INSERT INTO `service_fault_descr`(`sfd_equip_id`, `sfd_equip_comment`, `sfd_descr`, `sfd_serv_id`, `sfd_type`) SELECT sfd_equip_id, sfd_equip_comment, sfd_descr, "'.$new_id.'", sfd_type FROM service_fault_descr WHERE sfd_serv_id="'.$id.'"';
if (!$db->query($query)){
    echo $query;
    $db->rollback();
    $db->close();
    exit();
}
//CALCULATION FORM
$rates_cat='SELECT rate_cat_id,rate_cat_name FROM service_rates_cat WHERE rate_our_comp=(SELECT service_our_comp FROM service WHERE service_id='.$id.') ORDER BY rate_order';
$query='INSERT INTO service_calc_entries (entry_type, entry_text, entry_qty, entry_price, entry_related_id, entry_base_id) VALUES ';
$cats_list=$db->query($rates_cat);
while ($row = $cats_list->fetch_assoc()) {
    $query.='("3","'.$row['rate_cat_name'].'","0","0","'.$new_id.'","'.$row['rate_cat_id'].'"),'; 
}
$query.='("4","Port expenses:","1","20","'.$new_id.'",0),("4","Administrative fees:","1","20","'.$new_id.'",0)';
if (!$db->query($query)){
    echo '<p><font color="red">FAILED1:</font></p>'.$db->error;
    $db->rollback();
    $db->close();
    exit();
}
header('Location: /service.php');
$db->commit();