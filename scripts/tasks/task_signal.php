<?php
require_once('../../functions/db.php');
require_once('../../functions/auth.php');
require_once('../../functions/main.php');
require_once('../../classes/Task.php');

$task = new Task();
$db = db_connect();

$id = clean($_POST['id']);
$value = clean($_POST['value']);

if ($value == 1) {$status = 2;}
elseif ($value == 2) {$status=4;}
elseif ($value == 3) {$status=7;}

try {
   $task -> load($db, $id);
   $task -> update($db, ['status' => $status]);
} catch (Exception $ex) {
   $result_arr = array('result' => 'false', 'id' => $id, 'error' => $ex->getMessage());
   echo json_encode($result_arr);
   $db -> close();
   exit();
}

$db -> close();
$result_arr = array('result' => 'true', 'id' => $id, 'error' => '');
echo json_encode($result_arr);