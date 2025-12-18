<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$id=clean($_POST['id']);
$query= 'select id, name FROM customers_contacts WHERE customer_id ="'.$id.'" AND deleted=0';
$db =  db_connect();
if(!$result=$db->query($query))exit('<option value="NULL">Error</option>');
if($result->num_rows>0){
    echo '<option value="NULL">No agent</option>';
    while ($row=$result->fetch_assoc()){
        echo'<option value="',$row['id'],'">',$row['name'],'</option>';
    }
}   
else echo'<option value="NULL">No contacts</option>';