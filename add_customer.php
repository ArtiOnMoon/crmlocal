<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
startSession();
security ();
if(check_access('acl_cust', 2)) exit('Access denied.');

$new_invoicing_address=clean($_POST['new_invoicing_address']);
$new_invoicing_address2=clean($_POST['new_invoicing_address2']);
$new_address=clean($_POST['new_address']);
$new_address2=clean($_POST['new_address2']);

if(isset($_POST['address_check'])){
    $new_invoicing_address=$new_address;
    $new_invoicing_address2=$new_address2;
}

$db =  db_connect();
$bank= mysqli_real_escape_string($db,$_POST['bank_details']);
$db->autocommit(false);
$query= 'insert into customers set ';
    if (isset($_POST['is_mngr']))$query.= 'is_mngr=1, '; else $query.= 'is_mngr=0, ';
    if (isset($_POST['is_serv']))$query.= 'is_serv=1, '; else $query.= 'is_serv=0, ';
    if (isset($_POST['is_sppl']))$query.= 'is_sppl=1, '; else $query.= 'is_sppl=0, ';
    if (isset($_POST['is_mnfr']))$query.= 'is_mnfr=1, '; else $query.= 'is_mnfr=0, ';
    if (isset($_POST['is_ownr']))$query.= 'is_ownr=1, '; else $query.= 'is_ownr=0, ';
    if (isset($_POST['is_agnt']))$query.= 'is_agnt=1, '; else $query.= 'is_agnt=0, ';
    if (isset($_POST['is_optr']))$query.= 'is_optr=1, '; else $query.= 'is_optr=0, ';
    if (isset($_POST['is_fchk']))$query.= 'is_fchk=1, '; else $query.= 'is_fchk=0, ';
    $query.='cust_short_name = "'.clean($_POST['new_comp_short_name']).'", ' 
    . 'cust_full_name="'.clean($_POST['new_company_full_name']).'", '
    . 'address="'.clean($new_address).'", '
    . 'address2="'.clean($new_address2).'", '
    . 'vat="'.clean($_POST['vat']).'", '
    . 'InvoicingAddress="'.clean($new_invoicing_address).'", '
    . 'InvoicingAddress2="'.clean($new_invoicing_address2).'", '
    . 'client_of="'.clean($_POST['client_of']).'", '
    . 'note="'.clean($_POST['new_note']).'", '
    . 'website="'.clean($_POST['new_website']).'", '
    . 'country="'.clean($_POST['country']).'", '
    . 'email="'.clean($_POST['new_email']).'", '
    . 'email2="'.clean($_POST['email3']).'", '
    . 'email3="'.clean($_POST['email2']).'", '
    . 'customer_status="'.clean($_POST['customer_status']).'", '
    . 'fax="'.clean($_POST['new_fax']).'", '
    . 'contact_phone="'.clean($_POST['new_contact_phone']).'", '
    . 'add_phone="'.clean($_POST['new_add_phone']).'", '
    . 'discount="'.clean($_POST['discount']).'", '
    . 'service_discount="'.clean($_POST['service_discount']).'", '
    . 'payment_terms="'.clean($_POST['payment_terms']).'", '
    . 'modified_by="'.$_SESSION['uid'].'", '
    . 'credit_limit="'.clean($_POST['credit_limit']).'"';
if (!$db->query($query)) {
    if (isset($_POST['return-path'])){
        echo $db->error;
        $db->rollback();
        $db->close();
        exit();
    }
    echo '<font color="red">Problem: </font>'.$db->error;
    $db->rollback();
    $db->close();
    echo '<meta http-equiv="Refresh" content="5; url=/customers.php">';
    echo '<p>You will be redirected to previous page in 5 seconds...';
    exit();
}

//Проверка переданных клиентов
if (isset($_POST['cont_name'])){
    $customer_id=$db->insert_id;
    $count=count($_POST['cont_name']);
    for ($i=0; $i<$count;$i++){
        $query='insert into customers_contacts set '
            . 'department="'.clean($_POST['cont_department'][$i]).'", '
            . 'name="'.clean($_POST['cont_name'][$i]).'", '
            . 'email="'.clean($_POST['cont_email'][$i]).'", '
            . 'phone="'.clean($_POST['cont_phone'][$i]).'", '
            . 'mob="'.clean($_POST['cont_mob'][$i]).'", '
            . 'position="'.clean($_POST['cont_position'][$i]).'", '
            . 'note="'.clean($_POST['cont_note'][$i]).'", '
            . 'customer_id="'.$customer_id.'"';
        if (!$db->query($query)){
            echo $db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}
$db->commit();
$db->close();
if (isset($_POST['return-path']))exit('true');
header('Location: /customers.php'); 