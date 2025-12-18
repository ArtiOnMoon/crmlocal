<?php
//print_r($_POST);
//print_r($_FILES);
//exit();

require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../PATHS.php';
startSession();
security ();
if (!access_check([],[],2)) exit ('Access denied');
if ($_FILES['userfile']['name']==='') exit('No file was uploaded.');

$db =  db_connect();
$query= 'insert into docs_forms set '
        . 'name="'.clean($_POST['name']).'", '
        . 'description="'.clean($_POST['description']).'", '
        . 'date="'.date('Y-m-d').'", '
        . 'uploader="'.$_SESSION['valid_user'].'", '
        . 'cat="'.clean($_POST['cat']).'", '
        . 'modified_by="'.$_SESSION['valid_user'].'", '
        . 'file_name="'.$_FILES['userfile']['name'].'"';
if ($_POST['cat']=='1')$type='Forms';
elseif ($_POST['cat']=='2')$type='Documents/';
elseif ($_POST['cat']=='3')$type='Procedures/';
elseif ($_POST['cat']=='4')$type='Instructions/';
else exit('Something has gone wrong (unknown category)');
$uploaddir=$uploads_folder.'Documents/'.$type;
if (!file_exists($uploaddir)) mkdir($uploaddir,0777,true);
if ($db->query($query)){
    $id=$db->insert_id;
    $uploaddir=$uploaddir.$id.'/'; 
    mkdir($uploaddir,0777,true);
    if(move_uploaded_file($_FILES['userfile']['tmp_name'],$uploaddir.$_FILES['userfile']['name'])){
        echo 'File successfuly uploaded';
        echo '<meta http-equiv="refresh" content="2;/documents.php">';
    }
    else echo 'Something has gone wrong.';
}
else {
    echo'<font color="red">Problem: </font>'.$db->error;    
    exit();
}