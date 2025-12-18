<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';
startSession();
if(check_access('acl_stock', 1)) exit('Access denied.');

$page_title = 'Transfers';
include 'header.php';

$comp_list = get_our_companies_list(1);
?>

<div id="side_menu">
    <b>From Stock</b> <?php echo select_stock($row['stock_stock_id'],'id="from_stock" onchange="show_transfer_table(1)"',1);?>
    <b>To Stock</b> <?php echo select_stock($row['stock_stock_id'],'id="to_stock" onchange="show_transfer_table(1)"',1);?>
    
    <b>Ship date</b> <input size="10" type="text" class="datepicker" id="ship_date_start" onchange="show_transfer_table(1)" value=""> 
    to <input size="10" type="text" class="datepicker" id="ship_date_end" onchange="show_transfer_table(1)">
    <b>Receipt date</b><input size="10" type="text" class="datepicker" id="receipt_date_start" onchange="show_transfer_table(1)" value=""> 
    to <input size="10" type="text" class="datepicker" id="receipt_date_end" onchange="show_transfer_table(1)">
    <label>Search <input type="search" id="stock_transfers_search" onchange="show_transfer_table(1);" placeholder="PN, model, description"></label>
    <label>Officialy sold
        <select id="stock_transfers_sold" onchange="show_transfer_table(1);">
            <option value="2">All</option>
            <option value="1">Sold</option>
            <option value="0">Not sold</option>
        </select>
    </label>
</div>
<main id="main_div_menu"></main>  

<?php include 'footer.php';?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_stock_new.js"></script>
<script type="text/javascript" src="java/java_stock_nmnc.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script type="text/javascript" src="java/java_purchase.js"></script>
<script type="text/javascript" src="java/java_stock_complect.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", show_transfer_table(1));
</script>