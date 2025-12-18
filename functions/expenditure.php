<?php
function select_expend_type ($id='', $headers='name="expend_type"',$type=0){
    $out='';
    $db= db_connect();
    $query='select * from expend_type';
    $result=$db->query($query);
    if ($result->num_rows===0) return 'Not found';
    $out.='<select '.$headers.'>';
    if ($type===1) $out.='<option value="All">All</option>';
        while($row = $result->fetch_assoc()){
            $out.='<option value="'.$row['expend_type_id'].'"';
            if ($id===$row['expend_type_id']) $out.=' selected ';
            $out.='>'.$row['expend_type_name'].'</option>';
    }
    $out.='</select>';
    return $out;
}
function select_year($headers='name="year"'){
    $str='<select '.$headers.'>';
    $year=date(Y);
    settype($year,'integer');
    for ($i=($year-10);$i<=$year+10;$i++){
        $str.='<option value="'.$i.'"';
        if ($i===$year) $str.=' selected';
        $str.='>'.$i.'</option>';
    }
    $str.='</select>';
    return $str;
}