<?php
    require_once 'functions/db.php';
    if(check_access('acl_purchase', 1)) exit('Access denied.');
    $data = trim(strip_tags(stripcslashes(htmlspecialchars($_POST["data"]))));
    $index=$_POST["index"];
    $class=$_POST["type"]; 
    if ($index=='1') $elem='pn';
    if ($index=='2') $elem='description';
    $db =  db_connect();
    $query= 'select class, pn, description from price where '.$elem.' LIKE("%'.$data.'%") and class LIKE("%'.$class.'%")';
    $result=$db->query($query);
    echo '<table width="1024px" rules="cols" cellspadding="0" align="center">';
    while ($row = $result->fetch_row()) {
        echo '<tr onclick="data_selected(this)"><td width="250px">'.$row[1]
                . '</td><td width="300px">'.$row[2].'</td><td></td>';
        echo '</tr>';
    }
    echo '</table>';
    

?>