<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
startSession();
security ();
if(check_access('acl_purchase', 1)) exit('Access denied.');
$id=$_POST['file_id'];
$downloaddir='./purchase_files/'.$id.'/'; 

//Скачивание файла
if($_POST['file_action']==='download'){
file_force_download($downloaddir.$_POST['file_name']);
}
//Удаление файла
if($_POST['file_action']==='delete'){
    unlink($downloaddir.$_POST['file_name']);
    //запись в бд об изменении
    $db =  db_connect();
    $query= 'update purchase set modified="'.$_SESSION['valid_user'].'", modified_date=CURRENT_TIMESTAMP where id="'.$id.'"';
    $db->query($query);
    //переход назад
    header('Location: /view_purchase.php?purchase_id='.$id);
}