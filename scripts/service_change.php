<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/doc_fns.php';
startSession();
security ();

$db =  db_connect();
$db->autocommit(false);
if(check_access('acl_service', 2)) exit('Access denied.');
$service_id=clean($_POST['service_id']);

if ($_POST['service_executor_id']!=1 and $_POST['service_executor_id']!='') {
    $service_executor=$_POST['service_executor_id'];
} else {$service_executor='NULL';}
if ($_POST['agent_contact_id']=='' OR $_POST['agent_contact_id']=='NULL') $agent_contact='NULL'; else $agent_contact='"'.clean($_POST['agent_contact_id']).'"';
if ($_POST['new_ETA']=='') $ETA='NULL'; else $ETA='"'.clean($_POST['new_ETA']).'"';
//if ($_POST['service_no']=='') $service_no=$service_id; else $service_no=''.clean($_POST['service_no']).'';
if ($_POST['new_ETD']=='') $ETD='NULL'; else $ETD='"'.clean($_POST['new_ETD']).'"';
if ($_POST['service_total']=='') $service_total='NULL'; else $service_total='"'.clean($_POST['service_total']).'"';
if ($_POST['srv_inv_date']=='') $srv_inv_date='NULL'; else $srv_inv_date='"'.clean($_POST['srv_inv_date']).'"';
if ($_POST['srv_inv_from']=='') $srv_inv_from='NULL'; else $srv_inv_from='"'.clean($_POST['srv_inv_from']).'"';
if ($_POST['srv_inv_number'] =='') $invoice=clean($_POST['invoice']); else $invoice=clean($_POST['srv_inv_number']);
if (isset($_POST['srv_cn_flag'])) $srv_cn_flag=1; else $srv_cn_flag=0;
if (isset($_POST['inv_instructions']))$inv_instructions=1; ELSE $inv_instructions=0;
$query= 'UPDATE service SET '
        . 'comp_id = "'.clean($_POST['new_customer']).'", '
        . 'service_date="'.clean($_POST['new_service_date']).'", '
        . 'status="'.clean($_POST['new_service_status']).'", '
        . 'sr_form="'.clean($_POST['sr_form']).'", '
//        . 'service_no="'.clean($service_no).'",'
        . 'service_executor_id='.$service_executor.', '
        . 'ETA='.$ETA.', '
        . 'ETD='.$ETD.', '
        . 'PO="'.clean($_POST['new_PO']).'",'
        . 'PO2="'.clean($_POST['new_PO2']).'",'
        . 'request="'.clean($_POST['request']).'",'
        . 'agent="'.($_POST['service_agent']).'",'
        . 'agent_contact_id='.$agent_contact.', '
        . 'passes_status="'.($_POST['passes_status']).'",'
        . 'service_note="'.clean($_POST['service_note']).'",'
        . 'location="'.clean($_POST['new_location']).'",'
        . 'vessel_id="'.clean($_POST['new_vessel']).'",'
        . 'invoice="'.$invoice.'",'
        . 'srv_pay_type="'.clean($_POST['srv_pay_type']).'", '
        . 'description="'.clean($_POST['new_service_description']).'", '
        . 'equipment="'.clean($_POST['equipment']).'", '
        . 'service_total='.$service_total.', '
        . 'service_currency="'.clean($_POST['service_currency']).'", '
        . 'srv_inv_comp_name="'.clean($_POST['srv_inv_comp_name']).'", '
        . 'srv_inv_comp_name2="'.clean($_POST['srv_inv_comp_name2']).'", '
        . 'srv_inv_addr1="'.clean($_POST['srv_inv_addr1']).'", '
        . 'srv_inv_addr2="'.clean($_POST['srv_inv_addr2']).'", '
        . 'srv_our_bank_details="'.(int)clean($_POST['srv_our_bank_details']).'", '
        . 'srv_pay_terms="'.clean($_POST['srv_pay_terms']).'", '
        . 'srv_inv_number="'.clean($_POST['srv_inv_number']).'", '
        . 'srv_cn_number="'.clean($_POST['srv_cn_number']).'", '
        . 'srv_inv_email="'.clean($_POST['srv_inv_email']).'", '
        . 'srv_inv_country="'.clean($_POST['srv_inv_country']).'", '
        . 'srv_inv_vat="'.clean($_POST['srv_inv_vat']).'", '
        . 'srv_inv_from='.$srv_inv_from.', '
        . 'srv_inv_date='.$srv_inv_date.', '
        . 'inv_instructions='.$inv_instructions.', '
        . 'srv_cn_flag='.$srv_cn_flag.', '
        . 'modified="'.$_SESSION['uid'].'" '
        . 'WHERE service_id="'.$service_id.'"';
$result=$db->query($query);
if (!$result){
    echo '<p>FAILED insert service</p>',$db->error;
    $db->rollback();
    exit();
}
if (isset($_POST['users'])){
    //Delete old records
    $db->query('DELETE from service_users where su_service_id="'.$service_id.'"');
    //Insert new
    $query='insert into service_users values';
    foreach ($_POST['users'] as $value) {
        $query.='(DEFAULT,'.$service_id.','.$value.'),';
    }
    $query=substr($query, 0, -1);
    if (!$db->query($query)){
        echo '<p><font color="red">FAILED insert users:</font></p>'.$db->error;
        $db->rollback();
        $db->close();
        exit();
    }
}

//РАбота с ENTRIES
//Очистка старых
if(!$db->query('DELETE FROM service_calc_entries WHERE entry_related_id="'.$service_id.'"')){
    echo 'Unable to clear previous records.<br>Error: ',$db->error;
    $db->rollback();
    $db->close();
    exit();            
}
$length= count($_POST['entry_type']);
for ($i=0;$i<$length;$i++){
    $price=($_POST['entry_price'][$i]=='')?0:clean($_POST['entry_price'][$i]);
    $base_id=($_POST['entry_base_id'][$i]=='')?0:clean($_POST['entry_base_id'][$i]);
    $query='INSERT INTO service_calc_entries SET '
            . 'entry_type="'.clean($_POST['entry_type'][$i]).'",'
            . 'entry_text="'.clean($_POST['entry_text'][$i]).'",'
            . 'entry_qty="'.clean($_POST['entry_qty'][$i]).'",'
            . 'entry_discount="'.clean($_POST['entry_discount'][$i]).'",'
            . 'entry_price="'.$price.'",'
            . 'entry_base_id="'.$base_id.'",'
            . 'entry_related_id="'.$service_id.'"';
    if(!$db->query($query)){
        echo $query;
        echo 'Unable to set new entries.<br>Error: ',$db->error;
        $db->rollback();
        $db->close();
        exit(); 
    }
}

//Работа с Fault descriptions
if(!$db->query('DELETE FROM service_fault_descr WHERE sfd_serv_id="'.$service_id.'"')){
    echo 'Unable to clear previous records.<br>Error: ',$db->error;
    $db->rollback();
    $db->close();
    exit();            
}
$length=count($_POST['sfd_equip_id']);
for ($i=0;$i<$length;$i++){
    $query='INSERT INTO service_fault_descr SET '
            . 'sfd_equip_id="'.clean($_POST['sfd_equip_id'][$i]).'",'
            . 'sfd_equip_comment="'.clean($_POST['sfd_equip_comment'][$i]).'",'
            . 'sfd_descr="'.clean($_POST['sfd_descr'][$i]).'",'
            . 'sfd_type="'.(int)clean($_POST['sfd_type'][$i]).'",'
            . 'sfd_serv_id="'.$service_id.'"';
    if(!$db->query($query)){
        echo $query;
        echo 'Unable to set new equipment.<br>Error: ',$db->error;
        $db->rollback();
        $db->close();
        exit(); 
    }
}
//Все прошло успешо
        
$db->commit();
$db->close();
if (isset($_POST['return-path']))exit('true');
header('Location: /service.php');