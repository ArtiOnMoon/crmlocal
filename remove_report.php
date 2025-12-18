<?php
require_once 'functions/fns.php';
if(check_access('acl_service', 2)) exit('Access denied.');
db_connect();
$db =  db_connect();
$query= 'update service set report="0" where service_id = "'.$_POST['service_upload_id'].'"';
if($result=$db->query($query)) {header('Location: view_service.php?service_id='.$_POST['service_upload_id']); exit;}
echo 'Something wrong.';
