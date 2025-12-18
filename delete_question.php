<?php
require_once 'functions/fns.php';
startSession();
security();
$id=clean($_POST['id']);
$query= 'select user from questions where id="'.$id.'"';
$db =  db_connect();
$result=$db->query($query);
if (!$result){
    echo "Error: " . $db->error;
    exit();
}
if ($result-> num_rows===1){
$row=$result->fetch_assoc();
if (!access_check([''],[$row['valid_user'],'vk','kvv']))exit ('Access denied.');
$query= 'delete from questions where id="'.$id.'"';
if ($db->query($query)) {
   header('Location: /questions.php');
}
else {
    echo '<p><font color="red">FAILED:</font></p>'.$db->error;
}
}
else echo 'Nothing found';