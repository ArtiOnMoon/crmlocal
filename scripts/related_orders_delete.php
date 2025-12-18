<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
startSession();
security ();

if(check_access('acl_purchase', 2)) exit('Access denied.');
$id=clean($_POST['id']);
$db =  db_connect();
$query= 'DELETE FROM cross_docs WHERE id="'.$id.'"';
if(!$result=$db->query($query)){
    echo $db->error;
    exit();
}
echo 'Successfully deleted';