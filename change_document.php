<?php
require_once 'functions/fns.php';
startSession();
security();
if(check_access('acl_documents', 2)) exit('Access denied.');
$db =  db_connect();
$query= 'update documents set '
        . 'type = "'.clean($_POST['doctype']).'", '
        . 'doc_name = "'.clean($_POST['doc_name']).'", '
        . 'start_date = "'.clean(strtotime($_POST['start_date'])).'", '
        . 'expire_date = "'.clean(strtotime($_POST['expire_date'])).'", '
        . 'alarm = "'.clean($_POST['alarm']).'", '
        . 'incharge = "'.clean($_POST['user']).'" '
        . 'where id="'.$_POST['id'].'"';

$db->query('LOCK TABLES documents WRITE');

if ($db->query($query)){
    $db->query('UNLOCK TABLES');
    header('Location: /doc_control.php');
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
}
$db->query('UNLOCK TABLES');
