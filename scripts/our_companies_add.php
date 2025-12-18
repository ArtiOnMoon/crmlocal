<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
startSession();
security ();

$db =  db_connect();
$query= 'insert into our_companies set '
        . 'our_name = "'.clean($_POST['our_name']).'", '
        . 'our_full_name = "'.clean($_POST['our_full_name']).'", '
        . 'our_vat = "'.clean($_POST['our_vat']).'", '
        . 'our_fact_addr = "'.clean($_POST['our_addr']).'", '
        . 'our_inv_addr = "'.clean($_POST['our_inv_addr']).'", '
        . 'our_fact_addr2 = "'.clean($_POST['our_addr2']).'", '
        . 'our_inv_addr2 = "'.clean($_POST['our_inv_addr2']).'", '
        . 'our_mail = "'.clean($_POST['our_mail']).'"';
$result=$db->query($query);
$sale_id=$db->insert_id;
if ($result)  header('Location: /our_companies.php');
else {
    echo '<font color="red">Problem: </font>'.$db->error;
}

echo $db->error;
