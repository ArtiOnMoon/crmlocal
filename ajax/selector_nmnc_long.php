<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
$db= db_connect();
$query='SELECT stnmc_id,stnmc_pn,stnmc_type_model,stnmc_descr '
        . 'FROM stock_nmnc '
        . 'WHERE stnmc_manuf="'.$_POST['manufacturer'].'" AND stnmc_type="'.$_POST['category'].'" '
        . 'ORDER BY stnmc_pn,stnmc_type_model';
if(!$result=$db->query($query)) exit('<option="0">Nothing found</option>');
if ($result->num_rows === 0) {
    exit('<option value="0">Nothing found</option>');
}
$db->close();
echo '<option value="0"></option>';
while ($row=$result->fetch_assoc()){
    echo '<option value="'.$row['stnmc_id'].'">'.$row['stnmc_pn'].' '.$row['stnmc_type_model'].' '.$row['stnmc_descr'].'</option>'; 
}