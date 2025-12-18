<?php
require_once 'functions/fns.php';
startSession();
security();
if(check_access('acl_stock', 2)) exit('Access denied.');
$po=clean($_POST['po']);
$date_receipt=clean($_POST['date_receipt']);
$currency=clean($_POST['currency']);
$place=clean($_POST['place']);
$manufacturer=clean($_POST['manufacturer']);
$supplier=clean($_POST['supplier']);
$stock=clean($_POST['stock']);
$freight=clean($_POST['freight']);
$customs_dec=clean($_POST['customs_dec']);
if (clean($_POST['on_balance'])=='1') $on_balance=1; else $on_balance=0;
//echo $po,$date_receipt,$currency,$place;
$db=db_connect();
$db->autocommit(false);
$content=json_decode($_POST['content']);
foreach ($content as $value){
   $item= json_decode($value);
   $query= 'insert into stock set '
        . 'place = "'.$place.'", '
        . 'stock = "'.$stock.'", '
        . 'freight = "'.$freight.'", '
        . 'customs_dec = "'.$customs_dec.'", '
        . 'currency = "'.$currency.'", '
        . 'date_receipt = "'.$date_receipt.'", '
        . 'purchase_order = "'.$po.'", '
        . 'type_or_pn = "'.$item[0].'", '
        . 'descr = "'.$item[1].'", '
        . 'note = "'.$item[2].'", '
        . 'net_price = "'.$item[4].'", '
        . 'min_price = "'.$item[5].'", '
        . 'serial = "'.$item[6].'", '
        . 'class = "'.$item[7].'", '
        . 'status = "'.$item[8].'", '
        . 'cond = "'.$item[9].'", '
        . 'supplier = "'.$supplier.'", '
        . 'on_balance = "'.$on_balance.'", '
        . 'manufacturer = "'.$manufacturer.'"';
   if ($item[3]=='') $query.=',warranty=NULL';//проверка даты 
   $db->query($query);
   if ($db->errno){
       echo $db->error;
       $db->rollback();
   }   
}
$db->commit();
header('Location: /stock.php');