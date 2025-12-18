<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
startSession();
security ();

$db =  db_connect();
$db->autocommit(false);
$query= 'insert into our_details set '
        . 'name="'.clean($_POST['name']).'",'
        . 'pay_comment="'.clean($_POST['pay_comment']).'",'
        . 'our_comp_id = "'.clean($_POST['id']).'"';
$result=$db->query($query);
if (!$result) exit($db->error);
$details_id=$db->insert_id;
for($i=0;$i<count($_POST['param_name']);$i++){
    $query='INSERT INTO our_details_sub SET '
            . 'details_id = "'.$details_id.'",'
            . 'param_name = "'.clean($_POST['param_name'][$i]).'",'
            . 'param_value = "'.clean($_POST['param_value'][$i]).'"';
    if(!$db->query($query)) {
        $db->rollback();
        exit($db->error);
    }
}
$db->commit();
header('Location: /our_companies_view.php?id='.$_POST['id']);