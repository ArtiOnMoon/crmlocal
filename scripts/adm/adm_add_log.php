<?php
require_once '../../functions/main.php';
require_once '../../functions/db.php';
require_once '../../functions/auth.php';
require_once '../../classes/Adm.php';
require_once '../../classes/Order_name_engine.php';

security();

$db = db_connect();

$query = 'INSERT INTO administrative_logs SET '
        . 'order_id = "'.clean($_POST['id']).'", '
        . 'text = "'.clean($_POST['text']).'", '
        . 'user = "'.$_SESSION['uid'].'"';
if ($db->query($query)){
    exit(json_encode(array('result' => 'true')));
} else{
    exit(json_encode(array('result' => 'false', 'error' => $db->error)));
}

