<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security();
$db =  db_connect();
$query='UPDATE manufacturers SET '
        . 'mnf_short_name="'.clean($_POST['mnf_short_name']).'", '
        . 'mnf_full_name="'.clean($_POST['mnf_full_name']).'" '
        . 'WHERE mnf_id="'.clean($_POST['mnf_id']).'"';
if (!$db->query($query)){
    echo '<p><font color="red">FAILED:</font></p>'.$db->error;
    $db->close();
    exit();
}
header('Location: /manufacturers.php');