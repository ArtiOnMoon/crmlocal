<?php
require_once 'functions/fns.php';
require_once 'PATHS.php';
startSession();
security();
if(check_access('acl_documents', 2)) exit('Access denied.');
//Проверка загрузки файла
if ($_FILES['userfile']['tmp_name']===''){
    echo('File error.');
    exit ('<meta http-equiv="refresh" content="5;/doc_control.php">');
}

$db =  db_connect();
$query= 'insert into documents set '
        . 'type = "'.clean($_POST['doctype']).'", '
        . 'doc_name = "'.clean($_POST['doc_name']).'", '
        . 'doc_number = "'.clean($_POST['doc_number']).'", '
        . 'our_company = "'.clean($_POST['our_company']).'", '
        . 'start_date = "'.clean(strtotime($_POST['start_date'])).'", '
        . 'expire_date = "'.clean(strtotime($_POST['expire_date'])).'", '
        . 'alarm = "'.clean($_POST['alarm']).'", '
        . 'incharge = "'.clean($_POST['user']).'"';
if ($db->query($query)){
    $id=$db->insert_id;
    $uploaddir=$uploads_folder.'/uploaded_documents/'.$id; 
    mkdir($uploaddir,0777,true); 
    //echo '<meta http-equiv="refresh" content="2;/doc_control.php">';
}
else {
    echo'<font color="red">Problem: </font>'.$db->error;    
    exit();
}
//отправка файла
if (move_uploaded_file($_FILES['userfile']['tmp_name'],$uploaddir.'/'.$_FILES['userfile']['name']))
{
    $query='update documents set file="'.$_FILES['userfile']['name'].'" where id ='.$id;
    if(!$db->query($query)) echo $db->error;
    print "File is valid, and was successfully uploaded.";
    echo '<meta http-equiv="refresh" content="2;/doc_control.php">';
} else {
    print "File wasn't uploaded!";
    echo '<meta http-equiv="refresh" content="5;/doc_control.php">';
}
//конец отправки файла

