<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
startSession();
security ();
if(check_access('acl_purchase', 2)) exit('Access denied.');
$id=$_POST['purchase_id'];
$uploaddir='./purchase_files/'.$id.'/'; 
$files = $_FILES['purchase_files'];
$symbols=['-',',','\'','!','$','(',')'];
//проверка и создание папки
if (!is_dir($uploaddir))
{
    mkdir($uploaddir,0777,true);
}
//Загрузка файлов
if(!empty($files))
{
    $files_desc = reArrayFiles($files);
    foreach($files_desc as $file){
        clean_file_name($file['name']);
        move_uploaded_file($file['tmp_name'], $uploaddir.$file['name']);
    }   
}
//Преобразование массива файлов
function reArrayFiles($file)
{
    $file_ary = array();
    $file_count = count($file['name']);
    $file_key = array_keys($file);
    
    for($i=0;$i<$file_count;$i++)
    {
        foreach($file_key as $val)
        {
            $file_ary[$i][$val] = $file[$val][$i];
        }
    }
    return $file_ary;
}
//запись в бд об изменении
$db =  db_connect();
$query= 'update purchase set modified="'.$_SESSION['valid_user'].'", modified_date=CURRENT_TIMESTAMP where id="'.$id.'"';
$db->query($query);
header('Location: /view_purchase.php?purchase_id='.$id);