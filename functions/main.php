<?php

//PARAM
$sales_gloobal_multiplier=1;
//END PARAM


function display_login_form () {
    print '
    <form method="post" action="auth.php">
        <table align="right">
            <tr>
                <td>Login</td><td><input type="text" name="username"></td>
            </tr>
            <tr>
                <td>Password</td><td><input type="password" name="password"></td>
            </tr>
            <tr>
                <td></td><td><input method="post" type="submit" value="Log In"></td>
            </tr>
    </table>
    </form>';
}
function destroySession() {
	if ( session_id() ) {
		// Если есть активная сессия, удаляем куки сессии,
		setcookie(session_name(), session_id(), time()-60*60*24);
		// и уничтожаем сессию
		session_unset();
		session_destroy();
	}
}
function getUrl() {
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
  $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
  $url .= $_SERVER["REQUEST_URI"];
  return $url;
}
function long_td_string($val,$length=50){
        if (mb_strlen($val)>$length) return '<td title="'.$val.'">'.mb_substr($val,0,$length).'	..&#9660;</td>';
        else return '<td>'.$val.'</td>';
}
function view_company_link ($cust_name, $cust_id){
    if ($cust_id=='1' or $cust_id=='') return '';
    $var='<a href="#tab1" onclick="cust_edit(\''.$cust_id.'\')">'.$cust_name.'</a>';
    return $var;
}
function view_order_link ($service_our_comp,$service_no,$order=''){
    if ($order==='')$order=$service_id;
    $var='<a href="#" onclick="view_service_order(\''.$service_our_comp.'\',\''.$service_no.'\')">'.$order.'</a>';
    return $var;
}
function view_link($comp,$num,$type=1,$text='Link'){
    // 1 - SERVICE, 2 - SALES, 3 - PO, 4 - INVOICE ...
    if ($num=='')return $text;
    elseif ($comp=='')return $text;
    switch ($type){
        case 1:
            return '<a href="#" onclick="view_service_order(\''.$comp.'\',\''.$num.'\')">'.$text.'<img title="Click to open" class="line_image" align="middle" src="/icons_/ex_link.png"></a>';
        case 2:
            return '<a href="#" onclick="sales_view(\''.$comp.'\',\''.$num.'\')">'.$text.'<img title="Click to open" class="line_image" align="middle" src="/icons_/ex_link.png"></a>';
        case 3:
            return '<a href="#" onclick="purchase_view(\''.$comp.'\',\''.$num.'\')">'.$text.'<img title="Click to open" class="line_image" align="middle" src="/icons_/ex_link.png"></a>';
        case 4:
            return '<a href="#" onclick="invoice_view(\''.$comp.'\',\''.$num.'\')">'.$text.'<img title="Click to open" class="line_image" align="middle" src="/icons_/ex_link.png"></a>';
    } 
}
//function view_order($type,$id,$text){
//    //1-Service 2-Sales 3-Purchase 4-invoice
//    $var='<a href="#" onclick="view_order_link(\''.$type.'\',\''.$id.'\')">'.$text.'</a>';
//    return $var;
//}
function view_equipment_link ($name, $id){
    $var='<a href="#" onclick="vessel_equipment_view('.$id.')">'.$name.'</a>';
    return $var;
}
function view_our_company_link ($id,$name=''){
    If ($name!==''){
        $var='<a href="our_companies_view.php?id='.$id.'">'.$name.'</a>';
        return $var;
    }
    $query='select our_name from our_companies where id="'.$id.'"';
    $db = db_connect();
    $result=$db->query($query);
    if ($result->num_rows!==1) return 'Not set';
    $row = $result->fetch_assoc();
    $var='<a href="our_companies_view.php?id='.$id.'">'.$row['our_name'].'</a>';
    return $var;
}
function view_vessel_link ($vessel_name, $vessel_id){
    if ($vessel_id=='1') return $vessel_name;
    return '<a href="#" onclick="vessel_view(\''.$vessel_id.'\')">'.$vessel_name.'</a>';
}
function view_sales_link ($sales_our_comp,$sales_no,$sales_name=''){
    if ($sales_name=='')$sales_name=$sales_our_comp.'.'.$sales_no;
    return '<a href="#" onclick="sales_view(\''.$sales_our_comp.'\',\''.$sales_no.'\')">'.$sales_name.'</a>';
}
function view_purchase_link ($comp_id,$po_no='',$text=''){
    if ($po_name=='')$po_name=$po_id;
    if ($text!=''){
        return '<a href="#" onclick="purchase_view(\''.$comp_id.'\',\''.$po_no.'\')">'.$text.'</a>';
    }
    return '<a href="#" onclick="purchase_view(\''.$comp_id.'\',\''.$po_no.'\')">'.$comp_id.'.'.$po_no.'</a>';
}
function view_po($id,$comp,$num){
    if ($num=='')return;
    elseif ($comp=='')return $num;
    if ($id=='')return $comp.'.'.$num;
    return '<a href="#" onclick="sales_view(\''.$id.'\')">'.$comp.'.'.$num.'</a>';    
}
function view_sales($id,$comp,$num){
    if ($num=='')return;
    elseif ($comp=='')return $num;
    if ($id=='')return $comp.'.'.$num;
    return '<a href="#" onclick="sales_view(\''.$id.'\')">'.$comp.'.'.$num.'</a>';    
}
function view_order($type,$comp,$number){
    //1-Service 2-Sales 3-PO 4-invoice
    if ($number == '' or $comp=='' or $type=='') return;
    if ($type == 1) {
        return '<a href="#" onclick="view_service_order(\''.$comp.'\',\''.$number.'\')">'.numberFormat($number,5).'</a>';
    }
    elseif ($type == 2) {
        return '<a href="#" onclick="sales_view(\''.$comp.'\',\''.$number.'\')">'.numberFormat($comp,2).'.'.$number.'</a>';
    }
    elseif ($type == 3) {
        return '<a href="#" onclick="purchase_view(\''.$comp.'\',\''.$number.'\')">'.numberFormat($comp,2).'.'.numberFormat($number,4).'</a>';
    }
    return;
}
function view_order_by_id($type,$id){
    //1-Service 2-Sales 3-PO
    if ($id == '') return;
    if ($type == 1) return '<img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png" onclick="service_view_by_id('.$id.')">';
    elseif ($type == 2) return '<img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png" onclick="sales_view_by_id('.$id.')">';
    elseif ($type == 3) return '<img title="View complect" class="line_image" align="middle" src="/icons_/ex_link.png" onclick="purchase_view_by_id('.$id.')">';
    return;
}
function select_country ($current='185', $headers='name="country" class="combobox"',$type=0){
    $query.='SELECT id, name FROM countries ORDER BY name';
    $db = db_connect();
    $result=$db->query($query);
    echo '<select '.$headers.' style="overflow:auto; max-width:200px">';
    if ($type===1) {echo'<option>All</option>';}
    elseif ($type===2) {echo'<option></option>';}
    while($row = $result->fetch_assoc()){
        echo '<option value="'.$row['id'].'"';
        if ($row['id']==$current) {echo 'selected ';}
        echo '>'.$row['name'].'</option>';
    }
    echo '</select>';
    $db->close();
}
function select_customer ($comp_id='', $type=0,$headers='class="combobox" name="new_customer"', $cat='',$updatable=0){
    if ($updatable===1){
        $str='<img src="icons_/refresh.png" alt="Update" class="refresh_button" onclick="java_refresh_func(this,\''.$comp_id.'\',\''.$type.'\',\''.$cat.'\',1)">';
    }
    else $str='';
    $query='select cust_full_name, cust_id from customers where deleted=0';
    if ($cat!=''){
        if ($cat=='agnt')$query.=' and is_agnt=1';
        elseif ($cat=='mnfr')$query.=' and is_mnfr=1';
        elseif ($cat=='splr')$query.=' and is_sppl=1';
        elseif ($cat=='serv')$query.=' and is_serv=1';
        elseif ($cat=='ownr')$query.=' and is_ownr=1';
        elseif ($cat=='mngr')$query.=' and is_mngr=1';
        elseif ($cat=='optr')$query.=' and is_optr=1';
    }
    $query.=' ORDER BY cust_full_name';
    $db = db_connect();
    $result=$db->query($query);
    $str.= '<select required '.$headers.'">';
    if ($type=='1')$str.= '<option value="All">All</option>';
    else $str.= '<option value=""></option>';
    while($row = $result->fetch_assoc()){
        $str.='<option ';
        if ($row['cust_id']==$comp_id) $str.= 'selected ';
        $str.= 'value="'.$row['cust_id'].'">'.$row['cust_full_name'].'</option>';
    }
    $str.= '</select>';
    $db->close();
    return $str;
};
function select_customer2 ($comp_id='', $headers='class="combobox" name="new_customer"'){
    $str='';
    $query='select cust_short_name, cust_id from customers';
    $db = db_connect();
    $result=$db->query($query);
    $str.= '<select required '.$headers.'>';
    $str.= '<option value="All">All</option>';
    while($row = $result->fetch_assoc()){
        $str.='<option ';
        if ($row['cust_id']==$comp_id) $str.= 'selected ';
        $str.= 'value="'.$row['cust_id'].'">'.$row['cust_short_name'].'</option>';
    }
    $str.= '</select>';
    return $str;
};
function select_manufacturer ($comp_id='', $headers='name="manufacturer"',$type=0){
    $str='';
    $query='SELECT mnf_short_name, mnf_id FROM manufacturers ORDER BY mnf_short_name';
    $db = db_connect();
    $result=$db->query($query);
    $str.= '<select required '.$headers.'>';
    if ($type!==0) $str.='<option value="All">All</option>';
    while($row = $result->fetch_assoc()){
        $str.='<option ';
        if ($row['mnf_id']==$comp_id) $str.= 'selected ';
        $str.= 'value="'.$row['mnf_id'].'">'.$row['mnf_short_name'].'</option>';
    }
    $str.= '</select>';
    return $str;
};
function select_array (array $mnf_list, $comp_id='', $headers='name="manufacturer"',$type=0){
    $str.= '<select required '.$headers.'>';
    if ($type!==0) $str.='<option value="All">All</option>';
    foreach ($mnf_list as $key => $value) {
        $str.='<option ';
        if ($key==$comp_id) $str.= 'selected ';
        $str.= 'value="'.$key.'">'.$value.'</option>';
    }
    $str.= '</select>';
    return $str;
};
function select_customer_type ($type='', $headers='name="customer_type" id="customer_type"'){
    echo '<select required '.$headers.'>';
    $ar=['Owner', 'Agent', 'Service company', 'Manufacturer', 'Supplier', 'Manager', 'For checking'];
    foreach ($ar as $key){
        echo '<option ';
        if ($key===$type) {echo 'selected ';}
        echo '>'.$key.'</option>';
    }
    echo '</select>';
};
function select_customer_type2 ($type=''){
    echo '<select required name="customer_type" id="customer_type" onchange="show_customers_table()">';
    $ar=['All','Owner', 'Agent', 'Service company', 'Manufacturer', 'Supplier', 'Manager', 'For checking'];  
    foreach ($ar as $key){
        echo '<option ';
        if ($key===$type) {echo 'selected ';}
        echo '>'.$key.'</option>';
    }
    echo '</select>';
};
function select_customer_status($stat='', $flag=0){
    $str='';
    $status=['No', 'green', 'yellow', 'red'];
    $str.= '<select name="customer_status">';
    if ($flag===1)$str.='<option value="All" selected>All</option>';
    foreach ($status as $key){
        $str.= '<option ';
        if ($key===$stat) {$str.= 'selected ';}
        $str.= 'value="'.$key.'">'.$key.'</option>';
    }
    $str.= '</select>';
    echo $str;
}
function select_payment_terms($value='', $name="payment_terms"){
    $m=array('in advance', 'NET 30 days', 'NET 15 days');
    echo '<select name="'.$name.'">';
    foreach ($m as $elem){
        echo '<option';
        if ($elem===$value) echo ' selected';
        echo '>'.$elem.'</option>';
        }
    echo '</select>';
};
function select_stock_class_old($class='', $type=0,$headers='name="stock_class" id="stock_view" onchange="select_control(this)"'){
    $str='';
    $stock_classes=[1=>'Gyro', 2=>'VDR', 3=>'Magnetron',4=>'GPS', 5=>'ECDIS', 6=>'Radar',7=>'Autopilot',8=>'AIS', 9=>'Speed log',10=>'Spares',11=>'other'];
    $str.= '<select '.$headers.'>';
    if ($type===1) $str.='<option value="All" selected>All</option>';
    elseif ($type===2) $str.='<option value="" selected></option>';
    foreach ($stock_classes as $key =>$val){
        $str.= '<option ';
        if ($key==$class) {$str.= 'selected ';}
        $str.= 'value="'.$key.'">'.$val.'</option>';
    }
    $str.= '</select>';
    echo $str;
};
function select_stock_class($class='',$type=0,$headers='name="stock_class" id="stock_view" onchange="select_control(this)"'){
    $str='';
    $query='select id, stock_cat_name from stock_cats';
    if ($type===2) $query.=' where id !=1';
    if ($type===3) $query.=' where id !=1';
    $query.=' order by stock_cat_name';
    $db = db_connect();
    $result=$db->query($query);
    
    $str.= '<select '.$headers.'>';
    if ($type===1 || $type===3 ) $str.='<option value="All" selected>All</option>';
    while($row = $result->fetch_assoc()){
        $str.= '<option ';
        if ($row['id']==$class) {$str.= 'selected ';}
        $str.= 'value="'.$row['id'].'">'.$row['stock_cat_name'].'</option>';
    }
    $str.= '</select>';
    return $str;
};
function select_stock_class_purchase($class=''){
    $str='';
    $stock_classes=['Gyro', 'VDR', 'Magnetron', 'GPS', 'ECDIS', 'Radar', 'Autopilot', 'AIS', 'Speed log','Spares','other'];
    $str.= '<select>';
    foreach ($stock_classes as $key){
        $str.= '<option ';
        if ($key===$class) {$str.= 'selected ';}
        $str.= 'value="'.$key.'">'.$key.'</option>';
    }
    $str.= '</select>';
    return $str;
};
function select_our_company_old($comp=''){
    $list=['MS-Service', 'A-Z Marine', 'A-Z Marine B', 'A-Z Marine C', 'A-Z marine D'];
    $var.= '<select name="our_company">';
    foreach ($list as $key){
        $var.= '<option';
        if ($key===$comp) {$var.= ' selected';}
        $var.= ' value="'.$key.'">'.$key.'</option>';
    }
    $var.= '</select>';
    echo $var;
};
function select_our_company($id='',$headers='required name="our_company"',$type=0){
    $str='';
    $query='SELECT id, our_name FROM our_companies WHERE our_deleted=0';
    $db = db_connect();
    $result=$db->query($query);
    $str.= '<select '.$headers.'>';
    if ($type===1)$str.='<option value="0">All</option>';
    elseif ($type===0) $str.='<option></option>';
    while($row = $result->fetch_assoc()){
        $str.='<option ';
        if ($row['id']==$id) $str.= ' selected ';
        $str.= 'value="'.$row['id'].'">'.$row['our_name'].'</option>';
    }
    $str.= '</select>';
    echo $str;
};
function get_our_companies_list($arg=0){
    $data=[];
    $query='SELECT id, our_name FROM our_companies WHERE our_deleted=0';
    if ($arg==1)$query.=' AND id<90';
    $db = db_connect();
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $data[$row['id']]=($row['our_name']);
    }
    $db->close();
    return $data;
}
function select_our_company2(array $data,$id='',$headers='required name="our_company"',$type=0){
    $str='';
    $str.= '<select '.$headers.'>';
    if ($type==1){ $str.='<option>All</option>'; } 
    if ($type==2){ $str.='<option value="0">Company</option>'; } 
    else { $str.='<option></option>'; }
    foreach ($data as $key => $value) {
        $str.='<option ';
        if ($key==$id) $str.= ' selected ';
        $str.= 'value="'.$key.'">'.$value.'</option>';
    }
    $str.= '</select>';
    return $str;
};
function select_our_bank_det($our_comp,$id='',$headers='class="short_select" name="srv_our_bank_details"'){
    $str.= '<select '.$headers.'>';
    $query='SELECT * FROM our_details WHERE our_comp_id="'.$our_comp.'"';
    $db = db_connect();
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $str.='<option ';
        if ($row['id']==$id) $str.= ' selected ';
        $str.= 'value="'.$row['id'].'">'.$row['name'].'</option>';
    }
    $str.= '</select>';
    $db->close();
    return $str;
};
function select_our_bank_det_ajax($our_comp='',$id='',$currency="",$headers='class="short_select" name="srv_our_bank_details"'){
    $str.= '<select '.$headers.'>';
    if ($our_comp!==''){
    $query='SELECT * FROM our_details WHERE our_comp_id="'.$our_comp.'" AND currency_list LIKE ("%'.$currency.'%")';
    $db = db_connect();
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $str.='<option ';
        if ($row['id']==$id) $str.= ' selected ';
        $str.= 'value="'.$row['id'].'">'.$row['name'].'</option>';
    }
    $db->close();
    } else {
        $str.='<option></option>';
    }
    $str.= '</select>';
    return $str;
};
function select_user ($user='', $headers='name="user"',$role='', $type=0){
    $db= db_connect();
    $query='select uid, username, role, full_name from users WHERE user_deleted=0';
    if ($role!=='')$query.=' AND role="'.$role.'"';
    $result=$db->query($query);
    echo '<select ',$headers,'>';
    if ($type==1) echo '<option></option>';
    while($row = $result->fetch_assoc()){
        echo '<option ';
        if ($row['uid']===$user)echo 'selected ';
        echo 'value="',$row['uid'],'">',$row['full_name'],'</option>';
    }
    echo '</select>';
};
function web_site_link($link){
    $substr= substr($link, 0,5);
    if ($substr==='http:') {
        $out='<a target="_blank" href="'.$link.'">'.substr($link, 7).'</a>';
        return $out;
    }
    if ($substr==='https'){
        $out='<a target="_blank" href="'.$link.'">'.substr($link, 8).'</a>';
        return $out;
    }
    $out='<a target="_blank" href="http://'.$link.'">'.$link.'</a>';
    return $out;
}
function clean($value) {
    $var = htmlspecialchars(strip_tags(stripslashes(trim($value))),ENT_QUOTES,'UTF-8');
    return $var;
}
function file_force_download($file) {
  if (file_exists($file)) {
    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
    // если этого не сделать файл будет читаться в память полностью!
    if (ob_get_level()) {
      ob_end_clean();
    }
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    // заставляем браузер показать окно сохранения файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'. rawurldecode(basename($file)).'"');
    //header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    // читаем файл и отправляем его пользователю
    readfile($file);
    exit;
  }
}
function mail_to($link, $text) {
    echo '<a href="mailto:'.$link.'">'.$text.'</a>';
}
function select_currency ($current=0,$headers='required name="currency"', $type=0){
    $db =  db_connect();
    $query= 'SELECT * FROM currency';
    $str='<select '.$headers.'>';
    if(!$result=$db->query($query))return $str.='<option>Error</option></select>';
    if ($type===1)$str.='<option selected value="0">All</option>';
    while ($row=$result->fetch_assoc()){
        $str.='<option ';
        if ($row['curr_id']==$current) $str.='selected ';
        $str.='value="'.$row['curr_id'].'">'.$row['curr_name'].'</option>';
    }
    $str.='</select>';
    return $str;
}
function unread_messages(){
    $db =  db_connect();
    $query= 'select message_id from messages where receiver = "'.$_SESSION['valid_user'].'" and checked=0';
    $result=$db->query($query);
    if ($result->num_rows>0) return '<span class="redtext">'.$result->num_rows.'</span>';
    return $result->num_rows;
}
function document_control(){
    $warning=0;
    $time= time();
    $db =  db_connect();
    $query= 'SELECT expire_date, alarm FROM documents WHERE is_archive=0';
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $days=floor(($row['expire_date']-$time) / (60*60*24));
        if ($days<$row['alarm'])$warning=$warning+1;
    }
    if ($warning===0) return '<span class="greentext">OK</span>';
    return '<span class="redtext"><b>Warnings: '.$warning.'</b></span>';
    $db->close();
}
function check_tasks(){
//    $unread=0;
    $in_work=0;
    $expired=0;
    $str='';

    $db =  db_connect();
    
    //Check for new
    $query= 'SELECT id, expire, status '
            . 'FROM tasks '
            . 'WHERE JSON_CONTAINS(to_user, CONCAT(\'"\','.$_SESSION['uid'].',\'"\')) AND status=1';
    $result=$db->query($query);
    $new = $result->num_rows;
    
    //Check for unread
//    $query= 'SELECT id FROM task_history '
//            . 'WHERE is_read=0 '
//            . 'AND user_id!='.$_SESSION['uid'].' ' // Свои не могут быть непрочитаны 
//            . 'AND task_history.task_id IN (SELECT task_id FROM tasks WHERE from_user='.$_SESSION['uid'].' OR to_user='.$_SESSION['uid'].')';
//    $result=$db->query($query);
//    $unread = $result->num_rows;
    
        //Check for in_work
    $query= 'SELECT id, expire, status '
            . 'FROM tasks '
            . 'WHERE JSON_CONTAINS(to_user, CONCAT(\'"\','.$_SESSION['uid'].',\'"\')) AND status=2';
    $result = $db->query($query);
    $in_work = $result->num_rows;
    
    //Check for expired
    $query= 'SELECT id, expire, status '
            . 'FROM tasks '
            . 'WHERE (JSON_CONTAINS(to_user, CONCAT(\'"\','.$_SESSION['uid'].',\'"\'))'
            . ' OR from_user="'.$_SESSION['uid'].'")  '
            . 'AND expire <= CURDATE() AND status IN (1,2,3)';
    $result=$db->query($query);
    $expired = $result->num_rows;

    $str.='<span class="greentext"><b> ('.$new.') </b></span>'; // new
    $str.='<span class="yellowtext"><b> ('.$in_work.') </b></span>'; //in work
    $str.='<span class="redtext"><b> ('.$expired.') </b></span>'; //expired
    $db->close();
    return $str;
}
function clean_file_name(&$string){
    $symbols=['.','-',',','\'','!','$','(',')'];
    if(in_array(substr($string,0,1), $symbols))$string='~'.$string;    
}
function get_customer_name($id){
    $query='select cust_short_name from customers where cust_id='.$id;
    $db = db_connect();
    $result=$db->query($query);
    if ($result->num_rows===1) {
        $row = $result->fetch_assoc();
        return $row['cust_short_name'];
    }
    else return 'No customer';
}
function marine_traffic_link($imo,$text='MarineTraffic'){
    if ($imo=='')return;
    else return '<a target="_blank" href="https://www.marinetraffic.com/en/ais/index/search/all?keyword='.$imo.'">'.$text.'</a>';
}
function sort_class($current, $sort, $type=''){
    if ($current===$sort) {
        if ($type==='DESC') echo 'class="sort_down"';
        echo 'class="sort_up"';
    }
}
function numberFormat($digit, $width) {
    while(strlen($digit) < $width)
      $digit = '0' . $digit;
      return $digit;
}
//Currency finctions
function get_currency_list(){
    $query='SELECT curr_id, curr_name FROM currency';
    $db = db_connect();
    $result=$db->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[$row['curr_id']]=($row['curr_name']);        
    }
    $db->close();
    return $data;
}
function select_currency2(array $data, $current=0, $headers='name="currency"'){
    $out='<select '.$headers.'>';
    foreach ($data as $key => $value) {
        $out.='<option value="'.$key.'"';
        if ($key==$current)$out.=' selected';
        $out.='>'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}
//Nomenclature selector
function get_stock_category_list($arg=0){
    $query='SELECT id, stock_cat_name FROM stock_cats ORDER BY stock_cat_name';
    if ($arg == 1)$query='SELECT id, stock_cat_name FROM stock_cats WHERE for_vessel=1 ORDER BY stock_cat_name';
    $db = db_connect();
    $result=$db->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[$row['id']]=($row['stock_cat_name']);        
    }
    $db->close();
    return $data;
}
function get_manufacturers_list($arg=0){
    $query='SELECT mnf_id, mnf_short_name FROM manufacturers ORDER BY mnf_short_name';
    if ($arg == 1)$query='SELECT mnf_id, mnf_short_name FROM manufacturers WHERE mnf_for_service=1 ORDER BY mnf_short_name';
    $db = db_connect();
    $result=$db->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[$row['mnf_id']]=($row['mnf_short_name']);        
    }
    $db->close();
    return $data;
}
function stock_nmnc_selector(array $stock_cats,$headers='name="stock_nmnc_id"',$current_cat=0,$current_id=0){
    //Селектор выбора категории
    $str='<span>';
    $str.= '<select required onchange="stock_nmnc_selector(this)">';
    $str.='<option value="">Select category</option>';
    foreach ($stock_cats as $key => $value) {
        $str.='<option value="'.$key.'"';
        if ($key==$current_cat)$str.=' selected';
        $str.='>'.$value.'</option>';
    }
    $str.= '</select>';
    $str.= '<select '.$headers.'>';
    if ($current_id!==0){
        $query='SELECT stnmc_id, stnmc_descr, stnmc_pn FROM stock_nmnc WHERE ';
        if ($current_cat!=0) $query.='stnmc_type="'.$current_cat.'"';
        else $query.='stnmc_id="'.$current_id.'"';               
        $db = db_connect();
        $result=$db->query($query);
        while ($row = $result->fetch_assoc()) {
            $str.='<option value="'.$row['stnmc_id'].'"';
            if ($row['stnmc_id']==$current_id) $str.=' selected';
            $str.='>'.$row['stnmc_pn'].' '.$row['stnmc_descr'].'</option>';        
        } 
    }
    $str.='</select></span>';
    return $str;
}
function view_stock_nmnc ($id=0){
    $query='SELECT stnmc_descr, stnmc_pn FROM stock_nmnc WHERE stnmc_id="'.$id.'"';
    $db = db_connect();
    $result=$db->query($query);
    if ($result->num_rows!==1) $var='Not found';
    else {
        $row=$result->fetch_assoc();
        $var=$row['stnmc_descr'].' '.$row['stnmc_pn'];
    }
    $db->close();
    return $var;
}
function select_client_of($curent='',$headers='name="client_of"'){
    $list=Array('RU','EE','HR');
    $str='<select '.$headers.'><option></option>';
    foreach ($list as $value) {
        $str.='<option value="'.$value.'"';
        if ($value==$curent)$str.=' selected';
        $str.='>'.$value.'</option>';
    }
    $str.='</select>';
    return $str;
}
function cross_docs_get_type($type){
    if ($type=='1')return 'Service';
    elseif ($type=='2')return 'Sales';
    elseif ($type=='3')return 'Purchase';
    elseif ($type=='4')return 'Invoice';
    elseif ($type=='5')return 'Credit note';
}
function order_display_format($type,$comp,$number){
    //1 - service 2 - sales 3 - PO 4 - invoice	
    if ($type==='1') return numberformat($number, 5);
    elseif ($type==='2') return numberformat($comp, 2).'.'.$number;
    elseif ($type==='3') return numberformat($comp, 2).'.'.numberformat($number, 4);
    else return $number;
}
function get_list_of_stocks(){
    $query='SELECT stockl_id, stockl_name FROM stock_list';
    $db = db_connect();
    $result=$db->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[$row['stockl_id']]=($row['stockl_name']);        
    }
    $db->close();
    return $data;
}
function days_check($days){
    if ($days<0) return '<span class="redtext">'.$days.'</span>';
    elseif ($days<30) return '<span class="orangetext">'.$days.'</span>';
    return $days;
}
function select_vat_rem($current='', $headers='name="sales_vat_remarks"'){
    $out='<select '.$headers.'>';
    $out.='<option></option>';
    //Option 1
    $option1='VAT remarks: KMS pr.15, lg.3, p.3 Directive 2006/ 112/EC, art 148, art 37(3)';
    $out.='<option';
    if ($current===$option1) {$out.=' selected';}
    $out.='>'.$option1.'</option>';
    //Option 2
    $option2='Cust. VAT exemption No. : EU services, VAT 0% Intra-Community supply';
    $out.='<option';
    if ($current===$option2){$out.=' selected';}
    $out.='>'.$option2.'</option>';
    $out.='</select>';
    return $out;
}

function entity_type($current='', $headers=''){
    $list = [
        0 => 'Order type',
        1 => 'Service',
        2 => 'Sales',
        3 => 'Purchase',
        4 => 'Invoice',
        6 => 'Administrtive',
        7 => 'Claim'
        //5 => 'Credit note'
        //6 => 'Contract'
        //7 => 'Tender'
    ];
    $out='<select '.$headers.'>';
    foreach ($list as $key => $value) {
        $out.='<option value="'.$key.'"';
        if ($current==$key){$out.=' selected';}
        $out.='>'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}
function get_order_types($type=1){
    $query='SELECT id, type_text, prefix FROM order_types WHERE active=1';
    $db = db_connect();
    $result=$db->query($query);
    if ($type===2){
         $data[0]='';
    }
    while ($row = $result->fetch_assoc()) {
        $data[$row['id']]=[$row['type_text'],$row['prefix']];        
    }
    $db->close();
    return $data;
}
function get_order_name($type, $comp, $num){
    switch ($type) {
        case 1:
            return numberFormat($num,5);
        case 2:
            return numberFormat($comp,2).'.'.$num;
        case 3:
            return numberFormat($comp,2).'.'.$num;
        default:
            break;
    }
}
function get_user_list(){
    $query='SELECT uid, full_name FROM users WHERE user_deleted = 0 ORDER BY full_name';
    $db = db_connect();
    $result=$db->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[$row['uid']]=($row['full_name']);        
    }
    $db->close();
    return $data;
}

//Универсальный селектор
function select_from_list($list_of_values=[], $current_value=0, $headers='', $type=0){
    if (!is_array($list_of_values)){
        return 'Error loading data.';
    }
    $out = '<select '.$headers.'>';
    if ($type == 1){
        $out.='<option></option>';
    }
    foreach ($list_of_values as $key => $value) {
        $out.='<option '.$headers;
        if ($current_value == $key){$out.=' selected';}
        $out.=' value="'.$key.'">'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}   