<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_cust', 2)) exit('Access denied.');
if (!isset($_POST['deleted'])) $deleted=0; else $deleted=1;

$db =  db_connect();
$query= 'update customers_branches set '
        . 'branch_name = "'.clean($_POST['branch_name']).'", ' 
        . 'branch_email="'.clean($_POST['branch_email']).'", '
        . 'branch_note="'.clean($_POST['branch_note']).'", '
        . 'branch_phone="'.clean($_POST['branch_phone']).'", '
        . 'deleted="'.$deleted.'" '
        . 'where id="'.clean($_POST['id']).'"';
if ($db->query($query)) {
    echo('true');
}
else {
    exit ('<p><font color="red">FAILED:</font></p>'.$db->error);
}
?>

