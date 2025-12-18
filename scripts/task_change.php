<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../classes/Task.php';

startSession();
security ();
//Check dates

$id=clean($_POST['id']);
$task_from = clean($_POST['task_from']);
$db =  db_connect();
$db->autocommit(false);

$task = new Task();

try{
    $task ->load($db, $id);
} catch (Exception $e){
    $result_arr = array('result' => 'false', 'id' =>$id, 'error' => $e->getMessage());
    echo json_encode($result_arr);
    $db->rollback();
    $db->close();
    exit();
}

try{
    $task->update($db, $_POST);
} catch (Exception $e){
    $result_arr = array('result' => 'false', 'id' =>$id, 'error' => $e->getMessage());
    echo json_encode($result_arr);
    $db->rollback();
    $db->close();
    exit();
}

//Checklist
$checklists = json_decode($_POST['checklists']);
if (count($checklists)>0){
    try{ 
        $task -> checklist_init($db,$checklists ); }
    catch (Exception $e){
        $result_arr = array('result' => 'false', 'id' =>$id, 'error' => $e->getMessage());
        echo json_encode($result_arr);
        $db->rollback();
        $db->close();
        exit();
    }
}

//EVERYTHING is OK
$db->commit();
$db->close();
$result_arr = array('result' => 'true', 'id' => $id, 'error' => '');
echo json_encode($result_arr);