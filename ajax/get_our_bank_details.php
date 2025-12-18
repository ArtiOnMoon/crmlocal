<?php
require_once '../functions/auth.php';
require_once '../functions/db.php';
$id=$_POST['id'];
$currency=$_POST['currency'];
if($id==''){
    exit('<option></option>');
}
$out='<option></option>';
$query='SELECT * FROM our_details WHERE our_comp_id="'.$id.'" AND currency_list LIKE ("%'.$currency.'%")';
    $db = db_connect();
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $out.='<option ';
        if ($row['id']==$id) $out.=' selected ';
        $out.= 'value="'.$row['id'].'">'.$row['name'].'</option>';
    }
$db->close();
echo $out;