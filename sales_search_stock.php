<?php
    require_once 'functions/main.php';
    require_once 'functions/db.php'; 
    require_once 'functions/auth.php';
    if(check_access('acl_asles', 1)) exit('Access denied.');
    echo '<span class="search_close_span"><a href="#" onclick="close_search_div(this)">Close</a></span><br>';
    $data = clean($_POST["data"]);
    $class=$_POST["type"]; 
    $db =  db_connect();
    $query= 'select id, class, type_or_pn, descr,  serial, currency from stock where (type_or_pn like ("%'.$data.'%") OR id like ("%'.$data.'%")) and class ="'.$class.'" limit 50';
    $result=$db->query($query);
    if($result->num_rows===0) exit ('Nothing found');
    echo '<table width="100%" border-right="3px solid black" cellspadding="0" onclick="table_data_selected(event,\'stock\')">';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>'
        . '<td width="10%">'.$row['id'].'</td>'
        . '<td width="10%">'.$row['type_or_pn'].'</td>'
        . '<td width="20%">'.$row['descr'].'</td>'
        . '<td width="20%">'.$row['serial'].'</td>'
        . '<td width="20%">'.$row['currency'].'</td>'
        . '</tr>';
    }
?>
</table>