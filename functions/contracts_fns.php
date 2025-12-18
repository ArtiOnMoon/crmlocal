<?php
function contract_view($id,$number=''){
    if ($number==='')$number=$id;
    return '<a href="#" onclick="contract_view('.$id.')">'.$number.'</a>';
}
function contract_number($our_number,$number='',$use_ext_num=0){
    if($use_ext_num)return $number;
    return $our_number;
}
function select_contract_status($current, $headers='name="contract_status"',$type=0){
    $out='<select '.$headers.'>';
    $db =  db_connect();
    $query= 'SELECT * FROM contract_statuses';
    if(!$result=$db->query($query))$out.='<option>Error</option>';
    if($type===1)$out.='<option selected>All</option>';
    while ($row=$result->fetch_assoc()){
        $out.='<option ';
        if ($row['contract_status_id']==$current) $out.='selected ';
        $out.='value='.$row['contract_status_id'].'>'.$row['contract_status_text'].'</option>';
    }
    $out.='</select>';
    return $out;
}
function select_contract_type($current, $headers='name="contract_type"',$type=0){
    $out='<select '.$headers.'>';
    $db =  db_connect();
    $query= 'SELECT * FROM contract_types';
    if(!$result=$db->query($query))$out.='<option>Error</option>';
    if($type===1)$out.='<option selected>All</option>';
    while ($row=$result->fetch_assoc()){
        $out.='<option ';
        if ($row['contract_type_id']==$current) $out.='selected ';
        $out.='value='.$row['contract_type_id'].'>'.$row['contract_type_text'].'</option>';
    }
    $out.='</select>';
    return $out;
}