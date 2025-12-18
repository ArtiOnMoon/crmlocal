<?php
require_once '../../functions/main.php';
require_once '../../functions/db.php';
require_once '../../functions/auth.php';
require_once '../../classes/Adm.php';
require_once '../../classes/Order_name_engine.php';

$db = db_connect();

$adm = new Adm();
$adm->modified_by = $_SESSION['uid'];

try{
    $adm->set($_POST);
    $adm -> init($db);
} catch (Exception $ex){
    exit(json_encode(array('result' => 'false', 'error' =>$ex->getMessage())));
}
exit(json_encode(array('result' => 'true')));