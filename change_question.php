<?php
require_once 'functions/fns.php';
startSession();
security();
if (!access_check([],[],1)) exit ('Access denied');
$id=clean($_POST['id']);
$query= 'select user, answer from questions where id="'.$id.'"';
$db =  db_connect();
$result=$db->query($query);
if (!$result){
    echo "Error: " . $db->error;
    exit();
}
$row=$result->fetch_assoc();
if (!access_check([''],['vk','kvv',$row['user']])) exit ('Access denied.');
if ($_POST['disabled']==='disabled'){
    $answer=$row['answer'];
    $status='published';
}
else {
    $answer=clean($_POST['answer']);
    $status=clean($_POST['status']);
}
$query= 'update questions set '
        . 'text = "'.clean($_POST['text']).'", '
        . 'status = "'.$status.'", '
        . 'answer = "'.$answer.'", '
        . 'date = "'. date('Y-m-d ').'" '
        . 'where id="'.$id.'"';
if ($db->query($query)) {
   header('Location: /questions.php');
}
else {
    echo '<p><font color="red">FAILED:</font></p>'.$db->error;
}