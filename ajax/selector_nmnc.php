<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$data=clean($_POST['data']);
$condition=clean($_POST['condition']);
$query= 'SELECT stnmc_id, stnmc_descr,stnmc_pn, stnmc_type_model FROM stock_nmnc WHERE stnmc_deleted=0';
if ($data != '') $query.=' AND (stnmc_id = "'.$data.'" OR stnmc_descr LIKE "%'.$data.'%" OR stnmc_pn LIKE "%'.$data.'%" OR stnmc_type_model LIKE "%'.$data.'%")';
if ($condition !='') $query.=' AND stnmc_type="'.$condition.'"';
$query.=' ORDER BY stnmc_descr';
$db =  db_connect();
if(!$result=$db->query($query))exit($db->error);
if($result->num_rows>0){
    while ($row=$result->fetch_assoc()){
        echo '<div class="selector_result_div row_white" data-id="'.$row['stnmc_id'].'" data-value="'.trim($row['stnmc_pn'].' '.$row['stnmc_descr'].' '.$row['stnmc_type_model']).'">',
            '<strong>',$row['stnmc_descr'].' '.$row['stnmc_type_model'],'</strong><br>',
            'P/N:',$row['stnmc_pn'],
        '</div>';
    }
}
else echo'No results';
$db->close();