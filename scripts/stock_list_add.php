<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
startSession();
security ();
if(check_access('acl_stock', 2)) exit('Access denied.');
$db =  db_connect();
$query= 'insert into stock_list set '
        . 'stockl_name = "'.clean($_POST['stockl_name']).'", '
        . 'stockl_note="'.clean($_POST['stockl_note']).'", '
        . 'stockl_phone="'.clean($_POST['stockl_phone']).'",'
        . 'stockl_email="'.clean($_POST['stockl_email']).'",'
        . 'stockl_country="'.clean($_POST['stockl_country']).'",'
        . 'stockl_address="'.clean($_POST['stockl_address']).'"';
$result=$db->query($query);
if ($result) {
    header('Location: /stock_list.php');
}
else{
    echo '<p>FAILED</p>'; echo $db->error;
}