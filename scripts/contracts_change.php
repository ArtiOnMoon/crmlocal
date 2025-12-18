<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security ();
//if(check_access('acl_sales', 2)) exit('Access denied.');
//if (!access_check([],[],1)) exit ('Access denied');
$contract_id=clean($_POST['contract_id']);
$contract_num_flag=(isset($_POST['contract_num_flag']))?1:0;
$contract_date= ($_POST['contract_date']==='') ? 'Null':'"'.clean($_POST['contract_date']).'"';
$contract_expire= ($_POST['contract_expire']==='') ? 'Null':'"'.clean($_POST['contract_expire']).'"';

$db =  db_connect();
$db->autocommit(false);
$query= 'UPDATE contracts SET '
        . 'contract_our_num = "'.clean($_POST['contract_our_num']).'", '
        . 'contract_num = "'.clean($_POST['contract_num']).'", '
        . 'contract_our_num = "'.clean($_POST['contract_our_num']).'", '
        . 'contract_num_flag = "'.$contract_num_flag.'", '
        . 'contract_date = '.$contract_date.', '
        . 'contract_expire = '.$contract_expire.', '
        . 'contract_our_comp = "'.clean($_POST['contract_our_comp']).'", '
        . 'contract_customer = "'.clean($_POST['contract_customer']).'", '
        . 'contract_type = "'.clean($_POST['contract_type']).'", '
        . 'contract_status = "'.clean($_POST['contract_status']).'", '
        . 'contract_descr = "'.clean($_POST['contract_descr']).'", '
        . 'contract_currency = "'.clean($_POST['contract_currency']).'", '
        . 'contract_amount = "'.clean($_POST['contract_amount']).'", '
        . 'contract_note = "'.clean($_POST['contract_note']).'", '
        //. 'contract_incharge = "'.clean($_POST['contract_incharge']).'", '
        //. 'contract_deleted= "'.clean($_POST['contract_deleted']).'", '        
        . 'contract_modified = "'.$_SESSION['uid'].'" '
        . 'WHERE contract_id="'.$contract_id.'"';
$result=$db->query($query);
if (!$result) {
    echo $db->error;
    echo $query;
    $db->rollback();
    $db->close();
    exit();
}
$db->commit();
$db->close();
exit('true');
