<?php
require_once 'functions/fns.php';
require_once 'PATHS.php';
startSession();
security();
if(check_access('acl_purchase', 2)) exit('Access denied.');
$id=clean($_POST['id']);
settype($id, "integer");
$db =  db_connect();
$query= 'delete from documents where id="'.$id.'"';
if ($db->query($query)) {
   $dir=$uploads_folder."uploaded_documents\\".$id.'\\'; 
   array_map('unlink', glob("$dir/*.*"));
   rmdir($dir);
   header('Location: /view_purchase.php?id='.$id);
}
else echo '<p><font color="red">FAILED:</font></p>'.$db->error;
