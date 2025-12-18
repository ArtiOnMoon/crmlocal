<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
$db= db_connect();
$query='SELECT srv_eq_id, srv_eq_name '
        . 'FROM service_equipment '
        . 'WHERE srv_eq_manuf="'.$_POST['manufacturer'].'" AND srv_eq_cat="'.$_POST['category'].'" '
        . 'ORDER BY srv_eq_name';
if(!$result=$db->query($query)) exit('<option="0">Nothing found</option>');
if ($result->num_rows === 0) {
    exit('<option value="0">Nothing found</option>');
}
$db->close();
echo '<option value="0"></option>';
while ($row=$result->fetch_assoc()){
    echo '<option value="'.$row['srv_eq_id'].'">'.$row['srv_eq_name'].'</option>'; 
}