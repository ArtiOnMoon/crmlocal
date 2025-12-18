<?php
function select_tender_status($current=0,$type=0,$headers='name="tender_status"'){
    $out='<select '.$headers.'/>';
    $query= 'SELECT * FROM tenders_statuses';
    $db= db_connect();
    $result=$db->query($query);
    if ($type===1)$out.='<option value="All">All</option>';
    while($row = $result->fetch_assoc()){
        $out.='<option ';
        if ($current==$row['tender_status_id']) $out.='selected ';
        $out.='value="'.$row['tender_status_id'].'">'.$row['tender_status_text'].'</option>';
    }
    $out.='</select>';
    $db->close();
    return $out;
}
function select_tender_paltform($current=0,$type=0,$headers='name="tender_paltform"'){
    $out='<select '.$headers.'/>';
    $query= 'SELECT * FROM tender_platforms';
    $db= db_connect();
    $result=$db->query($query);
    if ($type===1)$out.='<option value="All">All</option>';
    while($row = $result->fetch_assoc()){
        $out.='<option ';
        if ($current==$row['platform_id']) $out.='selected ';
        $out.='value="'.$row['platform_id'].'">'.$row['platform_name'].'</option>';
    }
    $out.='</select>';
    $db->close();
    return $out;
}
