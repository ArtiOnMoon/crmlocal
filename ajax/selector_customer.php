<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$data=clean($_POST['data']);
$cond=clean($_POST['condition']);
$query= 'SELECT cust_id, cust_short_name, cust_full_name,is_fchk,customer_status,client_of,discount '
        . 'FROM customers '
        . 'WHERE deleted=0 AND (cust_id LIKE "%'.$data.'%" OR cust_short_name LIKE "%'.$data.'%" OR cust_full_name LIKE "%'.$data.'%")';
if ($cond!=''){
    if ($cond==='mngr')$query.=' AND is_mngr=1';
    elseif ($cond==='serv')$query.=' AND is_serv=1';
    elseif ($cond==='mnfr')$query.=' AND is_mnfr=1';
    elseif ($cond==='agnt')$query.=' AND is_agnt=1';
    elseif ($cond==='sppl')$query.=' AND is_sppl=1';
    elseif ($cond==='ownr')$query.=' AND is_ownr=1';
    elseif ($cond==='optr')$query.=' AND is_optr=1';
    elseif ($cond==='fchk')$query.=' AND is_fchk=1';
    
}
$query.=' ORDER BY cust_full_name';
$db =  db_connect();
echo '<div class="selector_result_div row_white" data-id="new_customer"><a href="#">Add new</a></div>';

if(!$result=$db->query($query))exit($db->error);
if($result->num_rows>0){
    while ($row=$result->fetch_assoc()){
        $class='row_white';
        if ($row['is_fchk'])$class='row_grey';
        elseif ($row['customer_status']==='red') $class="row_red";
        elseif ($row['customer_status']==='yellow') $class='row_yellow';
        elseif ($row['customer_status']==='green') $class='row_green';
        echo '<div class="selector_result_div ',$class,'" data-id="'.$row['cust_id'].'" data-value="'.$row['cust_full_name'].'", data-discount="'.$row['discount'].'">',
            '<strong>',$row['cust_short_name'],'</strong><br>',
            $row['cust_full_name'],
        '<div class="selector_client_of">',$row['client_of'],'</div></div>';
    } 
}   
else echo'No results';