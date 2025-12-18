<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';

startSession();
security();
if(check_access('acl_stock', 1)) exit('Access denied.');

$page_title = 'Stock complects';
include 'header.php';
?>


<div id="side_menu">
    <a class="knopka" onclick="complect_new()">New complect</a>
    <label>Category <?php echo select_stock_class('',1,'id="compl_cat" onchange="show_complects_table(1)"'); ?></label>
    <span style="padding-right:2em">Manufacturer <?php echo select_manufacturer('',' id="manufacturer" onchange="show_complects_table(1)"',1); ?></span>
    <span style="padding-right:2em">Status <?php echo selector_multi_stock_status([1,2,4,5,6,7,8,9],'id="stock_id"',"show_complects_table(1)");?></span>
    Keyword: <input type="search" id="complect_search" placeholder = "Enter keyword" onchange="show_complects_table(1)">
</div>
<?php $currency_data=get_currency_list();?>


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
    document.addEventListener("DOMContentLoaded", show_complects_table(1));
</script>