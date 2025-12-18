<?php
function stock_id_link ($id){
     $var='<a href="view_stock_item.php?id='.$id.'">'.$id.'</a>';
    return $var;
}
function view_stock_link ($id,$text){
     $var='<a href="view_stock_item.php?id='.$id.'">'.$text.'</a>';
    return $var;
}
function select_stock_status($status='',$type=0){
    $stock_statuses=['to service', 'sold', 'defect', 'to spair', 'in stock', 'deconsolidated','on the way', 'for checking'];
    echo '<select name="stock_item_status" onchange="select_control(this)" ';
    if ($type===2) echo'id="stock_status">'; 
    elseif ($type===1) echo'id="stock_status"><option value="All" selected>All</option>'; 
    elseif ($type===3) echo '><option class=greytext value="">No change</option>';
    else echo'>';
    foreach ($stock_statuses as $key){
        echo '<option ';
        if ($type===0 AND $key===$status) {echo 'selected ';}
        echo 'value="'.$key.'">'.$key.'</option>';
    }
    echo '</select>';
}
function select_complect($current='',$field_id='new_complect_id'){
    $types=array('No','Main item', 'Part item');
    $out='<select name="is_complect" id="is_complect" onchange="complect_disable_control(this,\''.$field_id.'\')">';
    $i=0;
    foreach ($types as $key){
        $out.='<option value="'.$i.'"';
        if ($i===(int)$current)$out.=' selected'; 
        $out.='>'.$key.'</option>';
        $i++;
    }
    $out.='</select>';
    return $out;
}
function view_stocklist_item ($id, $name){
    return '<a href="stocklist_view.php?id='.$id.'">'.$name.'<a>';    
}
function select_stock($current='', $headers='name="stock"', $type=0){
    $out='<select '.$headers.'/>';
    $query= 'select stockl_id, stockl_name from stock_list';
    $db= db_connect();
    $result=$db->query($query);
    if ($type===1)$out.='<option value="All">All</option>';
    while($row = $result->fetch_assoc()){
        $out.='<option ';
        if ($current==$row['stockl_id']) $out.='selected ';
        $out.='value="'.$row['stockl_id'].'">'.$row['stockl_name'].'</option>';
    }
    $out.='</select>';
    return $out;
}
function days_left($date){
    if ($date=='')return 'not set';
    $date1= strtotime($date);
    $date2= strtotime("now");
    $days=($date2-$date1)/86400;
    return floor($days);
}
function balance ($val){
    if ($val=="1") return '&#10004;';
    else return '&#10008;';
}
function condition_decode($val){
    if($val=='1') return '<td class="row_green">new</td>';
    elseif ($val=='2') return '<td class="row_yellow">used</td>';
    elseif ($val=='3') return '<td class="row_red">defect</td>';
    elseif ($val=='4') return '<td class="row_blue">restored</td>';
    return 'not set';
}
function select_condition($current='0',$type='0',$headers='name="cond"'){
    $out='<select '.$headers.'>';
    $array=array(
        '1'=>'new',
        '2'=>'used',
        '3'=>'defect',
        '4'=>'restored',
        );
    if ($type===1) $out.='<option selected value="0">All</option>';
    elseif ($type===2) $out.='<option selected value=""></option>';
    foreach ($array as $i=>$value){
        $out.='<option value="'.$i.'"';
        if ($i==$current) $out.=' selected';
        $out.='>'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}
function select_stock_stat($current='0',$type='0',$headers='onchange="select_control(this)" name="stock_item_status"'){
    $out='<select '.$headers;
    if ($type===1) $out.=' id="stock_status"><option value="All">All</option>';
    elseif ($type===2) $out.='><option value="" selected></option>';
    else $out.='>';
    $query='SELECT * FROM stock_status';
    $db = db_connect();
    $result=$db->query($query);
    WHILE ($row=$result->fetch_assoc()){
      $out.= '<option ';
        if ($row['stock_stat_id']==$current) {$out.= 'selected ';}
        $out.= 'value="'.$row['stock_stat_id'].'">'.$row['stock_stat_name'].'</option>';
    }
    $out.= '</select>';
    return $out;   
}
function color_table($val,$cat=1){
    if ($val=='1')$color='row_green';
    elseif ($val=='2') $color='row_yellow';
    elseif ($val=='3') $color='row_red';
    elseif ($val=='4') $color='row_brown';
    elseif ($val=='5') $color='row_grey';
    elseif ($val=='6') $color='row_confirmed';
    elseif ($val=='7') $color='row_complete';
    elseif ($val=='8') $color='row_blue';
    elseif ($val=='9') $color='row_violet';
    elseif ($val=='10') $color='row_pink';
    else $color='white';
    return 'class="'.$color.'"';
};
function stock_cat_decode($val){
    $query='select * from stock_cats';
    $db = db_connect();
    $result=$db->query($query);
    if($val=='1') return 'Gyro';
    elseif ($val=='2') return 'VDR';
    elseif ($val=='3') return 'Magnetron';
    elseif ($val=='4') return 'GPS';
    elseif ($val=='5') return 'ECDIS';
    elseif ($val=='6') return 'Radar';
    elseif ($val=='7') return 'Autopilot';
    elseif ($val=='8') return 'Ais';
    elseif ($val=='9') return 'Speed log';
    elseif ($val=='10') return 'Spares';
    elseif ($val=='11') return 'other';
    return 'not set';
}
function select_stock_on_balance($val='',$headers='name="on_balance"'){
    $out='';
    $out.='<select '.$headers.'>'
            . '<option value="1">Yes</option>'
            . '<option value="0"';
    if ($val=='0')$out.=' selected';
    $out.='>No</option></select>';
    return $out;
}
function stock_view_sales($comp,$num,$type=1){
    if ($num=='')return;
    elseif ($comp=='')return $num;
    if ($type=="1"){
        return '<a href="#" onclick="sales_view(\''.$comp.'\',\''.$num.'\')">'.$comp.'.'.$num.'</a>'; 
    } else {
        return '<a href="#" onclick="view_service_order(\''.$comp.'\',\''.$num.'\')">'.$comp.'.'.$num.'</a>'; 
    }
}
function stock_view_sales_short($comp,$num,$type=1){
    if ($num=='')return;
    elseif ($comp=='')return $num;
    if ($type=="1"){
        return '<a href="#" onclick="sales_view(\''.$comp.'\',\''.$num.'\')"><img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png"></a>'; 
    } else {
        return '<a href="#" onclick="view_service_order(\''.$comp.'\',\''.$num.'\')"><img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png"></a>'; 
    }
}
function stock_view_po_short($comp,$num,$type=1){
    if ($num=='')return;
    elseif ($comp=='')return $num;
    if ($type=="1"){
        return '<a href="#" onclick="purchase_view(\''.$comp.'\',\''.$num.'\')"><img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png"></a>'; 
    }elseif ($type=="2"){
        return '<a href="#" onclick="view_service_order(\''.$comp.'\',\''.$num.'\')"><img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png"></a>'; 
    } elseif ($type=="3") {
        return '<a href="#" onclick="sales_view(\''.$comp.'\',\''.$num.'\')"><img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png"></a>';
    }  
}
function stock_view_po($comp,$num,$type=1){
    if ($num=='')return;
    elseif ($comp=='')return $num;
    if ($type=="1"){
        return '<a href="#" onclick="purchase_view(\''.$comp.'\',\''.$num.'\')">'.$comp.'.'.$num.'</a>'; 
    }elseif ($type=="2"){
        return '<a href="#" onclick="view_service_order(\''.$comp.'\',\''.$num.'\')">'.$comp.'.'.$num.'</a>'; 
    } elseif ($type=="3") {
        return '<a href="#" onclick="sales_view(\''.$comp.'\',\''.$num.'\')">'.$comp.'.'.$num.'</a>';
    }    
}
function stock_view_link($stock_id,$stock_compl_id='',$stock_is_compl=0){
    $out='';
    //if ($stock_compl_id!='')$out.='<td><a href="#" onclick="stock_edit('.$stock_compl_id.')">C'.$stock_compl_id.'</a></td><td>';
    if ($stock_compl_id!='')$out.='<td><a href="#" onclick="stock_edit('.$stock_compl_id.')">compl.</a></td><td>';
    else $out.='<td colspan="2">';
    if ($stock_is_compl==0) $stock_display_id=$stock_id; else $stock_display_id='C'.$stock_id;
    $out.='<a href="#" onclick="stock_edit('.$stock_id.')">'.$stock_display_id.'</a></td>';
    return $out;
}
function stock_view_complect_link($stock_id,$level=1){
    $out='';
    $real_level=6-$level;
    If ($real_level<1)$real_level=1;
    for ($i=1;$i<=$level;$i++){
        $out.="<td></td>";
    }
    $out.='<td colspan="'.$real_level.'"><a href="#" onclick="stock_edit('.$stock_id.')">'.$stock_id.'</a></td>';
    return $out;
}
function select_stock_link_type($current='', $headers=''){
    $array=['1'=>'Sales','2'=>'Service'];
    $out='<select '.$headers.' name="stock_so_type">';
    foreach ($array as $key=>$value){
        $out.='<option ';
        if ($key==$current) {$out.= 'selected ';}
        $out.= 'value="'.$key.'">'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}
function select_stock_po_link_type($current='',$headers=''){
    $array=['1'=>'Purchase','2'=>'Service','3'=>'Sales'];
    $out='<select '.$headers.' name="stock_po_type">';
    foreach ($array as $key=>$value){
        $out.='<option ';
        if ($key==$current) {$out.= 'selected ';}
        $out.= 'value="'.$key.'">'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}
function stock_tr_is_sold($status){
    if ($status==='3')return 'stock_tr_sold';
    else return '';
}

//NEW_STOCK_FUNCTIONS
function select_stock_nmnc($current=0, $headers='name="nmnc_id" required class="combobox"'){
    $out='<select '.$headers.'>';
    $db= db_connect();
    $query='SELECT stnmc_id, stnmc_descr FROM stock_nmnc WHERE stnmc_deleted=0';
    if (!$result=$db->query($query)){
        $out.='<option>Error</option></select>';
        return $out;
    }
    $out.='<option></option>';
    while($row = $result->fetch_assoc()){
       $out.='<option value='.$row['stnmc_id'].'>'.$row['stnmc_descr'].'</option>';
    }
    $out.='</select>';
    return $out;
}