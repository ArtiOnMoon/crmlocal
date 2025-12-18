<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security ();
//if(check_access('acl_sales', 2)) exit('Access denied.');

$db=db_connect();
$db->autocommit(false);


$contract_date=($_POST['contract_date']==='') ? 'Null':'"'.clean($_POST['contract_date']).'"';
$contract_expire = ($_POST['contract_expire']==='') ? 'Null':'"'.clean($_POST['contract_expire']).'"';
$contract_amount = ($_POST['contract_amount']==='') ? '0':''.clean($_POST['contract_amount']).'';
if (isset($_POST['contract_num_flag']))$contract_num_flag=1; ELSE $contract_num_flag=0;

$query= 'INSERT INTO contracts SET '
        . 'contract_num = "'.clean($_POST['contract_num']).'", '
        . 'contract_date = '.$contract_date.', '
        . 'contract_num_flag = '.$contract_num_flag.', '
        . 'contract_expire = '.$contract_expire.', '
        . 'contract_status = "'.clean($_POST['contract_status']).'", '
        . 'contract_type = "'.clean($_POST['contract_type']).'", '
        . 'contract_our_comp = "'.clean($_POST['contract_our_comp']).'", '
        . 'contract_customer = "'.clean($_POST['contract_customer']).'", '
        . 'contract_currency = "'.clean($_POST['contract_currency']).'", '
        . 'contract_amount = "'.$contract_amount.'", '
        . 'contract_descr = "'.clean($_POST['contract_descr']).'", '
        . 'contract_note = "'.clean($_POST['contract_note']).'", '
        . 'contract_modified = "'.$_SESSION['uid'].'"';
if (!$db->query($query)) {
    echo json_encode(array('result' => 'false','error'=>$db->error));
    $db->rollback();
    $db->close();
    exit();
}
$contract_id=$db->insert_id;

//Sales_id into sales_no
if ($_POST['contract_our_num']==''){
    if($result=$db->query('SELECT MAX(contract_our_num) AS max FROM contracts WHERE contract_our_comp = "'.clean($_POST['contract_our_comp']).'"')){
        if ($db->errno !==0){
            $db->rollback();
            $db->close();
            exit(json_encode(array('result' => 'false','error'=>$db->error)));
        }
        $row = $result->fetch_assoc();
        $db->query('UPDATE contracts SET contract_our_num='.($row['max']+1).' WHERE contract_id='.$contract_id);
        if ($db->errno !==0){
            $db->rollback();
            $db->close();
            exit(json_encode(array('result' => 'false','error'=>$db->error)));
        }
    } else {
        $db->rollback();
        $db->close();
        exit(json_encode(array('result' => 'false','error'=>$db->error)));
    }
}
$db->commit();
$db->close();
$arr = array('result' => 'true');
echo json_encode($arr);
exit();