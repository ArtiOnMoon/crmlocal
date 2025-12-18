<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security ();
if (!access_check([],[],2)) exit ('Access denied');
$id=clean($_POST['id']);
if (clean($_POST['proforma_exist'])==='1')$query= 'UPDATE service_calculation SET ';
else $query= 'INSERT INTO service_calculation SET calc_id="'.$id.'", ';
$db = db_connect();
$db->autocommit(false);
$query.= 'calc_cust_id="'.clean($_POST['new_customer']).'", '
        . 'calc_currency="'.clean($_POST['currency']).'", '
        . 'calc_our_company="'.clean($_POST['our_company']).'", '
        . 'calc_our_ref="'.clean($_POST['calc_our_ref']).'", '
        . 'calc_your_ref="'.clean($_POST['calc_your_ref']).'", '
        . 'calc_total="'.(double)clean($_POST['proforma_total']).'", '
        . 'calc_note="'.clean($_POST['calc_note']).'", '
        . 'calc_pay_terms="'.clean($_POST['pay_terms']).'"';
if (clean($_POST['proforma_exist'])==='1')$query.= ' WHERE calc_id="'.$id.'"';
if(!$db->query($query)){
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();    
}


//проверка существования проформы
if (clean($_POST['proforma_exist'])==='1') {
    //очистка ENTRIES
    if(!$db->query('DELETE FROM service_calc_entries WHERE entry_related_id="'.$id.'"'))
        {
            echo 'Unable to clear previous records.<br>Error: ',$db->error;
            $db->rollback();
            $db->close();
            exit();            
    }
}
//вставка ENTRIES
$length= count($_POST['entry_type']);
for ($i=0;$i<$length;$i++){
    $query='INSERT INTO service_calc_entries SET '
            . 'entry_type="'.clean($_POST['entry_type'][$i]).'",'
            . 'entry_text="'.clean($_POST['entry_text'][$i]).'",'
            . 'entry_qty="'.clean($_POST['entry_qty'][$i]).'",'
            . 'entry_price="'.clean($_POST['entry_price'][$i]).'",'
            . 'entry_base_id="'.clean($_POST['entry_base_id'][$i]).'",'
            . 'entry_related_id="'.$id.'"';
    if(!$db->query($query)){
        echo $query;
        echo 'Unable to set new entries.<br>Error: ',$db->error;
        $db->rollback();
        $db->close();
        exit(); 
    }
}
//Все прошло успешо
$db->commit();
$db->close();
header('Location: /service_proforma2.php?saved=1&service_id='.$id);