<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security ();

$db =  db_connect();
$db->autocommit(false);
$length= count($_POST['stnmc_type']);
for ($i=0; $i<$length; $i++){
    ($_POST['stnmc_discount'][$i]=='')? $discount='NULL' : $discount='"'.clean($_POST['stnmc_discount'][$i]).'"';
    ($_POST['stnmc_price'][$i]=='')? $price='NULL' : $price='"'.clean($_POST['stnmc_price'][$i]).'"';
    $query= 'insert into stock_nmnc set '
        . 'stnmc_type = "'.clean($_POST['stnmc_type'][$i]).'", '
        . 'stnmc_pn = "'.clean($_POST['stnmc_pn'][$i]).'", '
        . 'stnmc_type_model = "'.clean($_POST['stnmc_type_model'][$i]).'", '
        . 'stnmc_descr = "'.clean($_POST['stnmc_descr'][$i]).'", '
        . 'stnmc_manuf = "'.clean($_POST['stnmc_manuf'][$i]).'", '
        . 'stnmc_curr = "'.clean($_POST['stock_currency'][$i]).'", '
        . 'stnmc_price = '.$price.', '
        . 'stnmc_note = "'.clean($_POST['stnmc_note'][$i]).'", '
        . 'stnmc_discount = '.$discount.', '
        . 'stnmc_is_spare = "'.clean($_POST['stnmc_is_spare'][$i]).'", '
        . 'stnmc_modified_by = "'.$_SESSION['uid'].'"';
    $db->query($query);
    if ($db->errno){
       echo $db->error;
       $db->rollback();
    }   
}
$db->commit();
header('Location: /stock_nmnc.php');