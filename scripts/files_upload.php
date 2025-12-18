<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/files_func.php';
require_once '../PATHS.php';
startSession();
security ();
$id=$_POST['id'];
$type=$_POST['file_type'];

$uploaddir= get_file_folder($id, $type, $uploads_folder);
$files = $_FILES['service_files'];
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

exit('true');
header('Location: /view_'.$type.'.php?id='.$id);