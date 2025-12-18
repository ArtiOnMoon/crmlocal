<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security();
if(check_access('acl_service', 2)) exit('Access denied.');
if (isset($_POST['users']))$users=implode(",", $_POST['users']);
else $users='';

if ($_POST['service_executor_id']!=1 and $_POST['service_executor_id']!='') {
    $service_executor=$_POST['service_executor_id'];
}else {$service_executor='NULL';}
if (isset($_POST['service_no'])) $service_no='"'.(int)clean($_POST['service_no']).'"'; ELSE $service_no='NULL';
if ($_POST['new_customer']=='') die ('Field Customer is empty');
if ($_POST['new_vessel']=='') die ('Field Vessel is empty');
if ($_POST['agent_contact_id']=='' OR $_POST['agent_contact_id']=='NULL') $agent_contact='NULL'; else $agent_contact='"'.clean($_POST['agent_contact_id']).'"';
if ($_POST['new_ETA']=='') $ETA='NULL'; else $ETA='"'.$_POST['new_ETA'].'"';
if ($_POST['new_ETD']=='') $ETD='NULL'; else $ETD='"'.$_POST['new_ETD'].'"';
if (isset($_POST['inv_instructions']))$inv_instructions=1; ELSE $inv_instructions=0;

$db =  db_connect();
$db->autocommit(false);

$query='insert into service set '
        . 'service_no='.$service_no.', '
        . 'comp_id="'.clean($_POST['new_customer']).'", '
        . 'modified="'.$_SESSION['uid'].'", '
        . 'service_date="'.clean($_POST['new_service_date']).'", '
        . 'sr_form="'.clean($_POST['sr_form']).'", '
        . 'invoice="'.clean($_POST['invoice']).'",'
        . 'srv_pay_type="'.clean($_POST['srv_pay_type']).'", '
        . 'description="'.clean($_POST['description']).'", '
        . 'service_our_comp="'.clean($_POST['service_our_comp']).'", '
        . 'equipment="'.clean($_POST['equipment']).'", '
        . 'service_executor_id='.$service_executor.', '
        . 'ETA='.$ETA.', '
        . 'ETD='.$ETD.', '
        . 'agent="'.clean($_POST['service_agent']).'", '
        . 'agent_contact_id='.$agent_contact.', '
        . 'request="'.clean($_POST['request']).'", '
        . 'PO="'.clean($_POST['new_PO']).'", '
        . 'service_note="'.clean($_POST['service_note']).'", '
        . 'status="'.clean($_POST['new_service_status']).'", '
        . 'location="'.clean($_POST['new_location']).'", '
        . 'inv_instructions='.$inv_instructions.', '
        . 'srv_inv_comp_name="'.clean($_POST['srv_inv_comp_name']).'", '
        . 'srv_inv_comp_name2="'.clean($_POST['srv_inv_comp_name2']).'", '
        . 'srv_inv_addr1="'.clean($_POST['srv_inv_addr1']).'", '
        . 'srv_inv_addr2="'.clean($_POST['srv_inv_addr2']).'", '
        . 'srv_inv_email="'.clean($_POST['srv_inv_email']).'", '
        . 'srv_inv_country="'.clean($_POST['srv_inv_country']).'", '
        . 'srv_inv_vat="'.clean($_POST['srv_inv_vat']).'", '
        . 'vessel_id="'.clean($_POST['new_vessel']).'"';
if (!$db->query($query)) {
    echo '<p><font color="red">FAILED:</font></p>'.$db->error;
    print_r($_POST);
    $db->rollback();
    $db->close();
    exit();
}
$id=$db->insert_id;
//Service_id into service_no
if(!isset($_POST['service_no'])){
    if($result=$db->query('SELECT MAX(service_no)AS max FROM service WHERE service_our_comp='.clean($_POST['service_our_comp']))){
        $row = $result->fetch_assoc();
        $db->query('UPDATE service SET service_no='.($row['max']+1).' WHERE service_id='.$id);
    } else {
        echo $db->error;
        $db->rollback();
        $db->close();
        exit();
    }
}
if(isset($_POST['users'])){
    $db->query('DELETE from service_users where su_service_id="'.$id.'"');
    $query='insert into service_users values';
    foreach ($_POST['users'] as $value) {
        $query.='(DEFAULT,'.$id.','.$value.'),';
    }
    $query=substr($query, 0, -1);
    if (!$db->query($query)){
        echo '<p><font color="red">FAILED:</font></p>'.$db->error;
        $db->rollback();
        $db->close();
        exit();
    }
}
//CALCULATION FORM
$rates_cat='SELECT rate_cat_id,rate_cat_name FROM service_rates_cat WHERE rate_our_comp="'.clean($_POST['service_our_comp']).'" ORDER BY rate_order';
$query='INSERT INTO service_calc_entries (entry_type, entry_text, entry_qty, entry_price, entry_related_id, entry_base_id) VALUES ';
$cats_list=$db->query($rates_cat);
while ($row = $cats_list->fetch_assoc()) {
    $query.='("3","'.$row['rate_cat_name'].'","0","0","'.$id.'","'.$row['rate_cat_id'].'"),'; 
}
$query.='("4","Port expenses:","1","20","'.$id.'",0),("4","Administrative fees:","1","20","'.$id.'",0)';
if (!$db->query($query)){
    echo '<p><font color="red">FAILED1:</font></p>'.$db->error;
    $db->rollback();
    $db->close();
    exit();
}

//Работа с Fault descriptions
if(!$db->query('DELETE FROM service_fault_descr WHERE sfd_serv_id="'.$id.'"')){
    echo 'Unable to clear previous records.<br>Error: ',$db->error;
    $db->rollback();
    $db->close();
    exit();            
}
$length=count((array)$_POST['sfd_equip_id']);
for ($i=0;$i<$length;$i++){
    $query='INSERT INTO service_fault_descr SET '
            . 'sfd_equip_id="'.clean($_POST['sfd_equip_id'][$i]).'",'
            . 'sfd_equip_comment="'.clean($_POST['sfd_equip_comment'][$i]).'",'
            . 'sfd_descr="'.clean($_POST['sfd_descr'][$i]).'",'
            . 'sfd_type="'.(int)clean($_POST['sfd_type'][$i]).'",'
            . 'sfd_serv_id="'.$id.'"';
    if(!$db->query($query)){
        echo $query;
        echo 'Unable to set new equipment.<br>Error: ',$db->error;
        $db->rollback();
        $db->close();
        exit(); 
    }
}    
    
//EVERYTHING IS OK
$db->commit();
$db->close();
exit('Service add successfully');
?>
