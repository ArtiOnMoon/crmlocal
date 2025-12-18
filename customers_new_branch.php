<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_cust', 2)) exit('Access denied.');
$db =  db_connect();
$query= 'insert into customers_branches set '
        . 'customer = "'.clean($_POST['customer']).'", '
        . 'branch_name = "'.clean($_POST['branch_name']).'", '
        . 'branch_address = "'.clean($_POST['branch_address']).'", '
        . 'branch_email = "'.clean($_POST['branch_email']).'", '
        . 'branch_phone = "'.clean($_POST['branch_phone']).'", '
        . 'branch_note = "'.clean($_POST['branch_note']).'" ';
if ($db->query($query)){
    exit ('true');
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;   
}