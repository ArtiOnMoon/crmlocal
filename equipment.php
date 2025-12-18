<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';

$page_title = 'Vessels equipment';
include 'header.php';

if(check_access('acl_service', 1)) exit('Access denied.');
?>

<div id="side_menu">
    <a class="knopka" href="#"  onclick="add_new_equipment()">Add equipment</a>
    <label>Category <?php echo select_stock_class('', 3,'id="category" onchange="show_equipment_table()"'); ?></label>
    <label>Manufacturer <?php echo select_manufacturer('',' id="manufacturer" onchange="show_equipment_table()"',1); ?></label>
    <span> Keyword: <input type="search" id="keyword" placeholder = "Search..." oninput="show_equipment_table()"></span>
    <button class="button" onclick="reset_filters()">Reset filter</button>
</div>
<main id="main_div_menu">Test</main>
<?php include"footer.php"; ?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_vessels.js"></script>
<script type="text/javascript" src="java/java_equipment_func.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", show_equipment_table(1)); 
</script>