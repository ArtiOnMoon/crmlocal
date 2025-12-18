<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/purchase_fns.php';
require_once 'functions/selector.php';
startSession();
security ();

$page_title = 'Purchase';
include 'header.php';

if(check_access('acl_purchase', 1)) exit('Access denied.');
?>
<link href="/css/purchase.css" rel="stylesheet">
<div id="side_menu">
    <a class="knopka" onclick="po_new()">New purchase</a>
    <label>Status<?php echo select_po_status('','onchange="show_purchase_table(1)" id="purchase_status"',1); ?></label>
    <label>Company <?php select_our_company(0, 'id="po_our_comp" onchange="show_purchase_table(1)"',1);?></label>
    <label>Customer <div style="display:inline-block;width:200px"><?php echo selector('customers','id="po_supplier" onchange="show_purchase_table(1)"');?></div></label>
    Date <input type="search" id="po_date_from" class="datepicker" size="9" onchange="show_purchase_table(1)"> - <input type="search" id="po_date_to" class="datepicker" size="9" onchange="show_purchase_table(1)">
    <label><input type="search" id="po_content" placeholder="PN, model, description" oninput="show_purchase_table()"></label>
    <text style="position:fixed; right:5px;"> Fast search: <input type="search" id="purchase_search" placeholder = "Enter keyword" oninput="show_purchase_table()"></text>
</div>
<div id="wrap" onclick="purchase_cancel()"></div>

<div id="main_div_menu"></div>
<?php include 'footer.php';?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_purchase.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/invoice_func.js"></script>
<script type="text/javascript" src="java/java_stock_new.js"></script>
<script>
document.addEventListener("DOMContentLoaded", show_purchase_table('1')); 
</script>