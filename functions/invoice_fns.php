<?php
function invoice_type($val){
    if ($val=='1')return 'IN';
    elseif($val=='2') return 'OUT';
    return 'Error';
}
function select_invoice_status($current=0,$headers='name="invoice_status"',$type=0){
    $out.= '<select '.$headers.'>';
    $db =  db_connect();
    $query= 'SELECT * FROM invoices_statuses';
    if(!$result=$db->query($query))$out.='<option>Error</option>';
    if($type===1)$out.='<option value="0" selected>All</option>';
    while ($row=$result->fetch_assoc()){
        $out.='<option ';
        if ($row['inv_stat_id']==$current) $out.='selected ';
        $out.='value='.$row['inv_stat_id'].'>'.$row['inv_stat_name'].'</option>';
    }
    $out.= '</select>';
    return $out;
}
function select_invoice_type($current=0,$headers='name="invoice_type"',$type=0){
    $out.= '<select '.$headers.'>';
    if($type===1)$out.='<option selected value="0">All</option>';
    $out.='<option ';
    if ($current==='1') $out.='selected ';
    $out.='value="1">IN</option>';
    $out.='<option ';
    if ($current==='2') $out.='selected ';
    $out.='value="2">OUT</option>';
    $out.= '</select>';
    return $out;
}
function select_invoice_cn($current=0,$headers='name="invoice_is_cn"',$type=0){
    $out.= '<select '.$headers.'>';
    if($type===1)$out.='<option selected value="All">All</option>';
    $out.='<option ';
    if ($current==='0') $out.='selected ';
    $out.='value="0">Invoice</option>';
    $out.='<option ';
    if ($current==='1') $out.='selected ';
    $out.='value="1">Credit note</option>';
    $out.= '</select>';
    return $out;
}
function invoice_cn_decode($val){
    if ($val==='0') return 'Invoice';
    elseif ($val==='1') return 'Credit note';
}
function view_invoice_by_id ($id,$name=''){
    if ($name=='')$name=$num;
    return '<a href="#" onclick="invoice_view_by_id(\''.$id.'\')">'.$name.'</a>';
}
function view_invoice ($our_comp,$num,$name=''){
    if ($name=='')$name=$num;
    return '<a href="#" onclick="invoice_view(\''.$our_comp.'\',\''.$num.'\')">'.$name.'</a>';
}
function invoice_color_table($status){
    $out='';
    if ($status=='1')$out.= 'row_grey';//Draft
    elseif ($status=='2') $out.= 'row_brown';//Sent
    elseif ($status=='3') $out.= 'row_complete';//Received
    elseif ($status=='4') $out.= 'row_green';//Paid
    elseif ($status=='5' ) $out.= 'row_red';//Canceled
    else $out.= 'row_white';
    return $out;
}