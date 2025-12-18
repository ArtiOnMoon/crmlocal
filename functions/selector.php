<?php
function selector($type,$headers='name="selector"',$current=0,$condition=''){
    $db =  db_connect();
    if ($type=='customers'){
        $query= 'SELECT cust_id, cust_full_name FROM customers WHERE cust_id="'.$current.'"';
        if ($condition!=''){
            if ($condition==='mngr')$query.=' AND is_mngr=1';
            elseif ($condition==='serv')$query.=' AND is_serv=1';
            elseif ($condition==='mnfr')$query.=' AND is_mnfr=1';
            elseif ($condition==='agnt')$query.=' AND is_agnt=1';
            elseif ($condition==='sppl')$query.=' AND is_sppl=1';
            elseif ($condition==='ownr')$query.=' AND is_ownr=1';
            elseif ($condition==='optr')$query.=' AND is_optr=1';
            elseif ($condition==='fchk')$query.=' AND is_fchk=1';
        }
        $result=$db->query($query);
        if($result->num_rows===0){
            $current_id='1';
            $current_name='';
        }
        else {
            $row=$result->fetch_assoc();
            @$current_id=$row['cust_id'];
            @$current_name=$row['cust_full_name'];
        }
    }
    elseif ($type=='stock_nmnc'){
        $query= 'SELECT stnmc_id, stnmc_descr,stnmc_pn,stnmc_type_model FROM stock_nmnc WHERE stnmc_id="'.$current.'"';
        if(!$result=$db->query($query)){
            $current_id='';
            $current_name='';
        }
        else {
            $row=$result->fetch_assoc();
            @$current_id=$row['stnmc_id'];
            @$current_name=$row['stnmc_pn'].' '.$row['stnmc_descr'].' '.$row['stnmc_type_model'];
        }
    }
    elseif ($type=='vessels'){
        $query= 'SELECT vessel_id, vessel_name FROM vessels WHERE vessel_id="'.$current.'"';
        if(!$result=$db->query($query)){
            $current_id='';
            $current_name='';
        }
        else {
            $row=$result->fetch_assoc();
            @$current_id=$row['vessel_id'];
            @$current_name=$row['vessel_name'];
        }
    }
    $db->close();
    $str='<span class="selector"><input type="text" class="selector_tosend_field"';
    $str.=$headers.'value="'.$current_id.'">';
    $str.='<input type="search" class="selector_input" value="'.$current_name.'" oninput="selector_search(this,\''.$type.'\',\''.$condition.'\')" onblur="selector_blur(this)"><button type="button" class="selector_button" onmousedown="selector_show(this,\''.$type.'\',\''.$condition.'\')">&#9660</button>';
    $str.='<div class="selector_results" onMouseDown="selector_select_item(event)"></div></span>';
    return $str;
}
function selector_customer($headers='name="selector"',$current=0,$condition=''){
    //Нестандартный обработчик при выборе из списка selector_cust_select_item(event)
    $db =  db_connect();
    $query= 'SELECT cust_id, cust_full_name,service_discount,discount FROM customers WHERE cust_id="'.$current.'"';
    if ($condition!=''){
        if ($condition==='mngr')$query.=' AND is_mngr=1';
        elseif ($condition==='serv')$query.=' AND is_serv=1';
        elseif ($condition==='mnfr')$query.=' AND is_mnfr=1';
        elseif ($condition==='agnt')$query.=' AND is_agnt=1';
        elseif ($condition==='sppl')$query.=' AND is_sppl=1';
        elseif ($condition==='ownr')$query.=' AND is_ownr=1';
        elseif ($condition==='optr')$query.=' AND is_optr=1';
        elseif ($condition==='fchk')$query.=' AND is_fchk=1';
    }
    $result=$db->query($query);
    if($result->num_rows===0){
        $current_id='1';
        $current_name='';
    }
    else {
        $row=$result->fetch_assoc();
        $current_id=$row['cust_id'];
        $current_name=$row['cust_full_name'];
    }
    $db->close();
    $str='<span class="selector"><input type="text" class="selector_tosend_field" data-discount="'.$row['discount'].'" data-service_discount="'.$row['service_discount'].'" ';
    $str.=$headers.'value="'.$current_id.'">';
    $str.='<input type="search" class="selector_input" value="'.$current_name.'" oninput="selector_search(this,\'customers\',\''.$condition.'\')" onblur="selector_blur(this)"><button type="button" class="selector_button" onmousedown="selector_show(this,\'customers\',\''.$condition.'\')">&#9660</button>';
    $str.='<div class="selector_results" onMouseDown="selector_cust_select_item(event)"></div></span>';
    return $str;
}
function selector_nmnc($headers='name="nmnc_id"',$current='', $category=''){
    $str='';
    if ($current!=''){
        $query='SELECT stnmc_type, stnmc_descr, stnmc_pn FROM stock_nmnc WHERE stnmc_id="'.$current.'"';
        $db = db_connect();
        $result=$db->query($query);
        $row=$result->fetch_row();
        $current_cat=$row[0];
        $current_elem=$row[1].' '.$row[2];
    }
    elseif ($category=!''){
        
    }
    //Stock class list
    $query='SELECT id, stock_cat_name FROM stock_cats ORDER BY stock_cat_name';
    $db = db_connect();
    $result=$db->query($query);
    if (!$result) return 'Error, code 1';
    $str.= '<span class="selector"><select class="nmnc_selector_category" onchange="nmnc_selector_cat_change(this)">';
    $str.= '<option value="">Select category</option>';
    while($row = $result->fetch_assoc()){
        $str.= '<option ';
        if ($row['id']==$current_cat) {$str.= 'selected ';}
        $str.= 'value="'.$row['id'].'">'.$row['stock_cat_name'].'</option>';
    }
    $str.= '</select>';
    //select nmnc
    $str.='<select '.$headers.'>';
    if ($current!=''){
        $str.='<option value="'.$current.'">'.$current_elem.'</option>';
    }
    $str.='</select><img src="icons_/refresh.png" alt="Update" class="refresh_button" onclick="nmnc_selector_click(this)"></span>';
    return $str;
}
function selector_equip_long(array $cat_list,array $manuf_list,$headers='name="sfd_equip_id[]"',$current='', $flag=0){
    if ($current!=''){
        $query='SELECT srv_eq_cat,srv_eq_manuf FROM service_equipment WHERE srv_eq_id="'.$current.'"';
        $db = db_connect();
        $result=$db->query($query);
        $row=$result->fetch_row();
        $current_cat=$row[0];
        $current_manuf=$row[1];
        $db->close();
    }
    else{ 
        $current_cat=0;
        $current_manuf=0;
        $current_elem=0;
    }
    $str='<span class="selector_equip_container';
    if ($flag===1)$str.= ' disabledbutton';
    $str.= '"><select onchange="selector_equip_long(this)"><option value="">Category</option>';
    foreach ($cat_list as $key=>$value){
        $str.='<option value="'.$key.'"';
        if ($key==$current_cat)$str.=' selected';
        $str.='>'.$value.'</option>';
    }
    $str.='</select><select onchange="selector_equip_long(this)"><option value="">Manufacturer</option>';
    foreach ($manuf_list as $key=>$value){
        $str.='<option value="'.$key.'"';
        if ($key==$current_manuf)$str.=' selected';
        $str.='>'.$value.'</option>';
    }
    $str.='</select><select '.$headers.'><option value="0"></option>';
    if ($current!=''){
        $query='SELECT srv_eq_id, srv_eq_name FROM service_equipment WHERE srv_eq_manuf="'.$current_manuf.'" AND srv_eq_cat="'.$current_cat.'" ORDER BY srv_eq_name';
        $db = db_connect();
        if(!$result=$db->query($query)) exit('<option="">Nothing found</option></select></span>');
        $db->close();
        while ($row=$result->fetch_assoc()){
            $str.='<option value="'.$row['srv_eq_id'].'"';
            if ($row['srv_eq_id']===$current)$str.=' selected';
            $str.='>'.$row['srv_eq_name'].'</option>'; 
        }
    }
    $str.='</select></span>';
    return $str;
}
function selector_nmnc_long(array $cat_list,array $manuf_list,$headers='name="stock_nmnc_id" required" class="selector_select"',$current='', $flag=0){
    if ($current!=''){
        $query='SELECT stnmc_type,stnmc_manuf FROM stock_nmnc WHERE stnmc_id="'.$current.'"';
        $db = db_connect();
        $result=$db->query($query);
        $row=$result->fetch_row();
        $current_cat=$row[0];
        $current_manuf=$row[1];
        $db->close();
    }
    else{ 
        $current_cat=0;
        $current_manuf=0;
        $current_elem=0;
    }
    $str='<span class="selector_equip_container';
    //if ($flag===1)$str.= ' disabledbutton';
    $str.= '"><select onchange="selector_nmnc_long(this)"><option value="">Category</option>';
    foreach ($cat_list as $key=>$value){
        $str.='<option value="'.$key.'"';
        if ($key==$current_cat)$str.=' selected';
        $str.='>'.$value.'</option>';
    }
    $str.='</select><select onchange="selector_nmnc_long(this)"><option value="">Manufacturer</option>';
    foreach ($manuf_list as $key=>$value){
        $str.='<option value="'.$key.'"';
        if ($key==$current_manuf)$str.=' selected';
        $str.='>'.$value.'</option>';
    }
    $str.='</select><select '.$headers.'><option value="0"></option>';
    if ($current!=''){
        $query='SELECT srv_eq_id, srv_eq_name FROM service_equipment WHERE srv_eq_manuf="'.$current_manuf.'" AND srv_eq_cat="'.$current_cat.'" ORDER BY srv_eq_name';
        $db = db_connect();
        if(!$result=$db->query($query)) exit('<option="">Nothing found</option></select></span>');
        $db->close();
        while ($row=$result->fetch_assoc()){
            $str.='<option value="'.$row['srv_eq_id'].'"';
            if ($row['srv_eq_id']===$current)$str.=' selected';
            $str.='>'.$row['srv_eq_name'].'</option>'; 
        }
    }
    $str.='</select></span>';
    return $str;
}
function selector_nmnc_linear($headers='',$headers_search='',$value_text='',$value_id='',$type=0){
    //type 0 - Purchase\Invoice, 1 - sales
    $out='<div class="selector_nmnc"><input type="search" '.$headers_search.' class="selector_nmnc_search_field';
    if ($value_id!='')$out.=' selector_has_nmnc" '; else $out.='" ';
    $out.='oninput="selector_nmnc_search(this)" onblur="selector_nmnc_blur(this)" ';
    $out.='value="'.$value_text.'">';
    $out.='<img title="View nomenclature" class="line_image" align="middle" src="/icons_/ex_link.png" onclick="stnmc_view_add2(this)">';
    $out.='<input type="hidden" class="selector_nmnc_tosend" '.$headers.' value="'.$value_id.'"">';
    if ($type=='0')$out.='<div class="selector_search_div" onclick="selector_nmnc_qte_selected(event)"></div></div>';
    //elseif ($type=='2')$out.='<div class="selector_search_div" onclick="selector_nmnc_invoice_selected(event)"></div></div>';
    else $out.='<div class="selector_search_div" onclick="selector_nmnc_selected(event)"></div></div>';
    return $out;
}
//COMBOSELECT
function comboselect_rates($current_text='',$current=0,$headers='name="entry_text[]"'){
    $var='<div class="comboselect_container">';
    $var.='<input type="text" '.$headers.' value="'.$current_text.'">';
    $var.='<select name="entry_base_id[]" onclick="comboselect_rates_click(this)" onchange="comboselect_rates_change(this)">';
    $var.='<option value="0"></option>';
    $query='SELECT service_rates.*, curr_name FROM service_rates LEFT JOIN currency ON curr_id=rate_currency';
    $db = db_connect();
    $result=$db->query($query);
    while($row2=$result->fetch_assoc()){
        $var.= '<option data-currency="'.$row2['rate_currency'].'" data-price="'.$row2['rate_price'].'" data-cat_id="'.$row2['rate_cat'].'"';
        if ($row2['rate_id']===$current) $var.= 'selected ';
        $var.= ' value="'.(int)$row2['rate_id'].'">'.$row2['rate_name'].'</option>';
    }
    $var.='</optgroup>';
    $var.='</select>';
    return $var;
}
function comboselect_rates_cat($current_text='',$current=0,$headers='class="input_header" name="entry_text[]"', $select_headers='name="entry_base_id[]" class="comboselect_rates_sel" onchange="comboselect_rates_change(this)"'){
    $var='<div class="comboselect_container">';
    $var.='<input type="text" '.$headers.' value="'.$current_text.'">';
    $var.='<select '.$select_headers.'>';
    $var.='<option value=""></option>';
    $query='SELECT * FROM service_rates_cat WHERE rate_cat_id > 1 AND (SELECT COUNT(rate_id) FROM service_rates)>0';
    $db = db_connect();
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
            $var.= '<option value="'.(int)$row['rate_cat_id'].'"';
            if ($row['rate_cat_id']==$current) $var.= 'selected ';
            $var.= '>'.$row['rate_cat_name'].'</option>';
    }
    $var.='</select>';
    return $var;
}
function calc_selector_nmnc($current_base_id=0,$current_text='',$condition=''){
    $db =  db_connect();
    $query= 'SELECT stnmc_id, stnmc_descr,stnmc_pn,stnmc_type_model FROM stock_nmnc WHERE stnmc_id="'.$current_base_id.'"';
    if ($current_base_id!==0){
        if(!$result=$db->query($query)){$current_id='';$current_name='Nothing selected';}
        else {
            $row=$result->fetch_assoc();
            $current_id=$row['stnmc_id'];
            $current_name=trim($row['stnmc_pn'].' '.$row['stnmc_descr'].' '.$row['stnmc_type_model']);
        }
    }
    else {$current_id='';$current_name='';}
    $db->close();
    ?>
    <span class="selector">
        <input type="text" class="selector_tosend_field" name="entry_base_id[]" value="<?php echo $current_base_id;?>">
        <input type="search" name="entry_text[]" class="selector_input calc_long_input" value="<?php echo $current_text;?>" oninput="calc_selector_search(this,'<?php echo $condition;?>')" onblur="calc_selector_blur(this)"><button type="button" class="selector_button" onmousedown="selector_show(this,'stock_nmnc','<?php echo $condition;?>')">&#9660;</button>
        <div class="selector_results" onMouseDown="calc_selector_select_item(event)"></div>
    </span>
    <?php
}
function selector_multi_stock_status(array $current_list=[],$headers='id="stock_id"',$func="show_stock_new_table()"){
    $db=db_connect();
    $query='SELECT * FROM stock_status';
    if(!$result=$db->query($query)){
        $current_id='';$current_name='Nothing selected';
        exit('Error retrieving data');
    }
    ?>
    <span class='stock_selector_conteiner'>
        <input class='stock_selector_input' value="Select status" type="text" readonly onclick="display_switch('stock_status_selector_window')">
        <div class='stock_selector_window' id='stock_status_selector_window' style='display:none;'>
        <?php
        while ($row = $result->fetch_assoc()){
            echo'<div><label><input class="stock_status_selector_check" type="checkbox" ';
            if (in_array($row['stock_stat_id'], $current_list))echo 'checked ';
            echo'value="',$row['stock_stat_id'],'">',$row['stock_stat_name'],'</div>';
        }
        ?>
            <div onclick="stock_check_all(this)" style="margin: 4px 0px;"><a href="#">Check all</a></div>
            <a class="knopka" href="#" onclick="<?php echo $func;?>">Apply</a>
        </div>
    </span>
    <?php
}