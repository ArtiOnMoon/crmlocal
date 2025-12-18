<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../classes/Task.php';

startSession();
security();
if (!access_check([],[],1)) exit ('Access denied');

$db =  db_connect();
$db -> autocommit(false);

$task = new Task();

try{
    $task ->set($_POST);
    $task ->init($db);
}catch (Exception $e){
    $result_arr = array('result' => 'false', 'id' =>'', 'error' => $e->getMessage());
    echo json_encode($result_arr);
    $db->rollback();
    $db->close();
    exit();
}       

$db->commit();
$db->close();
$result_arr = array('result' => 'true', 'id' => $task->id, 'error' => '');
echo json_encode($result_arr);