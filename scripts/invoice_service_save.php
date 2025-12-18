<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security ();

if (!access_check([],[],2)) exit ('Access denied');
$id=clean($_POST['id']);
$db =  db_connect();
$db->autocommit(false);
$query= 'update invoice_service set ';
$query.= 'inv_srv_customer="'.clean($_POST['new_customer']).'", '
        . 'inv_srv_po="'.clean($_POST['inv_srv_po']).'", '
        . 'inv_srv_num="'.clean($_POST['inv_srv_num']).'", '
        . 'inv_srv_currency="'.clean($_POST['inv_srv_currency']).'", '
        . 'inv_srv_our_comp="'.(integer)clean($_POST['inv_srv_our_comp']).'", '
        . 'inv_srv_our_ref="'.clean($_POST['inv_srv_our_ref']).'", '
        . 'inv_srv_your_ref="'.clean($_POST['inv_srv_your_ref']).'", '
        . 'inv_srv_status="'.(integer)clean($_POST['invoice_status']).'", '
        . 'inv_srv_note="'.clean($_POST['inv_srv_note']).'", '
        . 'inv_srv_date="'.clean($_POST['inv_srv_date']).'", '
        . 'inv_srv_total="'.(double)clean($_POST['inv_srv_total']).'", '
        . 'inv_srv_pay_terms="'.clean($_POST['inv_srv_pay_terms']).'"';
$query.= ' where inv_srv_id="'.$id.'"';
if(!$db->query($query)){
    echo 'Error updating invoice data.<br>Error: ',$db->error;
    $db->rollback();
    $db->close();
    exit();    
}

//RATES
//очистка rates
if(!$db->query('delete from invoice_service_rates where invsr_inv_id="'.$id.'"')){
    echo 'Unable to clear previous rates.<br>Error: ',$db->error;
    $db->rollback();
    $db->close();
    exit();            
}
//вставка новых rates
if ($_POST['rates']!=='NULL'){
    $rates= json_decode($_POST['rates']);
    foreach ($rates as $rate){
        $query='insert into invoice_service_rates set '
                . 'invsr_inv_id = "'.$id.'",'
                . 'invsr_inv_rate_id = "'.mysqli_real_escape_string($db,$rate[2]).'",'
                . 'invsr_inv_rate_qnt = "'.mysqli_real_escape_string($db,$rate[0]).'",'
                . 'invsr_inv_rate_price = "'.mysqli_real_escape_string($db,$rate[1]).'"';
        if(!$db->query($query)){
            echo 'Unable to insert new rate.<br>Error: ',$db->error;;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}

//SPARES
    //очистка items
if(!$db->query('delete from invoice_service_items where invsi_inv_id="'.$id.'"')){
    echo 'Unable to clear previous spares.<br>Error: ',$db->error;
    $db->rollback();
    $db->close();
    exit();            
}
if ($_POST['spare']!=='NULL'){
    $spares= json_decode($_POST['spare']);
    foreach ($spares as $sp){
        $query='insert into invoice_service_items set '
                . 'invsi_inv_id = "'.$id.'",'
                . 'invsi_pn = "'.mysqli_real_escape_string($db,$sp[0]).'",'
                . 'invsi_descr = "'.mysqli_real_escape_string($db,$sp[1]).'",'
                . 'invsi_qnt = "'.mysqli_real_escape_string($db,$sp[2]).'",'
                . 'invsi_price = "'.mysqli_real_escape_string($db,$sp[3]).'",'
                . 'invsi_type = "'.mysqli_real_escape_string($db,$sp[4]).'"';
        if(!$db->query($query)){
            echo 'Unable to insert new item.<br>Error: ',$db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}


//Все прошло успешо
$db->commit();
$db->close();
header('Location: /invoice_service_view.php?saved=1&invoice_id='.$id);