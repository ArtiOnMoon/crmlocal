<?php
require_once 'functions/fns.php';
if ($_POST['message_receiver']==$_SESSION['valid_user']) {
    echo '<font color="red">You trying send message to yourself</font>';    exit();
}
    $db =  db_connect();
    $subject=($_POST['message_subject']=='' ? 'No subject' : $_POST['message_subject']);
    $query= 'insert into messages set subject="'.$subject.'", '
            . 'content="'.$_POST['message_content'].'",'
            . ' date="'.  date('Y-m-d').'",'
            . ' receiver="'.$_POST['user'].'",'
            . ' sender="'.$_SESSION['valid_user']  .'"';
    If ($result=$db->query($query)) {echo 'Succsess';  $messgae_id=$db->insert_id;}
    else     echo 'Unsuccsess';

//Upload file
mkdir('upload_files/'.$messgae_id, 0777);
$uploaddir = 'upload_files/'.$messgae_id.'/';
$uploadfile = $uploaddir .$_FILES["userfile"]["name"]; 
echo $_FILES["userfile"]["name"].' '.$_FILES['userfile']['error'].'<br>';


if (copy($_FILES['userfile']['tmp_name'], $uploadfile))
{
echo "<h3>Файл успешно загружен на сервер</h3>";
$db =  db_connect();
$query= 'update messages set attachment="'.$_FILES["userfile"]["name"].'" where message_id = "'.$messgae_id.'"';
$result=$db->query($query);
}
else { echo "<h3>Ошибка! Не удалось загрузить файл на сервер!</h3>"; exit;}