<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';

startSession();
security();
if(check_access('acl_documents', 2)) exit('Access denied.');
if (isset($_POST['is_archive']))$is_archive=1; else $is_archive=0;
$db =  db_connect();
$query= 'update documents set '
        . 'type = "'.clean($_POST['doctype']).'", '
        . 'doc_name = "'.clean($_POST['doc_name']).'", '
        . 'doc_number = "'.clean($_POST['doc_number']).'", '
        . 'our_company = "'.clean($_POST['our_company']).'", '
        . 'start_date = "'.clean(strtotime($_POST['start_date'])).'", '
        . 'expire_date = "'.clean(strtotime($_POST['expire_date'])).'", '
        . 'alarm = "'.clean($_POST['alarm']).'", '
        . 'is_archive = "'.$is_archive.'", '
        . 'incharge = "'.clean($_POST['user']).'" '
        . 'where id="'.$_POST['id'].'"';
if ($db->query($query)){
    echo 'true';
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
}
$db->close();
