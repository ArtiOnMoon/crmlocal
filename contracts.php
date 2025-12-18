<?php
require_once 'functions/fns.php';
require_once 'functions/selector.php';
startSession();
//if(check_access('acl_stock', 1)) exit('Access denied.');
do_page_header('Contracts');
$comp_list = get_our_companies_list(1);
?>

<div id="side_menu">
    <a class="knopka" href="#"  onclick="contracts_new()">New contract</a>
    <label>Our company <?php select_our_company(0, 'id="contracts_our_company" onchange="show_contracts_table(1)"')?></label>
    <label>Customer <div style="display:inline-block;width:200px"><?php echo selector('customers','id="contract_customer" onchange="show_contracts_table(1)"',0);?></div></label>
    <label>Search <input type="search" id="contract_search" onchange="show_contracts_table(1)"></label>
</div>
<div id="main_div_menu">Text</div>  


<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_contracts.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", show_contracts_table(1));
</script>