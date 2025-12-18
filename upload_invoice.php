<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
startSession();
if(check_access('acl_invoices', 2)) exit('Access denied.');

$invoice_id=$_POST['invoice_id'];
$uploaddir='./invoice_files/'.$_POST['invoice_creation_year'].'/'.$invoice_id.'/';
$files = $_FILES['invoice_files'];
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
    $query= 'update invoices set invoice_modified="'.$_SESSION['valid_user'].'", invoice_modified_date=CURRENT_TIMESTAMP where invoice_id="'.$invoice_id.'"';
    $db->query($query);
    if (isset($db->error)) echo $db->error;
header('Location: /view_invoice.php?invoice_id='.$invoice_id);