<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security ();
if(check_access('acl_stock', 2)) exit('Access denied.');
isset($_POST['stnmc_deleted'])? $delete=1 : $delete=0;
isset($_POST['stnmc_is_spare'])? $checked=1 : $checked=0;
($_POST['stnmc_price']=='')? $price='NULL' : $price='"'.clean($_POST['stnmc_price']).'"';
($_POST['stnmc_discount']=='')? $discount='NULL' : $discount='"'.clean($_POST['stnmc_discount']).'"';
($_POST['stnmc_origin']=='')? $stnmc_origin='NULL' : $stnmc_origin='"'.clean($_POST['stnmc_origin']).'"';
$output = array();
$db =  db_connect();
$query= 'UPDATE stock_nmnc SET '
        . 'stnmc_type = "'.clean($_POST['stnmc_type']).'", '
        . 'stnmc_pn = "'.clean($_POST['stnmc_pn']).'", '
        . 'stnmc_type_model = "'.clean($_POST['stnmc_type_model']).'", '  
        . 'stnmc_descr = "'.clean($_POST['stnmc_descr']).'", '
        . 'stnmc_manuf = "'.clean($_POST['stnmc_manuf']).'", '
        . 'stnmc_curr = "'.clean($_POST['stnmc_currency']).'", '
        . 'stnmc_price = '.$price.', '
        . 'stnmc_note = "'.clean($_POST['stnmc_note']).'", '
        . 'stnmc_deleted = '.$delete.', '
        . 'stnmc_discount = '.$discount.', '
        . 'stnmc_commod_code = "'.clean($_POST['stnmc_commod_code']).'", '
        . 'stnmc_russia_mult = "'.clean($_POST['stnmc_russia_mult']).'", '
        . 'stnmc_origin = '.$stnmc_origin.', '
        . 'stnmc_is_spare = "'.$checked.'", '
        . 'stnmc_modified_by = "'.$_SESSION['uid'].'" '
        . 'WHERE stnmc_id = "'.$_POST['stnmc_id'].'"';
if(!$result=$db->query($query)){
    $output['result']=false;
    $output['error']=$db->error;
}
else $output['result']=true;
echo json_encode($output);