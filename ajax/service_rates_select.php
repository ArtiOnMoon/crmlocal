<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';

$currency= clean($_POST['currency']);
if ($currency=='') exit('No currency selected');

$var.='<select required name="service_rates"';
    if ($flag===0) $var.=' id="select_service_rate" onchange="slelect_control(this)"';
    else $var.=' width="100%" onchange="rate_calc(this)"';
    $var.='>';
    $var.='<option value=""></option>';
    $query='select * from service_rates_cat where rate_cat_id != 1 and rate_currency="'.$currency.'"';
    $db = db_connect();
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $query2='select rate_id, rate_name, rate_price, rate_currency from service_rates where rate_cat="'.$row['rate_cat_id'].'"';
        $result2=$db->query($query2);
        $var.= '<optgroup label="'.$row['rate_cat_name'].'">';
        while($row2=$result2->fetch_assoc()){
            $var.= '<option data-currency="'.$row2['rate_currency'].'" data-price="'.$row2['rate_price'].'"';
            if ($row2['rate_id']==$current) $var.= 'selected ';
            $var.= 'value="'.$row2['rate_id'].'">['.$row2['rate_price'].' '.$row2['rate_currency'].'] '.$row2['rate_name'].'</option>';
        }
        $var.='</optgroup>';
    }
    $query3='select rate_id, rate_name, rate_price, rate_currency from service_rates where rate_cat="1" and rate_currency="'.$currency.'"';
    $result3=$db->query($query3);
     if ($result->num-rows===0) return $var;
        $var.= '<optgroup label="">';
        while($row3=$result3->fetch_assoc()){
            $var.= '<option data-currency="'.$row3['rate_currency'].'" ';
            if ($row3['rate_id']==$current) $var.= 'selected ';
            $var.= 'value="'.$row3['rate_id'].'">['.$row3['rate_price'].' '.$row3['rate_currency'].'] '.$row3['rate_name'].'</option>';
    }
$var.='</optgroup></select>';
echo $var;