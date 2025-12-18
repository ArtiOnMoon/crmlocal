<?php
function show_stock_item ($stock_item){
    $db =  db_connect();
    $query= 'select name from stock where id = "'.$stock_item.'"';
    $result=$db->query($query);
    if ($result-> num_rows==1){
        $row=$result->fetch_assoc();
        $a='<a href="view_stock_item.php?id='.$stock_item.'">'.$row['name'].'</a>';
        return $a;
    }
}
function select_po_status($current, $headers='name="po_status"',$type=0){
    $out='<select '.$headers.'>';
    $db =  db_connect();
    $query= 'SELECT * FROM purchase_status';
    if(!$result=$db->query($query))$out.='<option></option>';
    if($type===1)$out.='<option selected>All</option>';
    while ($row=$result->fetch_assoc()){
        $out.='<option ';
        if ($row['po_stat_id']==$current) $out.='selected ';
        $out.='value='.$row['po_stat_id'].'>'.$row['po_stat_name'].'</option>';
    }
    $out.='</select>';
    return $out;
}
function po_color_table($status){
    $out='';
    if ($status=='2')$out.= 'row_brown';//Sent
    elseif ($status=='3') $out.= 'row_confirmed';//Confirmed
    elseif ($status=='4') $out.= 'row_yellow';//Shipped
    elseif ($status=='5') $out.= 'row_green';//Delivered
    elseif ($status=='7' ) $out.= 'row_red';//Canceled
    elseif ($status=='6') $out.= 'row_complete';//Completed
    elseif ($status=='8') $out.= 'row_violet';// Part. delivered
    else $out.= 'row_white';
    return $out;
}
function sales_select_vat_rem($current=''){
    $out='<select name="sales_vat_remarks">';
    $out.='<option></option>';
    //Option 1
    $option1='VAT remarks: KMS pr.15, lg.3, p.3 Directive 2006/ 112/EC, art 148, art 37(3)';
    $out.='<option';
    if ($current===$option1)$out.=' selected';
    $out.='>'.$option1.'</option>';
    //Option 2
    $option2='Cust. VAT exemption No. : EU services, VAT 0% Intra-Community supply';
    $out.='<option';
    if ($current===$option2)$out.=' selected';
    $out.='>'.$option2.'</option>';
    $out.='</select>';
    return $out;
}
function sales_dispatch($disp,$qty){
    if ($disp<$qty)echo 'class="row_yellow"';
    elseif ($disp===$qty)echo 'class="row_green"';
    else echo 'class="row_red"';
}
function sales_vat_func($cur=0){
    $out='<select class="inp_vat" name="scont_vat[]" onchange="sales_total(this)">';
    $out.='<option value="0">0</option>';
    if ($cur==20) $out.='<option selected value="20">20%</option>';
    else $out.='<option value="20">20%</option>';
    $out.='</select>';
    return $out;
}