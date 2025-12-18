<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security ();
//Check dates

$id=clean($_POST['id']);
$task_from = clean($_POST['task_from']);
$db =  db_connect();
$db->autocommit(false);

$query= 'INSERT INTO task_history SET '
        . 'task_id="'.$id.'", '
        . 'is_read="0", '
        . 'type="0", '
        . 'user_id="'.$_SESSION['uid'].'", '
        . 'text="'.clean($_POST['text']).'"';
if (!$db->query($query)) {
    $result_arr = array('result' => 'false', 'id' =>'', 'error' => $db->error);
    echo json_encode($result_arr);
    $db->rollback();
    $db->close();
    exit();
}

$db->commit();
$db->close();
$result_arr = array('result' => 'true', 'id' => $id, 'error' => '');
echo json_encode($result_arr);