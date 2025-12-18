<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../PATHS.php';
startSession();
security ();
$id=$_POST['file_id'];
$type=$_POST['file_type'];

$sub= intval((int)$id/1000);
$subdir=$uploads_folder.$type.'/'.$sub.'/';
$downloaddir=$subdir.$id.'/';
//Скачивание файла
if($_POST['file_action']==='download'){
    file_force_download($downloaddir.$_POST['file_name']);
}
//Удаление файла
if($_POST['file_action']==='delete'){
    unlink($downloaddir.$_POST['file_name']);
    //переход назад
    exit('true');
    header('Location: /view_'.$type.'.php?id='.$id);
}