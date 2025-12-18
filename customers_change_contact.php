<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_cust', 2)) exit('Access denied.');
if (!isset($_POST['deleted'])) $deleted=0; else $deleted=1;

$db =  db_connect();
$query= 'update customers_contacts set '
        . 'name = "'.clean($_POST['new_name']).'", ' 
        . 'position="'.clean($_POST['new_position']).'", '
        . 'department="'.clean($_POST['new_department']).'", '
        . 'email="'.clean($_POST['new_email']).'", '
        . 'note="'.clean($_POST['new_note']).'", '
        . 'phone="'.clean($_POST['new_phone']).'", '
        . 'deleted="'.$deleted.'", '
        . 'mob="'.clean($_POST['new_mob']).'" '
        . 'where id="'.clean($_POST['id']).'"';
if ($db->query($query)) {
    exit('true');
}
else {
    exit ('<p><font color="red">FAILED:</font></p>'.$db->error);
}
?>

