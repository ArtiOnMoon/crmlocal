<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';

startSession();
if(check_access('acl_service', 1)) { exit('Access denied.');}

$page_title = 'Service rates';
include 'header.php';


?>
<div id="wrap" onclick="cancel()"></div>
<div id="side_menu">
    <a class="knopka" href="#"  onclick="display('new_rate')">New rate</a>
    <a class="knopka" href="#"  onclick="display('new_rate_cat')">New rate category</a>
    <label>Company <?php echo select_our_company(0,'id="rate_our_comp" onchange="show_rates_table()"');?></label>
    <label>Currency <?php echo select_currency2(get_currency_list(),'','id="select_currency" onchange="show_rates_table()"',1);?></label>
    <span style="float: right;"> Fast search: <input type="search" id="rates_search" placeholder = "Enter Description, PO, technician" oninput="fast_search()"></span>  
</div>
<div id="main_div_menu"></div>
<div id="new_rate" class="hidden">
    <h1 align="center">New rate</h1>
    <form name="rate_form" action="service_rate_new.php" method="POST" width="300Px">
    <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Category</b></td>
            <td><?php echo select_service_rates_cat(''); ?></td>
        </tr>
        <tr>
            <td width="100"><b>Description</b></td>
            <td><textarea required rows="3" cols="50" maxlength="500" name="rate_name"></textarea></td>
        </tr>
        <tr>
            <td width="100"><b>Price</b></td>
            <td><input type="text" maxlength="8" name="rate_price" required></td>
        </tr>
        <tr>
            <td width="100"><b>Currency</b></td>
            <td><?php echo select_currency2(get_currency_list());?></td>
        </tr>
    </table> 
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add rate">
        <input type="button" value="Close" onclick="cancel()"> 
    </div>
    </form>
</div>
<div id="new_rate_cat" class="hidden">
    <h1 align="center">New rate category</h1>
    <form action="service_rate_cat_new.php" method="POST" width="300Px">
    <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Company</b></td>
            <td><?php echo select_our_company(0,'name="rate_our_comp"');?></td>
        </tr>
        <tr>
            <td width="100"><b>Category_name</b></td>
            <td><textarea required rows="3" cols="50" maxlength="500" name="rate_cat_name"></textarea></td>
        </tr>
    </table> 
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add category">
        <input type="button" value="Close" onclick="cancel()"> 
    </div>
    </form>
</div>

<?php include 'footer.php';?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/service_rates.js"></script>