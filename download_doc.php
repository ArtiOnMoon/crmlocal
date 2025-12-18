<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'PATHS.php';
startSession();
security ();
$id=$_POST['id'];
settype($id, "integer");
//Скачивание файла
if(isset($_POST['download_button'])){
    file_force_download($uploads_folder.'uploaded_documents/'.$id.'/'.$_POST['file']);
}

//Удаление файла
if(isset($_POST['delete_button'])){
    $db =  db_connect();
    $query= 'select file from documents where id="'.$_POST['id'].'"';
    $result=$db->query($query);
    if ($result-> num_rows!==1){ exit('File not found');}
    $row=$result->fetch_assoc();
    $query= 'update documents set '
        . 'file = "*NO_FILE*" '
        . 'where id="'.$_POST['id'].'"';
////////////////////////
    $file=$uploads_folder."uploaded_documents/".$id.'/'.$row['file'];
    $db->autocommit(false);
    if($db->query($query)) {
        if (unlink ($file)) {
            $db->commit();
            exit('true');
        }
        else {
            $db->rollback();
            $db->close();
            exit('Error. Unable to delete file.');
        }
    }
    else echo $db->error;
    header('Location: /view_doc.php?id='.$id);
}