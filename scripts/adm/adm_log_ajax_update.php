<?php

require_once '../../functions/main.php';
require_once '../../functions/db.php';
require_once '../../functions/auth.php';
require_once '../../classes/Adm.php';

$db = db_connect();

$id = clean($_POST['id']);

$query2 = 'SELECT administrative_logs.*, full_name FROM administrative_logs '
        . 'LEFt JOIN users on uid=user '
        . 'WHERE order_id ="'.$id.'" ORDER BY id DESC';
if(!$result2 = $db->query($query2)){
    echo $db->error,$query2;}
while($row = $result2->fetch_assoc()){
    echo '<div class="adm_log_div">';
    echo '<div class="adm_log_div_header"><strong>'.$row['full_name'].'</strong><br>'.$row['timestamp'].'</strong></div>';
    echo '<div class="adm_log_div_text">'.$row['text'].'</div></div>';
}