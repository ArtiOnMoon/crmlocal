<?php
require_once 'functions/fns.php';
startSession();
security();
if(check_access('acl_cust', 2)) exit('Access denied.');

$customer_id=clean($_POST['customer_id']);
$department=clean($_POST['new_department']);
$name=clean($_POST['new_name']);
$phone=clean($_POST['new_phone']);
$mob=clean($_POST['new_mob']);
$email=clean($_POST['new_email']);
$position=clean($_POST['new_position']);
$note=clean($_POST['new_note']);

$db =  db_connect();
$query= 'insert into customers_contacts set '
    . 'customer_id = "'.$customer_id.'", '
    . 'name = "'.$name.'", ' 
    . 'phone="'.$phone.'", '
    . 'department="'.$department.'", '
    . 'mob="'.$mob.'", '
    . 'position="'.$position.'", '
    . 'note="'.$note.'", '
    . 'email="'.$email.'"';
$result=$db->query($query);
if ($result) echo 'true'; else {
    echo '<font color="red">Problem: </font>'.$db->error;
    echo '<a href="view_customer.php?cust_id='.$_POST['customer_id'].'">Return</a>';
}



