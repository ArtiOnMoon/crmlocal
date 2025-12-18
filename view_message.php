<?php
require_once 'functions/fns.php';
require_once 'functions/message_fns.php';
do_page_header('View message');
    $db =  db_connect();
    $query= 'select * from messages where message_id = "'.$_GET['message_id'].'"';
    $result=$db->query($query);
    $row = $result->fetch_assoc();

    if (($row['sender']==$_SESSION['valid_user']) OR ($row['receiver']==$_SESSION['valid_user'])){
    echo 'From <b>'.$row['sender'].'</b> '; 
    echo '<br><b>Subject: '.$row['subject'];
    echo '</b><p>'.$row['content'].'</p>';
    echo 'date:'.$row['date'].'<br><br>';
    if ($row['attachment']!=='') echo 'Attachment: <a href="download_attachment.php?id='.$_GET['message_id'].'&file='.$row['attachment'].'">'.$row['attachment'].'</a>';
    
    if ($row['receiver']==$_SESSION['valid_user']){
        $query= 'update messages set checked = "1" where message_id = "'.$_GET['message_id'].'"';
        if (!$result=$db->query($query)) echo $db->error;
        }
    }
    else
    {
        echo 'Accsess denied.';
        exit();    
    }
//echo '<h2>Fast reply<h2>';
    if ($row['sender']!=$_SESSION['valid_user']){
echo '<form action="message_send.php" name="new_message" method="POST" onsubmit="send_message()">';
echo '<textarea name="message_content"></textarea><br>';
echo '<input type="button" value="Fast reply" onclick="send_message()">';
echo '<input type="hidden" name="message_subject" value="Re:'.$row['subject'].'">';
echo '<input type="hidden" name="message_receiver" value="'.$row['sender'].'">';
echo '</form>';
echo '<div id="message_status"></div>';
    }