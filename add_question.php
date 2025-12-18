<?php
require_once 'functions/fns.php';
startSession();
security();
if (!access_check([],[],2)) exit ('Access denied');
$db =  db_connect();
$query= 'insert into questions set '
        . 'text = "'.clean($_POST['task_text']).'", '
        . 'user = "'.$_SESSION['valid_user'].'", '
        . 'name = "'.$_SESSION['full_name'].'", '
        . 'date = "'. date('Y-m-d ').'"';

$db->query('LOCK TABLES questions WRITE');

if ($db->query($query)){
    $db->query('UNLOCK TABLES');
    header('Location: /questions.php');
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
}
$db->query('UNLOCK TABLES');