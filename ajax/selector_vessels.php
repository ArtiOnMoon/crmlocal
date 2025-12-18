<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$data=clean($_POST['data']);
$query= 'SELECT vessel_id, vessel_name,imo FROM vessels WHERE vessel_deleted=0';
if ($data !='') $query.=' AND (vessel_id LIKE "%'.$data.'%" OR vessel_name LIKE "%'.$data.'%" OR mmsi LIKE "%'.$data.'%" OR imo LIKE "%'.$data.'%")';
$query.=' ORDER BY vessel_name';
$db =  db_connect();
if(!$result=$db->query($query))exit($db->error);
echo '<div class="selector_result_div row_white" data-id="new_vessel"><a href="#">Add vessel</a></div>';
if($result->num_rows>0){
    while ($row=$result->fetch_assoc()){
        echo '<div class="selector_result_div row_white" data-id="'.$row['vessel_id'].'" data-value="'.$row['vessel_name'].'">',
            '<strong>',$row['vessel_name'],'</strong><br>',
            'IMO:',$row['imo'],
        '</div>';
    }
}   
else echo'No results';