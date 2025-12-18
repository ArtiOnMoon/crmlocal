<?php
$service_statuses=array('1'=>'Get reqst','2'=>'Quotation','3'=>'Cfm/ETA','4'=>'Cfm/ETD','5'=>'Canceled', '6'=>'Completed');
function select_service_status ($current_status,$headers='name="new_service_status"', $type=0){
    $out='<select '.$headers.'>';
    $query='select * from service_statuses ORDER BY serv_stat_sort';
    $db = db_connect();
    $result=$db->query($query);
    if ($type===1) $out.='<option selected>All</option>';
    while($row = $result->fetch_assoc()){
        $out.= '<option value="'.$row['serv_stat_id'].'"';
        if ($current_status==$row['serv_stat_id']) $out.= ' selected ';
        $out.= '>'.$row['serv_stat_name'].'</option>';
    }
    $out.= '</select>';
    return $out;
};
function service_status_encode($current){
    foreach ($GLOBALS['service_statuses'] as $key=>$value){
        if ($current==$key) return $value;
    }
    return 'Not set';
}
function select_service_rates_cat($current=''){
    $var='';
    $query='select service_rates_cat.*, our_companies.our_name from service_rates_cat LEFT JOIN our_companies ON rate_our_comp=id';
    $db = db_connect();
    $result=$db->query($query);
    $var.='<select required name="service_rates_category" onchange="slelect_control(this)">';
    while($row = $result->fetch_assoc()){
        $var.= '<option ';
        if ($row['rate_cat_id']==$current) $var.= 'selected ';
        $var.= 'value="'.$row['rate_cat_id'].'">'.'['.$row['our_name'].'] '.$row['rate_cat_name'].'</option>';
    }
    $var.='</select>';
    return $var;
}
function select_service_rate($current='',$flag=0){
    $var.='<select required name="service_rates"';
    if ($flag===0) $var.=' id="select_service_rate" class="rates_select" onchange="slelect_control(this)"';
    else $var.=' width="100%" onchange="rate_calc(this)"';
    $var.='>';
    $var.='<option value=""></option>';
    $query='SELECT * FROM service_rates_cat WHERE rate_cat_id > 1 AND (SELECT COUNT(rate_id) FROM service_rates)>0';
    $db = db_connect();
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $query2='select service_rates.*, curr_name from service_rates LEFT JOIN currency ON curr_id=rate_currency, service_rates_cat where rate_cat=rate_cat_id and rate_cat="'.$row['rate_cat_id'].'"';
        $result2=$db->query($query2);
        $var.= '<optgroup label="'.$row['rate_cat_name'].'">';
        while($row2=$result2->fetch_assoc()){
            $var.= '<option data-currency="'.$row2['rate_currency'].'" data-price="'.$row2['rate_price'].'"';
            if ($row2['rate_id']==$current) $var.= 'selected ';
            $var.= ' value="'.$row2['rate_id'].'">'.$row2['rate_name'].'</option>';
        }
        $var.='</optgroup>';
    }
    //NO CAT Rates
    $query3='select service_rates.*, curr_name from service_rates LEFT JOIN currency ON curr_id=rate_currency, service_rates_cat where rate_cat=rate_cat_id and rate_cat=1';
    $result3=$db->query($query3);
    if ($result->num_rows===0) return 'Error';
    $var.= '<optgroup label="No category">';
    while($row3=$result3->fetch_assoc()){
        $var.= '<option data-currency="'.$row3['rate_currency'].'" ';
        if ($row3['rate_id']==$current) $var.= 'selected ';
        $var.= ' value="'.$row3['rate_id'].'">'.$row3['rate_name'].'</option>';
    }
    $var.='</optgroup></select>';
    return $var;
}
function user_multiselect ($users_list=[]){
    $db= db_connect();
    $query='SELECT uid,full_name,user_deleted FROM users WHERE is_technician="1" ORDER BY u_comp_id,user_code';
    $result=$db->query($query);
    echo '<div id="user_select">';
    while ($row = $result->fetch_assoc()) {
        if ($row['user_deleted']==="1" and !in_array($row['uid'], $users_list))continue;
        ?>
        <label <?php if ($row['user_deleted']==='1') echo 'class="disabledbutton"';?>>
            <input type="checkbox" name="users[]" value="<?php echo $row['uid'];?>"
            <?php if(in_array($row['uid'], $users_list))echo' checked';?>
            >
            <?php echo $row['full_name'];?>
        </label><br>
    <?php }
    $db->close();
    echo'</div>';
}
function user_multiselect2 ($users_list=[]){
    $db= db_connect();
    $query='SELECT uid,full_name,user_code,user_deleted FROM users WHERE is_technician="1" ORDER BY full_name';
    $result=$db->query($query);
    echo '<div id="user_select2">';
    while ($row = $result->fetch_assoc()) {
        if ($row['user_deleted']==="1" and !in_array($row['uid'], $users_list))continue;
        ?>
        <label <?php if ($row['user_deleted']==='1') echo 'class="disabledbutton"';?>>
            <input type="checkbox" name="users[]" value="<?php echo $row['uid'];?>"
            <?php if(in_array($row['uid'], $users_list))echo' checked';?>
            >
            <?php echo $row['user_code'];?>
        </label>
    <?php }
    $db->close();
    echo'</div>';
}
function user_multiselect3 ($users_list=[]){
    $db= db_connect();
    $query='SELECT uid,last_name,user_code,user_deleted FROM users WHERE is_technician="1" ORDER BY u_comp_id,user_code';
    $result=$db->query($query);
    $user_string='';
    $div_body='';
    while ($row = $result->fetch_assoc()){
        if ($row['user_deleted']==="1" and !in_array($row['uid'], $users_list)) {continue;}
        $div_body.='<div><label><input type="checkbox" data-uid="'.$row['user_code'].'" name="users[]" onchange="user_select_save(this)" value="'.$row['uid'].'"';
                
        if(in_array($row['uid'], $users_list)){
            $user_string.=$row['user_code'].' ';
            $div_body.=' checked';
        }
        $div_body.='>'.$row['last_name'].' ('.$row['user_code'].')</label></div>';
    }
    $db->close();
    $div_body.='<a href="#" onclick="user_select_close(this)" style="margin:auto;">Close</a>';
    echo '<input class="selector_input" id="user_select_input" type="text" readonly onclick="user_select(this)" value="'.$user_string.'"><button type="button" href="#" onclick="user_select(this)" class="selector_button">&#9660;</button>';
    echo '<div id="user_select3">'.$div_body.'</div>';
}
function service_proforma_display($service_id, $proforma_id,$total,$currency){
    if ($proforma_id==''){
        return '<a href="service_proforma.php?service_id='.$service_id.'">Add calculation</a>';
    }
    else return '<a href="service_proforma.php?service_id='.$service_id.'" class="proforma_display_div" onmouseenter="dislpay_service_proforma(this)" data-id="'.$proforma_id.'">'.$total.' '.$currency.'</a>';
}
function select_agent ($current_comp=0,$current_cont=0, $headers='name="service_agent" onchange="select_agent_contact(this)" class="selector_select"',$headers2='name="agent_contact_id" class="selector_select" id="contact_id"'){
    $db=db_connect();
    $str.= selector('customers',$headers,$current_comp,'agnt');
    $str.= ' PIC: <select '.$headers2.'>';
    $query= 'select id, name FROM customers_contacts WHERE customer_id ="'.$current_comp.'" AND deleted=0';
    $str.='<option value="NULL"></option>';
    $result=$db->query($query);
    if($result->num_rows>0){
        while ($row=$result->fetch_assoc()){
            $str.='<option value="'.$row['id'].'"';
            if ($row['id']==$current_cont) $str.=' selected';
            $str.='>'.$row['name'].'</option>';
        }
    }
    $db->close();
    $str.='</select>';
    return $str;
}
function select_vessel ($vessel_id='',$headers='required name="new_vessel" class="combobox"', $type=0,$updatable=0){
    if ($updatable===1){
        echo'<img src="./icons_/refresh.png" alt="Update" class="refresh_button" onclick="java_refresh_func(this,\''.$comp_id.'\',\''.$type.'\',\'\',2)">';
    }
    $db= db_connect();
    $query='select vessel_name, vessel_id from vessels WHERE vessel_id>1 AND vessel_deleted=0 ORDER BY vessel_name';
    $result=$db->query($query);
    echo '<select '.$headers.'>';
    if ($type==1) echo '<option selected value="All">All</option>';
    echo '<option value="1"';
    if ($row['vessel_id']=='1') echo 'selected ';
    echo'>No name</option>';
    while($row = $result->fetch_assoc()){
        echo '<option ';
        if ($row['vessel_id']==$vessel_id) echo 'selected ';
        echo 'value="'.$row['vessel_id'].'">'.$row['vessel_name'].'</option>';
    }
    echo '</select>';
};
function select_service_type($current,$headers='name="sfd_type[]"'){
    $db= db_connect();
    $query='SELECT id, type_text FROM service_types';
    $result=$db->query($query);
    $str.= '<select '.$headers.'">';
    while($row = $result->fetch_assoc()){
        $str.= '<option ';
        if ($row['id']==$current) $str.= 'selected ';
        $str.= 'value="'.$row['id'].'">'.$row['type_text'].'</option>';
    }
    $db->close();
    $str.= '</select>';
    return $str;
}
function service_color_table($val,$eta){
    $out='';
    if ($eta!='' and $val==="3"){
        if((strtotime($eta)-time())<60*60*24*3) echo $out.='blink ';
    }
    if ($val=='2')$out.= 'row_yellow';//Qoutation
    elseif ($val=='3') $out.= 'row_confirmed';//Confirmed
    elseif ($val=='4') $out.= 'row_brown';//Proceed
    elseif ($val=='5') $out.= 'row_grey';//Canceled
    elseif ($val=='6') $out.= 'row_complete';//Completed
    elseif ($val=='7') $out.= 'row_green';//Follow_UP
    elseif ($val=='8') $out.= 'row_red';//Expired
    elseif ($val=='9') $out.= 'row_violet';//Post-processing
    else $out.= 'row_white';
    return $out;
}
function select_sr_form($value='', $headers='name="sr_form"'){
    $m=array('MSS', 'AZP', 'AEP', 'Sperry Marine');
    $out='<select '.$headers.'>';
    foreach ($m as $elem){
        $out.= '<option';
        if ($elem===$value) $out.=' selected';
        $out.= '>'.$elem.'</option>';
        }
    $out.='</select>';
    return $out;
};
function service_id_num($service_id, $our_comp=1){
    if ($our_comp==1){
        return numberFormat($service_id, 5);
    }
    else return $service_id;
}
function select_pay_type($current=0,$headers='name="srv_pay_type"'){
    $str='<select '.$headers.'>';
    $arr = array(0=>'',1=>'Inv',2=>'Счёт');
    foreach ($arr as $key=>$value){
        $str.='<option value="'.$key.'"';
        if ($key==$current)$str.=' selected';
        $str.= '>'.$value.'</option>';
    }
    $str.='</select>';
    return $str;
}
function return_pay_type($val,$serv,$inv){
    if ($inv!='') return $inv;
    if ($val==0)return $serv;
    elseif ($val==1)return 'Inv '.$serv;
    else return 'Счёт '.$serv;
}

function select_pass_status($current, $headers='name="passes_status"'){
    $out='<select '.$headers.'>';
    $statuses = [0=>'Not sent', 1=>'Requested',2=> 'Ready',3=>'Failed', 4=>'Not required'];
    $i=0;
    foreach ($statuses as $key=>$value) {
        $i++;
        $out.='<option value="'.$key.'"';
        if ($key == $current) $out.=' selected';
        $out.='>'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}