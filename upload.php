<?php
require_once 'functions/db.php';
if(check_access('acl_service', 2)) exit('Access denied.');
$uploaddir = 'service/'.$_POST[service_upload_id].'/';
$uploadfile = $uploaddir .'service_report.pdf'; 
echo $_FILES["userfile"]["tmp_name"].' '.$_POST[service_upload_id].' '.$_FILES['userfile']['error'].'<br>';

if (copy($_FILES['userfile']['tmp_name'], $uploadfile))
{
echo "<h3>Файл успешно загружен на сервер</h3>";
$db =  db_connect();
$query= 'update service set report="1" where service_id = "'.$_POST[service_upload_id].'"';
$result=$db->query($query);
}
else { echo "<h3>Ошибка! Не удалось загрузить файл на сервер!</h3>"; exit;}

// Выводим информацию о загруженном файле:
//echo "<h3>Информация о загруженном на сервер файле: </h3>";
//echo "<p><b>Оригинальное имя загруженного файла: ".$_FILES['userfile']['name']."</b></p>";
//echo "<p><b>Mime-тип загруженного файла: ".$_FILES['userfile']['type']."</b></p>";
//echo "<p><b>Размер загруженного файла в байтах: ".$_FILES['userfile']['size']."</b></p>";
//echo "<p><b>Временное имя файла: ".$_FILES['uploadfile']['userfile']."</b></p>";
?>