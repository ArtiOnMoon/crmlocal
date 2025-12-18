<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/service.php';
require_once 'functions/auth.php';
require_once 'classes/Order_name_engine.php';

startSession();
security();

//if(check_access('acl_service', 1)) exit('Access denied.');
if(check_access('acl_service', 1, $_POST['our_company'])) exit('Access denied.');

$limit=50;

if ($_POST['page']==='undefined') $page=$_SESSION['service_page'];
else $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;
$_SESSION['service_page']=$page;

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);
$on->type = 'SR';

$query='SELECT service_id, vessel_name FROM service LEFT JOIN vessels ON service.vessel_id=vessels.vessel_id WHERE service_deleted=0';
//CONDITIONS
$cond='';
$date_start= $_POST['date_start'];
$date_end= $_POST['date_end'];
if ($date_start!='') $cond.=' AND service_date>="'.$date_start.'"';
if ($date_end!='') $cond.=' AND service_date<="'.$date_end.'"';

if (isset($_POST['vessel'])) $cond.=' AND service.vessel_id="'.clean($_POST['vessel']).'"';
if (isset($_POST['customer'])) $cond.=' AND service.comp_id="'.clean($_POST['customer']).'"';
if (isset($_POST['agent'])) $cond.=' AND service.agent="'.clean($_POST['agent']).'"';
if (isset($_POST['srv_agent'])) $cond.=' AND service.service_executor_id="'.clean($_POST['srv_agent']).'"';
if (isset($_POST['our_company'])) $cond.=' AND service.service_our_comp="'.clean($_POST['our_company']).'"';
if (isset($_POST['our_company'])) $cond.=' AND service.service_our_comp="'.clean($_POST['our_company']).'"';
$status='';
if (isset($_POST['status_1']))$status.= 'status="1" or ';
if (isset($_POST['status_2']))$status.= 'status="2" or ';
if (isset($_POST['status_3']))$status.= 'status="3" or ';
if (isset($_POST['status_4']))$status.= 'status="4" or ';
if (isset($_POST['status_5']))$status.= 'status="5" or ';
if (isset($_POST['status_6']))$status.= 'status="6" or ';
if (isset($_POST['status_7']))$status.= 'status="7" or ';
if (isset($_POST['status_8']))$status.= 'status="8" or ';
if (isset($_POST['status_9']))$status.= 'status="9" or ';
if ($status!==''){
    $status=substr($status, 0, strlen($status)-4);
    $cond.=' and ('.$status.')';
}
if (isset($_POST['service_search_by_equipment']))
{
    $cond.=' AND service_id IN '
            . '(SELECT sfd_serv_id FROM service_fault_descr LEFT JOIN service_equipment on sfd_equip_id=srv_eq_id '
            . 'WHERE srv_eq_name LIKE ("%'. clean($_POST['service_search_by_equipment']).'%") OR sfd_descr LIKE ("%'. clean($_POST['service_search_by_equipment']).'%"))';
}

//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort = ' ORDER BY service_no DESC ';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
//Start users
if (isset($_POST['users_list']))
    $users=(array)json_decode($_POST['users_list']);
else $users=[];
if (count($users)>0){
    $users_list=implode(',',$users);
    $users_list=' AND service_id IN (SELECT su_service_id FROM service_users WHERE su_uid IN ('.$users_list.'))';
}
else $users_list='';
//End users

//SEARCH
if (!isset($_POST['search']))$search='';
else{
    $search_field=clean($_POST['search']);
    
    try{
        if ($on ->resolve_order($search_field)){
            $search=' AND (service.PO LIKE ("%'.$search_field.'%") OR service_no = ("'.$on->num.'") '
            . 'OR description LIKE ("%'.$search_field.'%") OR invoice LIKE ("%'.$search_field.'%") '
            . 'OR vessel_name LIKE ("%'.$search_field.'%") OR request LIKE ("%'.$search_field.'%") '
            . 'OR location LIKE ("%'.$search_field.'%") OR service_note LIKE ("%'.$search_field.'%"))';
        }
    } catch (Exception $ex) {
        $search=' AND (service.PO LIKE ("%'.$search_field.'%") OR service_no LIKE ("%'.$search_field.'%") '
            . 'OR description LIKE ("%'.$search_field.'%") OR invoice LIKE ("%'.$search_field.'%") '
            . 'OR vessel_name LIKE ("%'.$search_field.'%") OR request LIKE ("%'.$search_field.'%") '
            . 'OR location LIKE ("%'.$search_field.'%") OR service_note LIKE ("%'.$search_field.'%"))';
    } 
}

//END SEARCH
$result=$db->query($query.$cond.$search);

if (!$result) exit('No service yet');
$num = $result->num_rows;
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;
$query= 'SELECT '
    . 'service.*, serv_stat_name,'
    . 't1.cust_short_name as cust_short_name,'
    . 't2.cust_short_name as executor_name,'
    . 't3.cust_short_name as agent_name,'
    . 'vessels.vessel_id,'
    . 'our_companies.our_name,'
    . 'vessels.vessel_name '
    . 'FROM service '
        . 'LEFT JOIN customers t1 ON comp_id=t1.cust_id '
        . 'LEFT JOIN customers t2 ON service_executor_id=t2.cust_id '
        . 'LEFT JOIN customers t3 ON agent=t3.cust_id '
        . 'LEFT JOIN our_companies ON service_our_comp=our_companies.id '
        . 'LEFT JOIN service_statuses ON status=serv_stat_id '
        . 'LEFT JOIN vessels ON service.vessel_id=vessels.vessel_id '
    . 'WHERE service_deleted=0';
$query.=$cond.' '.$users_list.$search.' '.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);
if (!$result) exit('No service yet');

//УПРАВЛЕНИЕ СТРАНИЦАМИ
?>
<div id="main_subheader">
    <?php
    echo '<div id="main_table_controls">'
    . 'Page <b>'.$page.'</b> of '.$pages.'<br>';
    //Previous page button
    echo '<input';
    if ($page<=1)echo ' disabled ';
    echo' type="button" onclick="show_service_table('.$previous_page.')" value="Previous page">';
    //Next page button
    echo '<input ';
    if ($page>=$pages)echo ' disabled ';
    echo 'type="button" onclick="show_service_table('.($next_page).')" value="Next page"></div>';
    ?>
</div>

<div id="main_subbody">
    <div id="table_wrap">
    <table id="service_table" class="sort_table">
    <thead onclick="table_sort(event,'service')">
    <th keyword='service_no' <?php sort_class('service_no',$sort_field,$sort_type);?>>ID</th>
    <th keyword='status' <?php sort_class('status',$sort_field,$sort_type);?> width="5%">Status</th>
    <th keyword='service_date' <?php sort_class('service_date',$sort_field,$sort_type);?> width="5%">Date</th>
    <th keyword='vessel_name' <?php sort_class('vessel_name',$sort_field,$sort_type);?> width="10%">Vessel name</th>
    <th keyword='location' <?php sort_class('location',$sort_field,$sort_type);?> width="10%">Port</th>
    <th keyword='ETA' <?php sort_class('ETA',$sort_field,$sort_type);?> width="5%">ETA</th>
    <th keyword='ETD' <?php sort_class('ETD',$sort_field,$sort_type);?> width="5%">ETD</th>
    <th keyword='cust_short_name' <?php sort_class('cust_short_name',$sort_field,$sort_type);?>>Customer</th>
    <th keyword='agent_name' <?php sort_class('agent_name',$sort_field,$sort_type);?>>Agent</th>
    <th keyword='PO' <?php sort_class('PO',$sort_field,$sort_type);?> width="7%">Customer's PO</th>
    <th width="5%">Eng. code</th>
    <th keyword="executor_name" width="5%" <?php sort_class('executor_name',$sort_field,$sort_type);?>>S. agent</th>
    <th keyword='invoice' <?php sort_class('invoice',$sort_field,$sort_type);?> width="5%">Invoice</th>
    <th width="10%">Note</th>
    </thead><tbody>
    <?php
    while($row = $result->fetch_assoc()){
        $on->comp_id = $row['service_our_comp'];
        $on->num = $row['service_no'];
        $on->id = $row['service_id'];
        $users='';
        $query= 'SELECT user_code FROM service_users LEFT JOIN users ON su_uid=uid WHERE su_service_id = "'.$row['service_id'].'"';
        $u_list=$db->query($query);
        while($elem = $u_list->fetch_assoc()){
            $users.=$elem['user_code'].', ';
            $users= substr($users, 0,-2);
        }
        $s_agent=view_company_link($row['executor_name'],(int)$row['service_executor_id']);

        echo '<tr><td class="num">';
        echo '<a href="#" onclick="view_link(\''.$on->get_order().'\')">'.$on->order.'</a></td>'
//        echo view_order_link($row['service_our_comp'],$row['service_no'],$on->get_order()).'</td>'
            ,'<td class="',service_color_table($row['status'],$row['ETA']),'">',$row['serv_stat_name'],'</td>'
            ,'<td>',$row['service_date'],'</td>'
            ,'<td>',view_vessel_link($row['vessel_name'],$row['vessel_id']),'</td>'
            ,'<td>',$row['location'],'</td>'
            ,'<td>',$row['ETA'].'</td>'
            ,'<td>',$row['ETD'].'</td>'
            ,'<td>',view_company_link($row['cust_short_name'],(int)$row['comp_id']),'</td>'
            ,'<td>',view_company_link($row['agent_name'],(int)$row['agent']),'</td>'
            ,long_td_string($row['PO'], 15)
            ,'<td>',$users,'</td>'
            ,'<td>',$s_agent,'</td>'
            ,'<td>'.return_pay_type($row['srv_pay_type'],$row['invoice'],$row['srv_inv_number']).'</td>',
            long_td_string($row['service_note'], 20)
            ,'</tr>';
    }
    ?>
    </tbody>
    </table>
    </div>
</div>
<div id="main_subfooter"></div>