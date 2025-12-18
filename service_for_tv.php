<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/service.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';

$limit=50;

if ($_POST['page']==='undefined') $page=$_SESSION['service_page'];
else $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;
$_SESSION['service_page']=$page;
$db =  db_connect();
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
$status='';
if (isset($_POST['status_1']))$status.= 'status="1" or ';
if (isset($_POST['status_2']))$status.= 'status="2" or ';
$status.= 'status="3" or ';
if (isset($_POST['status_4']))$status.= 'status="4" or ';
if (isset($_POST['status_5']))$status.= 'status="5" or ';
if (isset($_POST['status_6']))$status.= 'status="6" or ';
$status.= 'status="7" or ';
if (isset($_POST['status_8']))$status.= 'status="8" or ';
if ($status!==''){
    $status=substr($status, 0, strlen($status)-4);
    $cond.=' and ('.$status.')';
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
$users=(array)json_decode($_POST['users_list']);
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
    $search=' AND (service.PO LIKE ("%'.$search_field.'%") OR service_no LIKE ("%'.$search_field.'%") '
            . 'OR description LIKE ("%'.$search_field.'%") OR invoice LIKE ("%'.$search_field.'%") '
            . 'OR vessel_name LIKE ("%'.$search_field.'%") OR request LIKE ("%'.$search_field.'%") '
            . 'OR location LIKE ("%'.$search_field.'%") OR service_note LIKE ("%'.$search_field.'%"))';
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
$query= 'select '
    . 'service.*, serv_stat_name,'
    . 't1.cust_short_name as cust_short_name,'
    . 't2.cust_short_name as executor_name,'
    . 't3.cust_short_name as agent_name,'
    . 'vessels.vessel_id,'
    . 'our_companies.our_name,'
    . 'vessels.vessel_name '
    . 'from service '
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

?>
<html>
<head>
    <meta charset="utf-8">
        <link rel="icon" href="img/favicon.svg" type="image/x-icon">
        <script src="java/jquery-3.1.1.min.js"></script>
        <script src="java/jquery-ui-1.12.1/jquery-ui.min.js"></script>
        <script src="java/selector.js"></script>
        <style>
            html, body{
                padding:0;
                margin:0;
                font-family: Arial;
                font-size: 18px !important;
                box-sizing: border-box;
                vertical-align: middle;
                line-height: 1.15;
                -ms-text-size-adjust: none;
                -webkit-text-size-adjust: none;
                zoom: 115%;
                background: black;
                color:white;
            }
            table {
                border-collapse: collapse;
                border: none;
                overflow-y: auto;
            }
            th {
                 background-color: #404040;
                 color:white;
                 text-align: center;
                 vertical-align: top;
             }
             td{
                 padding:2px 5px;
                 font-size: 18px;
             }
            a{
                background-color: transparent;
                -webkit-text-decoration-skip: objects;
                text-decoration: none;
                color: #0000FF;  
                font-weight: bold;
            }
            .sort_table{
                position: relative;
                border-collapse: separate;
                border-spacing: 0px;
                width:100%;
              }
            .sort_table td{
                padding: .2em .2em;
                border:1px solid #333;
                border-collapse: separate;
                border-spacing: 0px;
            }
            .sort_table tbody tr:hover {background-color:#eef;}
            .sort_table th {
                border:2px solid #dddddd;
                cursor:pointer;
                top:0;
                position: sticky;
            }
            .blink {
                webkit-animation: blink 2s linear infinite; 
                animation: blink 2s linear infinite;
            }
            @-webkit-keyframes blink { 
                50% { background: #f98b85; }
            }
            @keyframes blink {  
                50% { background: #f98b85; }
            }
            .row_red{background:#f79e99; color:black;}
            .row_white{background:white;color:black;}
            .row_yellow{background:#ecf576;color:black;}
            .row_green{background:#97fc9b;color:black;}
            .row_confirmed{background:#33d6d3;color:black;}
            .row_grey{background: #b5b5b5;color:black;}
            .row_complete{background: #9ad7ed;color:black;}
            .row_brown{background: #f2c396;color:black;}
            .row_light_green{background: #03fcad;color:black;}
            .row_blue{background: #10ade6;color:black;}
            .row_violet{background: #e084e3;color:black;}
            .row_pink{background: #f76aa5;color:black;}
        </style>
</head>
<body>
<link rel="stylesheet" type="text/css" href="/css/sales.css">
<link rel="stylesheet" type="text/css" href="/css/purchase.css">
<link rel="stylesheet" type="text/css" href="/css/invoices.css">
<table id="service_table" class="sort_table">
<thead onclick="table_sort(event,'service')">
<th width="60px" keyword='service_no' <?php sort_class('service_no',$sort_field,$sort_type);?>>ID</th>
<th keyword='status' <?php sort_class('status',$sort_field,$sort_type);?> width="5%">Status</th>
<th width="120px" keyword='service_date' <?php sort_class('service_date',$sort_field,$sort_type);?>>Date</th>
<th width="200px" keyword='vessel_name' <?php sort_class('vessel_name',$sort_field,$sort_type);?>>Vessel name</th>
<th width="50px" keyword='location' <?php sort_class('location',$sort_field,$sort_type);?>>Port</th>
<th width="120px" keyword='ETA' <?php sort_class('ETA',$sort_field,$sort_type);?> width="5%">ETA</th>
<th width="120px" keyword='ETD' <?php sort_class('ETD',$sort_field,$sort_type);?> width="5%">ETD</th>
<th>Note</th>
</thead><tbody>
<?php
while($row = $result->fetch_assoc()){
    $users='';
    $query= 'SELECT user_code FROM service_users LEFT JOIN users ON su_uid=uid WHERE su_service_id = "'.$row['service_id'].'"';
    $u_list=$db->query($query);
    while($elem = $u_list->fetch_assoc()){
        $users.=$elem['user_code'].', ';
        $users= substr($users, 0,-2);
    }
    $s_agent=view_company_link($row['executor_name'],(int)$row['service_executor_id']);
    
    echo '<tr><td class="num"><strong>';
    echo $row['service_no'].'</strong></td>'
        //,'<td>',$row['our_name'],'</td>'
        ,'<td class="',service_color_table($row['status'],$row['ETA']),'">',$row['serv_stat_name'],'</td>'
        ,'<td>',$row['service_date'],'</td>'
        ,'<td><strong>',$row['vessel_name'],'</strong></td>'
        ,'<td>',$row['location'],'</td>'
        ,'<td>',$row['ETA'].'</td>'
        ,'<td>',$row['ETD'].'</td>'
        ,long_td_string($row['service_note'], 100)
        ,'</tr>';
}
?>
</tbody>
</table>
</body>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_equipment_func.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_purchase.js"></script>
<script type="text/javascript" src="java/java_vessels.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script type="text/javascript" src="java/java_stock_nmnc.js"></script>
<script type="text/javascript" src="java/java_stock_new.js"></script>
<script type="text/javascript" src="java/invoice_func.js"></script>
<script>
window.setTimeout( function() {
  window.location.reload();
}, 30000);
</script>
