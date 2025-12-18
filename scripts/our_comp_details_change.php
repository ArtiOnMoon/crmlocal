<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
startSession();
security();
if(check_access('acl_invoices', 2)) exit('Access denied.');
$db =  db_connect();
$db->autocommit(false);
//ЗАМЕНА ИМЕНИ
$query='UPDATE our_details SET '
        . 'name="'.clean($_POST['name']).'", pay_comment="'.clean($_POST['pay_comment']).'" '
        . 'WHERE id="'.clean($_POST['id']).'"';
if(!$db->query($query)){
    $db->rollback();
    exit($db->error);
}
//Очистка старых реквизитов
$query='DELETE FROM our_details_sub WHERE details_id="'.clean($_POST['id']).'"';
if(!$db->query($query)){
    $db->rollback();
    exit($db->error);
}
//Вставка новых реквизитов
for($i=0;$i<count($_POST['param_name']);$i++){
    $query='INSERT INTO our_details_sub SET '
            . 'details_id = "'.clean($_POST['id']).'",'
            . 'param_name = "'.clean($_POST['param_name'][$i]).'",'
            . 'param_value = "'.clean($_POST['param_value'][$i]).'"';
    if(!$db->query($query)) {
        $db->rollback();
        exit($db->error);
    }
}
$db->commit();
header('Location: /our_companies_view.php?id='.$_POST['our_comp_id']);