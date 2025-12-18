<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security ();

if(check_access('acl_purchase', 2)) exit('Access denied.');
//1 - service 2 - sales 3 - PO 4 - invoice	

//$comp_id=clean($_POST['comp_id']);
$number=clean($_POST['number']);
//$type=clean($_POST['type']);
//if ($type<4)$number=(int)$number;
//$comp_id1=clean($_POST['comp_id1']);
$number1=clean($_POST['number1']);
//$type1=clean($_POST['type1']);
//if ($type1<4)$number1=(int)$number1;

//echo $comp_id.' '.$number.' '.$type;
$db =  db_connect();
$query= 'SELECT id FROM cross_docs WHERE '
        . 'num1="'.$number.'"  AND num2="'.$number1.'" '
        . 'UNION '
        . 'SELECT id FROM cross_docs WHERE '
        . 'num1="'.$number1.'" AND num2="'.$number.'"';
if(!$result=$db->query($query)){
    echo $db->error;
    exit();
}
if($result->num_rows!==0)exit('Link already exists.');
$query= 'INSERT INTO cross_docs SET '
        . 'num1="'.$number.'",'
        . 'num2="'.$number1.'"';
if(!$result=$db->query($query)){
    echo $db->error;
    exit();
}
echo 'Added successfully';