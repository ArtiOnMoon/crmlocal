<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';
startSession();
if(check_access('acl_stock', 1)) exit('Access denied.');
do_page_header('Transfers','Stock');
$comp_list = get_our_companies_list(1);
?>

<div id="side_menu">
    <a class="knopka" href="#" onclick="transfers_new()">New transfer order</a>
    <a class="knopka" href="#" onclick="stock_selector_main_show()">Stock selector</a>
    <b>From Stock</b> <?php echo select_stock($row['stock_stock_id'],'id="from_stock" onchange="show_transfer_table(1)"',1);?>
    <b>To Stock</b> <?php echo select_stock($row['stock_stock_id'],'id="to_stock" onchange="show_transfer_table(1)"',1);?>
    
    <b>Ship date</b> <input size="10" type="text" class="datepicker" id="ship_date_start" onchange="show_transfer_table(1)" value=""> 
    to <input size="10" type="text" class="datepicker" id="ship_date_end" onchange="show_transfer_table(1)">
    <b>Receipt date</b><input size="10" type="text" class="datepicker" id="receipt_date_start" onchange="show_transfer_table(1)" value=""> 
    to <input size="10" type="text" class="datepicker" id="receipt_date_end" onchange="show_transfer_table(1)">
</div>
<div id="main_div_menu"></div>  


<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_stock_new.js"></script>
<script type="text/javascript" src="java/java_stock_nmnc.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script type="text/javascript" src="java/java_stock_complect.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_transfers.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", show_transfers_table(1));
</script>