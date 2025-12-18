<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/sales_fns.php';
require_once 'functions/selector.php';

$page_title = 'Sales';
include 'header.php';

if(check_access('acl_sales', 1)) exit('Access denied.');
$cur_list=get_currency_list();
?>
<link rel="stylesheet" type="text/css" href="css/sales.css">

<div id="wrap" onclick="cancel()"></div>
<div id="invis_wrap" onclick="close_menu()"></div>
<div id="side_menu">
    <!-- <a class="knopka" href="new_sale.php">New sale</a> -->
    <a class="knopka" href="#" onclick="sales_new()">New sale</a>
    <div class="menu_container">
        <label class="caption_label">Status<br /><?php echo select_sale_status(0,'id="sales_status" onchange="show_sales_table(1)" style="min-height:20px;"', 1); ?></label> 
    </div>
    <div class="menu_container">
        <label class="caption_label">Company<br /><?php select_our_company($_SESSION['default_company'], 'id="sales_our_comp" onchange="show_sales_table(1)" class="required_select"',1);?></label>
    </div>
    <div class="menu_container">
        <label class="caption_label">Date from<br /></label>
        <input size="10" type="text" class="datepicker" id="date_start" onchange="show_sales_table(1)" value="">
    </div>
    <div class="menu_container">
        <label class="caption_label">Date before</label><br /><input size="10" type="text" class="datepicker" id="date_end" onchange="show_sales_table(1)">
    </div>
    <div class="menu_container">
        <label class="caption_label">Customer</label><br />
        <?php echo selector('customers','id="sales_customer" onchange="show_sales_table(1)"',0);?>
    </div>
    <div class="menu_container">
        <label class="caption_label">Vessel</label><br />
        <?php echo selector('vessels','id="sales_vessel" onchange="show_sales_table(1)"',0);?>
    </div>
    <div style="display:inline-block;float:right;">
        <div class="menu_container">
            <label class="caption_label">Search</label><br />
            <input type="search" id="sales_content" placeholder="PN, model, description" oninput="show_sales_table()">
        </div>
        <div class="menu_container">
            <label class="caption_label">Search</label><br />
            <input type="search" id="sales_search" placeholder = "ID, note, invoice.." onchange="show_sales_table()">
        </div>
    </div>
</div>

<?php
//SALES EXPIRED SCRIPT
//$db= db_connect();
//$query='UPDATE sales SET sales_status=9 WHERE sales_status=5 AND DATEDIFF(CURDATE(),sales_date)>3';
//if(!$db->query($query)) echo 'Warning! Sales auto expire error.';
?>
<div id="main_div_menu"></div>
<?php include 'footer.php';?>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_purchase.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script type="text/javascript" src="java/java_stock_nmnc.js"></script>
<script type="text/javascript" src="java/java_stock_new.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/invoice_func.js"></script>
<script type="text/javascript" src="java/java_vessels.js"></script>
<script>
document.addEventListener("DOMContentLoaded", show_sales_table(1));
</script>