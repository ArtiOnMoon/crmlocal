<?php
require_once 'functions/fns.php';
include('functions/smtp-func.php');
startSession();
security();
if (!access_check([],[],1)) exit ('Access denied');
$db =  db_connect();
$query= 'insert into tasks set '
        . 'task = "'.clean($_POST['task']).'", '
        . 'task_title = "'.clean($_POST['task_title']).'", '
        . 'from_user = "'.$_SESSION['uid'].'", '
        . 'to_user = "'.clean($_POST['user']).'", '
        . 'expire = "'.clean($_POST['expire']).'", '
        . 'date = "'. date('Y-m-d ').'"';
if ($db->query($query)){
    $id=$db->insert_id;
    //отправка mail'a
    $query = 'SELECT username,full_name,mail_addr FROM users WHERE uid="'.clean($_POST['user']).'"';
    $result=$db->query($query);
    if (!$result) echo '<font color="red">Problem: </font>'.$db->error;
        $row=$result->fetch_assoc();
        $type = 'html'; //Можно поменять на html; plain означяет: будет присылаться чистый текст.
        $charset = 'UTF-8';
        $mail_to=$row['mail_addr'];
        $replyto = 'crm@az-marine.com';
        $to=$row['full_name'];
        $headers = "To: \"$to\" <$mail_to>\r\n".
              "From: \"CRM\" <crm@az-marine.com>\r\n".
              "Reply-To: \"$replyto\" <crm@az-marine.com\r\n".
              "Content-Type: text/$type; charset=\"$charset\"\r\n";
    $message='You recceived a new task from '.$_SESSION['full_name'].'. To see task details folow <a href="http://crm.v-marine.net/task_view.php?id='.$id.'">this link</a>.';
    $sended = smtpmail($mail_to, 'You recceived a new task', $message, $headers);
    header('Location: /tasks.php');
    exit();
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;
}