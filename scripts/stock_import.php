<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
startSession();
security();
if (!access_check([],[],1)) exit ('Access denied');

$length= count($_POST['stock_nmnc_id']);
$db=db_connect();
$db->autocommit(false);
for ($i=0;$i<$length;$i++){
   ($_POST['stock_price'][$i]=='')? $price='NULL' : $price='"'.clean($_POST['stock_price'][$i]).'"';
   $query= 'INSERT INTO stock_new SET '
        . 'stock_place = "'.clean($_POST['stock_place'][$i]).'", '
        . 'stock_stock_id = "'.clean($_POST['stock_stock_id'][$i]).'", '
        . 'stock_condition = "'.clean($_POST['stock_condition'][$i]).'", '
        . 'stock_supplier = "'.clean($_POST['stock_supplier'][$i]).'", '
        . 'stock_nmnc_id = "'.clean($_POST['stock_nmnc_id'][$i]).'", '
        . 'stock_serial = "'.clean($_POST['stock_serial'][$i]).'", '
        . 'stock_note = "'.clean($_POST['stock_note'][$i]).'", '
        . 'stock_date_receipt = "'.clean($_POST['stock_date_receipt'][$i]).'", '
        . 'stock_currency = "'.clean($_POST['stock_currency'][$i]).'", '
        . 'stock_price = '.$price.', '
        . 'stock_po= "'.$po.'", '
        . 'stock_so = "'.$so.'", '
        . 'stock_mod_by = "'.$_SESSION['uid'].'", '
        . 'stock_status = "'.clean($_POST['stock_status'][$i]).'"';
   $db->query($query);
   if ($db->errno){
       echo $db->error;
       $db->rollback();
   }   
}
$db->commit();
$db->close();
header('Location: /stock_new.php');