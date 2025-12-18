<?php
require_once 'functions/fns.php';
require_once 'PATHS.php';
startSession();
security();
if(check_access('acl_documents', 2)) exit('Access denied.');
$id=$_POST['id'];
settype($id, "integer");
$db =  db_connect();
$uploaddir=$uploads_folder."uploaded_documents/".$id.'/';
if (!is_dir($uploaddir))
{
    mkdir($uploaddir,0777,true);
}

//отправка файла
$query='update documents set file="'.$_FILES['userfile']['name'].'" where id ='.$id;
$db->autocommit(false);
if(!$db->query($query)) {
    echo $db->error;
}
else
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$_FILES['userfile']['name'])) {
    $db->commit();
    $db->close();
    echo 'true';
} else {
    echo 'Error: ',$_FILES['userfile']['error'];
}
//конец отправки файла

