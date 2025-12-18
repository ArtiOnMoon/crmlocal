<?php
require_once 'functions/fns.php';
require_once 'functions/selector.php';
startSession();
//if(check_access('acl_stock', 1)) exit('Access denied.');

do_page_header('Tenders','Tenders');
$comp_list = get_our_companies_list(1);
?>

<div id="side_menu">
    <a class="knopka" href="#" onclick="tender_new()">New stock item</a>
    <a class="knopka" href="#" onclick="stock_multi_insert()">Multi insert</a>
    <a class="knopka" href="#" onclick="reset_filter()">Reset all filters</a>  
</div>

<div id="main_div_menu">Tenders</div>



<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/tenders.js"></script>
<script>
    //document.addEventListener("DOMContentLoaded", show_tenders_table(1));
</script>