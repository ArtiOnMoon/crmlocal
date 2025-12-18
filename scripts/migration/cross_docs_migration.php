<?php
require_once '../../functions/db.php';
require_once '../../functions/main.php';
require_once '../../classes/Order_name_engine.php';

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);

$comp_list = $on->comp_list;


$query2='SELECT * FROM `cross_docs` ORDER BY id';
$result2 = $db->query($query2);

while ($row = $result2->fetch_assoc()){
    //LEFT
    $on->comp_id = $row['comp1'];
    switch ($row['type1']){
        case '1': $on->type = 'SR'; break;
        case '2': $on->type = 'SL'; break;
        case '3': $on->type = 'PO'; break;
        default : $on->type = null;
    }
    if (is_null($on->type)) {continue;}
    
    if (is_numeric($row['num1'])){
        $on->num = $row['num1'];
    } else {
        continue;
    }
    
    $left = $on->get_order();
    
    //RIGHT
    $on->comp_id = $row['comp2'];
    switch ($row['type2']){
        case '1': $on->type = 'SR'; break;
        case '2': $on->type = 'SL'; break;
        case '3': $on->type = 'PO'; break;
        default : $on->type = null;
    }
    if (is_null($on->type)) {continue;}
    if (is_numeric($row['num2'])){
        $on->num = $row['num2'];
    } else {continue;}
    
    $right = $on->get_order();
    
    $query = 'UPDATE cross_docs '
            . 'SET num1="'.$left.'", num2="'.$right.'" '
            . 'WHERE id='.$row['id'];
    $db->query($query);
//    echo $query.'<br>';
}