<?php
    require_once '../functions/db.php'; 
    $data = trim(strip_tags(stripcslashes(htmlspecialchars($_POST["data"]))));
    $index=$_POST["index"];
    $class=$_POST["type"]; 
    if ($index=='1') $elem='pn';
    if ($index=='2') $elem='description';
    $db =  db_connect();
    $query= 'select class, pn, description, price, price_discount from price where '.$elem.' LIKE("%'.$data.'%") and class LIKE("%'.$class.'%")';
    $result=$db->query($query);
    echo '<table class="search_table" rules="cols" cellspadding="0" align="center">';
    while ($row = $result->fetch_row()) {
        $price=$row[3]+$row[3]*$row[4]*0.01;
        echo '<tr onclick="data_selected(this)"><td width="157px">'.$row[1]
                . '</td><td width="314px">'.$row[2].'</td><td width="107px"></td><td width="108px">'.$price.'</td>';
        echo '</tr>';
    }
    echo '</table>';
?>