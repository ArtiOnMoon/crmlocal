<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';

$category_id= clean($_POST['cat']);
if ($category_id=='')echo'<option>No category selected</option>';
$query='SELECT stnmc_id, stnmc_descr, stnmc_pn, stnmc_type_model FROM stock_nmnc WHERE stnmc_type ="'.$category_id.'"';
$db = db_connect();
$result=$db->query($query);
if ($result->num_rows===0)echo'<option value="">No results</option>';
else {
    while($row = $result->fetch_assoc()){
        echo '<option value="'.$row['stnmc_id'].'">'.$row['stnmc_descr'].' '.$row['stnmc_type_model'].' '.$row['stnmc_pn'].'</option>';
    }
}