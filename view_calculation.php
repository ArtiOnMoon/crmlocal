<?php
require_once 'functions/fns.php';
require_once 'functions/purchase_fns.php';
do_page_header('Calculation');
echo '<script type="text/javascript" src="java/java_func.js"></script>';
echo'<div id="main_div_menu">';
$procents=0;
$non_procents=0;
$base_total=0;
$db =  db_connect();
$query= 'select * from purchase, customers where id = "'.$_GET['purchase_id'].'" and customer = cust_id';
$result=$db->query($query);
if ($result-> num_rows==1){
    $row=$result->fetch_assoc();
}

//charges
echo '<div style="float:right">';
echo '<h2 align="center">Charges</h2>';
echo'<table id="charges" width="500px" border="0" cellspacing = "0" cellpadding="2px">
    <th width="100px">Name</th>
    <th width="100px">Value</th>
    <tr><td>VAT</td><td>'.$row['vat'].' %</td></tr>';

if ($row['charges']!==''){
    $element=explode('<>',$row['charges']);
    $i=0;
foreach ($element as $value) {
    $charges[$i] = explode('#',$value);
    echo '<tr><td>'.$charges[$i][0].'</td>';
    echo '<td>'.$charges[$i][1];
    if ($charges[$i][2]=='%') {
        echo ' %';
        $procents+=$charges[$i][1];
    }
    else {
        echo ' '.$row['currency'];
        $non_procents+=$charges[$i][1];
    }
    echo '</td></tr>';
    $i++;
    }
}
echo '</table></div>';

//content
echo '<p>';
echo'<table id="purchase_content" width="100%" border="0" cellspacing = "0" cellpadding="2px">
    <th width="100px">Type</th><th width="100px">Status</th>
    <th width="100px">ID</th><th width="100px">PN</th>
    <th width="150px">Serial</th><th>Description</th>
    <th width="70px">Net price</th><th width="70px">Duty %</th>
    <th width="50px">Price</th><th width="50px">Out price</th>';

$total=0;
if ($row['content']!==''){
    $content=explode(',',$row['content']);
foreach ($content as $value) {
    $query= 'select duty, net_price from stock where id = "'.$value.'"';
    $result=$db->query($query);
    $row2=$result->fetch_assoc();
    $base_total += $row2['net_price']*1;
    $total=$total+($row2['net_price']+$row2['net_price']*$row2['duty']*0.01);
}
    $total+= $total*0.01*$procents;
    $total+= $non_procents;
    if ($vat!='0') {$vat= $total*0.18; $total+=$vat;}
    $koef=round($total/$base_total, 3);
    
foreach ($content as $value) {
    $query= 'select class, status, id, type_or_pn, serial, descr, duty, net_price from stock where id = "'.$value.'"';
    $result=$db->query($query);
    $row=$result->fetch_assoc();
    echo '<tr><td>'.$row['class'].'</td>';
    echo '<td>'.$row['status'].'</td>';
    echo '<td>'.stock_id_link($row['id']).'</td>';
    echo '<td>'.$row['type_or_pn'].'</td>';
    echo '<td>'.$row['serial'].'</td>';
    echo '<td>'.$row['descr'].'</td>';
    echo '<td>'.$row['net_price'].'</td>';
    echo '<td>'.$row['duty'].'</td>';
    echo '<td>'.($row['net_price']+$row['net_price']*$row['duty']*0.01).'</td>';
    echo '<td>'.round($row['net_price']*$koef, 2).'</td>';
    }
    echo '<tr><td colspan="7"><td align="right"><b>Total: </b><td><b>'.round($total,2).'</b></td></tr>';
    echo '<tr><td colspan="7"><td align="right">incl VAT: <td>'.round($vat,2).'</td></tr>';
}
echo'</table>';
echo 'Calculation';
echo'<table id="purchase_content" width="100%" border="0" cellspacing = "0" cellpadding="2px">';
echo '<th width="100px">EXW</th><th width="100px">Status</th>';