<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/service.php';
startSession();
if (!access_check([],[],2)) exit ('Access denied');

$id=clean($_GET['service_id']);
$db =  db_connect();
//TRANSACTION START
$db->autocommit(false);
//Проверка существования проформы
$query= 'select proforma_id from service_proforma where proforma_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1){
    $db->rollback();
    $db->close();
    exit('Proforma not found.');
}
//Копирование проформы в инвойс
$query='insert into invoice_service ('
        . 'inv_srv_service_id,inv_srv_customer,inv_srv_our_comp,inv_srv_po,inv_srv_currency,inv_srv_our_ref,inv_srv_your_ref,inv_srv_pay_terms,inv_srv_total,inv_srv_orig_note) '
        . 'select proforma_id,comp_id,proforma_our_company,po,currency,our_ref,your_ref,pay_terms,proforma_total,proforma_note from service_proforma where proforma_id = "'.$id.'"';
if(!$db->query($query)){
    Echo 'Error sending proforma.<br>';
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
}
$invoice_id = $db->insert_id;
//Коприрование Rates
$query='select spr_rate_id,spr_rate_qnt,spr_rate_price from service_proforma_rates where spr_proforma_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==0){
    while($rates=$result->fetch_row()){
        $query= 'insert into invoice_service_rates (invsr_inv_id,invsr_inv_rate_id,invsr_inv_rate_qnt,invsr_inv_rate_price) VALUES("'.$invoice_id.'", "'.$rates[0].'","'.$rates[1].'","'.$rates[2].'")';
        if(!$db->query($query)){
            echo 'Error inserting rates.<br>';
            echo $db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}
//Коприрование Items
$query='select spi_type,spi_pn,spi_descr,spi_price,spi_qnt,spi_serial from service_proforma_items where spi_proforma_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==0){
    while($items=$result->fetch_row()){
        $query= 'insert into invoice_service_items (invsi_inv_id,invsi_type,invsi_pn,invsi_descr,invsi_price,invsi_qnt,invsi_serial) '
                . 'VALUES("'.$invoice_id.'", "'.$items[0].'","'.$items[1].'","'.$items[2].'","'.$items[3].'","'.$items[4].'","'.$items[5].'")';
        if(!$db->query($query)){
            echo 'Error inserting items.<br>';
            echo $db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}
$db->query('update service_proforma set proforma_on_sign=1 where proforma_id="'.$id.'"');
$db->commit();
$db->close();
header('Location: /service.php?service_id='.$id);