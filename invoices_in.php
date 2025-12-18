<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'functions/invoice_fns.php';
startSession();

if(check_access('acl_invoices', 1)) exit('Access denied.');

$page_title = 'Invoices';
include 'header.php';

?>

<div id="side_menu">
    <a class="knopka" href="#" onclick="invoice_new()">New invoice</a>
    <label>Status <?php echo select_invoice_status(0,'id="invoice_status" onchange="show_invoice_in_table(1)"',1);?></label>
    <label>Company <?php select_our_company($_SESSION['default_company'], 'id="invoice_our_comp" onchange="show_invoice_in_table(1)"',1);?></label>
    <label>Customer <div style="display:inline-block;width:200px"><?php echo selector('customers','id="invoice_customer" onchange="show_invoice_in_table(1)"');?></div></label>
    <label>Currency <?php echo select_currency(0, 'id="invoice_currency" onchange="show_invoice_in_table(1)"',1);?></label>
    Date <input type="search" id="invoice_date_from" class="datepicker" size="9" onchange="show_invoice_table(1)"> - <input type="search" id="invoice_date_to" class="datepicker" size="9" onchange="show_invoice_in_table(1)">
    <span style="position:fixed; right:5px;"> Fast search: <input type="search" id="invoice_search" placeholder = "Enter invoice number" oninput="show_invoice_in_table(1)"></span>
</div>

<main id="main_div_menu"></main>

<?php include 'footer.php';?>

<link href="/css/invoices.css" rel="stylesheet">
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/invoice_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_purchase.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script type="text/javascript" src="java/java_stock_nmnc.js"></script>
<script>document.addEventListener("DOMContentLoaded", show_invoice_in_table('1'));</script>