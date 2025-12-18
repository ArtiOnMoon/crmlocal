<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/service.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';

$page_title = 'Service';
include 'header.php';

if(check_access('acl_service', 1)) exit('Access denied.');
$currency_data=get_currency_list();

//SERVICE EXPIRED SCRIPT
$db= db_connect();
$query='UPDATE service SET status=8 WHERE status=2 AND DATEDIFF(CURDATE(),service_date)>60';
if(!$db->query($query)) echo 'Warning! Sales auto expire error.';

?>
<div id="side_menu">
    <a class="knopka" href="#"  onclick="service_new()">New service</a>
    <label><?php select_our_company($_SESSION['default_company'], 'id="service_our_company" onchange="show_service_table()"',2)?></label>
    <div class="hidden_conteiner"  style="display:inline-block;position:relative;"><label onclick="display_menu('hidden_div3','list_sign3')">Status <span id="list_sign3" class="sign">&#9660</span></label>
        <div class="hidden_div" id="hidden_div3" style="margin-left:-5px;margin-top:5px; vertical-align: top;">
            <label><input class="service_filter" type='checkbox' id='status_1' onchange="show_service_table()">Request</label><br>
            <label><input class="service_filter" type='checkbox' id='status_2' onchange="show_service_table()">Quotation</label><br>
            <label><input class="service_filter" type='checkbox' id='status_3' onchange="show_service_table()">Confirmed</label><br>
            <label><input class="service_filter" type='checkbox' id='status_7' onchange="show_service_table()">Follow-Up</label><br>
            <label><input class="service_filter" type='checkbox' id='status_6' onchange="show_service_table()">Completed</label><br>
            <label><input class="service_filter" type='checkbox' id='status_5' onchange="show_service_table()">Canceled</label><br>
            <label><input class="service_filter" type='checkbox' id='status_8' onchange="show_service_table()">Expired</label><br>
            <label><input class="service_filter" type='checkbox' id='status_9' onchange="show_service_table()">Post-Processing</label><br>
            <button onclick="check_all()">Check\uncheck all</button>
        </div>
    </div>
    <div class="hidden_conteiner" style="position:relative; display: inline-block;"><label onclick="display_menu('hidden_div2', 'list_sign2')">Engineers<span class="sign" id="list_sign2">&#9660</span></label>
        <div class="hidden_div" id="hidden_div2" style="margin-left:-5px;margin-top:5px;position:fixed; vertical-align: top;">
        <?php
        $db= db_connect();
        $query='select uid,role,full_name from users where is_technician="1" and user_deleted=0';
        $result=$db->query($query);
        while ($row = $result->fetch_assoc()) {?>
            <label><input type="checkbox" class='user_multiselect' onclick="show_service_table()" value="<?php echo$row['uid'];?>">
            <?php echo$row['full_name'];?>
            </label><br>
        <?php }?>
        </div>
    </div>
    <span>Fast search: <input type="search" id="service_search" placeholder = "ID, vessel, customer" oninput="show_service_table()"></span>
    Date from <input size="10" type="text" class="datepicker" id="date_start" onchange="show_service_table(1)"> 
    to <input size="10" type="text" class="datepicker" id="date_end" onchange="show_service_table(1)">
<!-- FILTERS BLOCK -->
    <div class="hidden_conteiner" onclick="display_menu('hidden_div','list_sign')">Show filters <span class="sign" id="list_sign">&#9660</span></div>
    <div class="hidden_div" id="hidden_div">
        <span style="padding-right:2em">Customer <?php echo select_customer2('',' id="customer" class="combobox" onchange="show_service_table(1)"'); ?></span>
        <span style="padding-right:2em">Agent <?php echo select_customer('',1,' id="agent" class="combobox" onchange="show_service_table(1)"','agnt'); ?></span>
        <span style="padding-right:2em">Vessel <?php echo select_vessel('','id="vessel" class="combobox" onchange="show_service_table(\'1\')"',1); ?></span>
        <span style="padding-right:2em">Service agent <?php echo select_customer('',1,' id="srv_agent" class="combobox" onchange="show_service_table(1)"','serv'); ?></span>
        <a class="knopka" href="#" onclick="show_service_table()">Apply filters</a> 
    </div>
    <input type="search" id="service_search_by_equipment" placeholder="Search by equipment" onchange="show_service_table(1)">
    <a class="knopka" href="#" onclick="srv_reset_filters()">Reset filters</a>&nbsp&nbsp
    <!-- <div style="float:right; padding:3px;"><label><input id="auto_refresh" type="checkbox" checked="true">Auto refresh</label></div> -->
</div><!-- END FILTERS BLOCK -->

<!-- <link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
<script type="text/javascript" src="java/service_timeline.js"></script> -->


<main id="main_div_menu"></main>

<?php include 'footer.php';?>

<div id="invis_wrap" onclick="close_menu()"></div>  

<link rel="stylesheet" type="text/css" href="css/sales.css">
<link rel="stylesheet" type="text/css" href="/css/purchase.css">
<link rel="stylesheet" type="text/css" href="/css/invoices.css">

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
    document.addEventListener("DOMContentLoaded", show_service_table('<?php echo $_SESSION['service_page'];?>','All'));
    function check_all(){
        var inputs=document.getElementsByClassName('service_filter');
        for (var i=0;i<inputs.length;i++){
              if (inputs[i].checked==false){
                  $(".service_filter").prop("checked",true);
                  show_service_table();
                  return;
              }
        }
        $(".service_filter").prop("checked",false);
    }
</script>

<!-- 🔹 ТАЙМЛАЙН: подключение модуля -->
<link rel="stylesheet" href="css/service_timeline.css?v=<?= filemtime('css/service_timeline.css') ?>">
<script src="java/service_timeline.js?v=<?= filemtime('java/service_timeline.js') ?>"></script>
<script src="java/service_timeline_init.js?v=<?= filemtime('java/service_timeline_init.js') ?>"></script>
<!-- 🔹 /ТАЙМЛАЙН -->

