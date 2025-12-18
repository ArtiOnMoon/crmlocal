<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
if(check_access('acl_stock', 2)) exit('Access denied.');

$po=clean($_POST['stock_po']);
$our_company=clean($_POST['stock_our_company']);
$date_receipt=clean($_POST['date_receipt']);
$stock=$_POST['stock'];
$so=clean($_POST['stock_so']);
($_POST['stock_so_comp']=='')? $stock_so_comp='NULL' : $stock_so_comp='"'.clean($_POST['stock_so_comp']).'"';
($_POST['stock_po_comp']=='')? $stock_po_comp='NULL' : $stock_po_comp='"'.clean($_POST['stock_po_comp']).'"';

$length= count($_POST['stock_nmnc_id']);
$db=db_connect();
$db->autocommit(false);
for ($i=0;$i<$length;$i++){
   if ($_POST['stock_warranty_to'][$i]=='')$warranty='NULL'; else $warranty='"'.$_POST['stock_warranty_to'][$i].'"';
   ($_POST['stock_price'][$i]=='')? $price='0' : $price='"'.clean($_POST['stock_price'][$i]).'"';
   //($_POST['stock_freight'][$i]=='')? $freight='0' : $freight='"'.clean($_POST['stock_freight'][$i]).'"';
   if (clean($_POST['stock_compl_id'][$i])==='') $stock_compl_id='NULL'; else $stock_compl_id='"'.clean($_POST['stock_compl_id'][$i]).'"';
   $query= 'INSERT INTO stock_new SET '
        . 'stock_place = "'.clean($_POST['stock_place'][$i]).'", '
        . 'stock_stock_id = "'.$stock.'", '
        . 'stock_our_company = "'.$our_company.'", '
        . 'stock_condition = "'.clean($_POST['stock_condition'][$i]).'", '
        . 'stock_supplier = "'.clean($_POST['stock_supplier'][$i]).'", '
        . 'stock_nmnc_id = "'.clean($_POST['stock_nmnc_id'][$i]).'", '
        . 'stock_serial = "'.clean($_POST['stock_serial'][$i]).'", '
        . 'stock_note = "'.clean($_POST['stock_note'][$i]).'", '
        . 'stock_ccd = "'.clean($_POST['stock_ccd'][$i]).'", '
        . 'stock_date_receipt = "'.$date_receipt.'", '
        . 'stock_currency = "'.clean($_POST['stock_currency'][$i]).'", '
        . 'stock_price = '.$price.', '
        //. 'stock_freight = '.$freight.', '
        . 'stock_so_comp = '.$stock_so_comp.', '
        . 'stock_po_comp = '.$stock_po_comp.', '
        . 'stock_po = "'.$po.'", '
        . 'stock_so = "'.$so.'", '
        . 'stock_mod_by = "'.$_SESSION['uid'].'", '
        . 'stock_warranty_to = '.$warranty.', '
        . 'stock_compl_id = '.$stock_compl_id.', '
        . 'stock_status = "'.clean($_POST['stock_status'][$i]).'"';
   $db->query($query);
   if ($db->errno){
       echo $db->error;
       echo $query;
       $db->rollback();
   }   
}
$db->commit();
$db->close();
exit('true');