<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
startSession();
security ();
if (!access_check([],[],1)) exit ('Access denied');
$cust_id=$_GET['id'];
echo $cust_id;
$db =  db_connect();
$query='update customers set deleted=1 where cust_id='.$cust_id;
$db->query($query);
