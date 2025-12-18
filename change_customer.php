<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_cust', 2)) exit('Access denied.');
$cust_id=$_POST['company_id'];
if(isset($_POST['addres_check'])){
    $new_invoicing_address=clean($_POST['new_address']);
    $new_invoicing_address2=clean($_POST['new_address2']);
}
else {
    $new_invoicing_address= clean($_POST['new_invoicing_address']);
    $new_invoicing_address2= clean($_POST['new_invoicing_address2']);
}

$db =  db_connect();
$query= 'update customers set ';
    if (isset($_POST['is_mngr']))$query.= 'is_mngr=1, '; else $query.= 'is_mngr=0, ';
    if (isset($_POST['is_serv']))$query.= 'is_serv=1, '; else $query.= 'is_serv=0, ';
    if (isset($_POST['is_sppl']))$query.= 'is_sppl=1, '; else $query.= 'is_sppl=0, ';
    if (isset($_POST['is_mnfr']))$query.= 'is_mnfr=1, '; else $query.= 'is_mnfr=0, ';
    if (isset($_POST['is_ownr']))$query.= 'is_ownr=1, '; else $query.= 'is_ownr=0, ';
    if (isset($_POST['is_agnt']))$query.= 'is_agnt=1, '; else $query.= 'is_agnt=0, ';
    if (isset($_POST['is_fchk']))$query.= 'is_fchk=1, '; else $query.= 'is_fchk=0, ';
    if (isset($_POST['is_optr']))$query.= 'is_optr=1, '; else $query.= 'is_optr=0, ';
    $query.= 'cust_short_name = "'.clean($_POST['new_comp_short_name']).'", ' 
    . 'cust_full_name="'.clean($_POST['new_company_full_name']).'", '
    . 'customer_status="'.clean($_POST['customer_status']).'", '    
    . 'vat="'.clean($_POST['vat']).'", '
    . 'client_of="'.clean($_POST['client_of']).'", '
    . 'country="'.clean($_POST['country']).'", '  
    . 'address="'.clean($_POST['new_address']).'", '
    . 'address2="'.clean($_POST['new_address2']).'", '
    . 'email="'.clean($_POST['new_email']).'", '
    . 'email2="'.clean($_POST['email2']).'", '
    . 'email3="'.clean($_POST['email3']).'", '
    . 'website="'.clean($_POST['new_website']).'", '
    . 'contact_phone="'.clean($_POST['new_contact_phone']).'", '
    . 'add_phone="'.clean($_POST['new_add_phone']).'", '
    . 'fax="'.$_POST['new_fax'].'", '
    . 'discount="'.clean($_POST['discount']).'", '
    . 'service_discount="'.clean($_POST['service_discount']).'", '
    . 'payment_terms="'.clean($_POST['payment_terms']).'", '
    . 'credit_limit="'.clean($_POST['credit_limit']).'", '
    . 'note="'.clean($_POST['new_note']).'", '
    . 'InvoicingAddress="'.clean($new_invoicing_address).'", '
    . 'modified_by="'.$_SESSION['uid'].'", '
    . 'InvoicingAddress2="'.clean($new_invoicing_address2).'" '
    . 'where cust_id="'.$cust_id.'"';
$result=$db->query($query);
if (!$result) echo '<p>FAILED</p>'.$db->error; else {
    if ($_POST['return-path']==='window')   echo 'true';
    else header('Location: /customers.php');
}