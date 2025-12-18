<?php
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/main.php';
startSession();
security ();

if(check_access('acl_stock', 2)) exit('Access denied.');

$db =  db_connect();
$query= 'INSERT INTO stock_complects SET '
        . 'complect_name = "'.clean($_POST['complect_name']).'", '
        . 'complect_cat = '.clean($_POST['complect_cat']).', '
        . 'complect_maker = '.clean($_POST['complect_maker']).', '
        . 'complect_note = "'.clean($_POST['complect_note']).'"';
if ($db->query($query)){
    $db->close();
    exit ('true');
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
    $db->close();
}