<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
startSession();
if (isset($_POST['delete']))$deleted=1; else $deleted=0;
$id=$_POST['id'];
$db =  db_connect();
$query= 'update our_companies set '
        . 'our_name = "'.clean($_POST['our_name']).'", '
        . 'our_full_name = "'.clean($_POST['our_full_name']).'", '
        . 'our_vat = "'.clean($_POST['our_vat']).'", '
        . 'our_fact_addr = "'.clean($_POST['our_fact_addr']).'", '
        . 'our_inv_addr = "'.clean($_POST['our_inv_addr']).'", '
        . 'our_fact_addr2 = "'.clean($_POST['our_fact_addr2']).'", '
        . 'our_inv_addr2 = "'.clean($_POST['our_inv_addr2']).'", '
        . 'our_mail = "'.clean($_POST['our_mail']).'", '
        . 'our_deleted = "'.$deleted.'" '
        . 'where id="'.$id.'"';
$result=$db->query($query);
if ($result) {
    header('Location: /our_companies.php');
}
else{
    echo '<p>FAILED</p>'; echo $db->error;
}