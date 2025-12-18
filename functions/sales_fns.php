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
function select_sale_status($current, $headers='name="sale_status"',$type=0){
    $out='<select '.$headers.'>';
    $db =  db_connect();
    $query= 'SELECT * FROM sales_statuses order by status_order';
    if(!$result=$db->query($query))$out.='<option></option>';
    if($type===1)$out.='<option selected>All</option>';
    while ($row=$result->fetch_assoc()){
        $out.='<option ';
        if ($row['sales_stat_id']==$current) $out.='selected ';
        $out.='value='.$row['sales_stat_id'].'>'.$row['sales_stat_name'].'</option>';
    }
    $out.='</select>';
    return $out;
}
function sales_color_table($status, $ship_date=''){
    $out='';
    if ($status=='2' and $ship_date!='' and (strtotime($ship_date)-time())<60*60*24*7) $out.= 'blink row_confirmed'; //Confirmed
    if ($status=='2')$out.= 'row_confirmed';//Confirmed
    elseif ($status=='3') $out.= 'row_brown';//Dispatched
    elseif ($status=='4') $out.= 'row_complete';//Completed
    elseif ($status=='5') $out.= 'row_yellow';//Qoutation
    elseif ($status=='6' ) $out.= 'row_grey';//Canceled
    elseif ($status=='9') $out.= 'blink';//Expired
    elseif ($status=='10') $out.= 'row_blue';//Delivered
    else $out.= 'row_white';
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