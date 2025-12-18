<?php
require_once 'functions/fns.php';
function show_input_message() {
    $db =  db_connect();
    $query= 'select * from messages where receiver = "'.$_SESSION['valid_user'].'"';
    $result=$db->query($query);
echo '<table class="sortable" rules="rows" width="100%" border="1px" cellspacing = "0" cellpadding="2px">'
. '<thead style="font-size: 150%"><tr>'
. '<th axis="num" width="20">Message ID</th>'
. '<th width="50">Date</th>'
. '<th width="100">Subject</th>'
. '<th width="20">Sender</th>'
. '</tr><tbody>';
while($row = $result->fetch_assoc()){
    echo '<tr ';
    if ($row['checked']==0)        echo 'style="background: orange";';
    echo '><td class="num">';
    echo   view_mesage_link($row['message_id'],$row['message_id'])
        . '<td>'.$row['date']
        . '<td>'.view_mesage_link($row['message_id'],$row['subject'])
        . '<td>'.$row['sender']
        . '</tr>';
};
echo '</tbody></table>';
}
function show_output_message() {
    $db =  db_connect();
    $query= 'select * from messages where sender = "'.$_SESSION['valid_user'].'"';
    $result=$db->query($query);
echo '<table class="sortable" width="100%" rules="rows"  border="1px" cellspacing = "0" cellpadding="2px">'
. '<thead style="font-size: 150%"><tr>'
. '<th axis="num" width="20">Message ID</th>'
. '<th width="50">Date</th>'
. '<th width="100">Subject</th>'
. '<th width="20">Receiver</th>'
. '</tr><tbody>';
while($row = $result->fetch_assoc()){
    echo '<tr><td class="num">';
    echo   view_mesage_link($row['message_id'],$row['message_id'])
        . '<td>'.$row['date']
        . '<td>'.view_mesage_link($row['message_id'],$row['subject'])
        . '<td>'.$row['receiver']
        . '</tr>';
};
echo '</tbody></table>';
}
function view_mesage_link ($message_id, $text){
    $var='<a href="view_message.php?message_id='.$message_id.'">'.$text.'</a>';
    return $var;
}
