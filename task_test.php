<?php
require_once './classes/Task.php';
require_once './functions/db.php';

$db = db_connect();
$db->autocommit(false);

$task = new Task();
try {
    $task->load($db,3);
    print_r ($task);
    $task->subject='876543218';
    $task->save($db);
//    $task->init2(['id'=>3, 'priority'=>2]);
} catch (Exception $exc) {
    echo $exc->getMessage();
    return;
}
$db->commit();
$db->close();
//print_r ($task);