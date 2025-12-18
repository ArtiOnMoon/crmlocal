<?php
function select_transfer_status($current=0, $headers='name="transfers_status"',$type=0){
    $out='<select '.$headers.'>';
    $db =  db_connect();
    $query= 'SELECT * FROM transfers_status';
    if(!$result=$db->query($query))$out.='<option></option>';
    if($type===1)$out.='<option selected>All</option>';
    while ($row=$result->fetch_assoc()){
        $out.='<option ';
        if ($row['transfers_status_id']==$current) $out.='selected ';
        $out.='value='.$row['transfers_status_id'].'>'.$row['transfers_status_name'].'</option>';
    }
    $out.='</select>';
    return $out;
}
function transfers_view($id){
    $out='<a href="#" onclick="transfers_view(\''.$id.'\')">TR'.$id.'</a>';
    return $out;
}