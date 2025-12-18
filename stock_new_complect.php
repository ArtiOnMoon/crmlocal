<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
startSession();
if(check_access('acl_stock', 2)) exit('Access denied.');
do_page_header('New stock complect');
?>
<div id="main_div_menu2" style="align-content: center;text-align: center">
<div style="width: 1024px; align-content: center; display: inline-block">
<form name="new_complect_form" method="POST" action="stock_complect_add.php" onsubmit="add_complect()">
        <h2 align="center">New complect</h2>

<table width="100%" border="1px" cellspacing = "0" cellpadding="2px" style="text-align: left">
    <tr>
        <td><b>Complect name</b></td>
        <td><input type="text" required name="complect_name"></td>
    </tr>
    <tr>
        <td width="150"><b>Class</b></td>
        <td><?php echo select_stock_class()?></td>
    </tr>
    <tr>
        <td><b>Category</b></td>
        <td><?php select_stock_status('in stock',2) ?></td>
    </tr>
    <tr>
        <td><b>Manufacturer</b></td>
        <td><?php echo select_customer('','','complect_manufacturer') ?></td>
    </tr>
    <tr>
        <td><b>Supplier</b></td>
        <td><?php echo select_customer('','','complect_supplier') ?></td>
    </tr>
    <tr>
        <td><b>PN or type</b></td>
        <td><input type="text" required name="complect_pn"></td>
    </tr>
    <tr>
        <td><b>Description</b></td>
        <td><input type="text" name="complect_desc"></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td><input type="text" name="complect_note"></td>
    </tr>
    <tr>
        <td><b>Condition</b></td>
        <td><input type="text" name="complect_cond"></td>
    </tr>
    <tr>
        <td><b>Serial number</b></td>
            <td><input type="text" required name="complect_serial"></td>
    </tr>
    <tr>
        <td><b>Purchase order</b></td>
        <td><input type="text" name="complect_po"></td>
    </tr>
    <tr>
        <td><b>Date of receipt</b></td>
        <td><input type="text" required class="datepicker" name="complect_date_receipt" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d');?>"></td>
    </tr>
    <tr>
        <td><b>Place</b></td>
        <td><input type="text" required name="complect_place"></td>
    </tr>
    <tr>
        <td><b>Currency</b></td>
        <td><?php echo select_currency('') ?></td>
    </tr>
    <tr>
        <td><b>Net price</b></td>
        <td><input type="text" name="complect_net"></td>
    </tr>
    <tr>
        <td><b>Minimal price</b></td>
        <td><input type="text" name="complect_min_price"></td>
    </tr>
    <tr>
        <td><b>Warranty up to</b></td>
        <td><input type="text" class="datepicker" placeholder="yyy-mm-dd" name="complect_warranty"></td>
    </tr>
    <tr>
        <td><b>Engineer code</b></td>
        <td><input type="text" name="new_stock_eng"></td>
    </tr>
    <tr>
        <td><b>On balance</b></td>
        <td><input type="checkbox" name="complect_on_balance" value="1"></td>
    </tr>
</table>


<h2>Content</h2>     

<table id="complect_content" width="100%" border="1px" cellspacing = "0" cellpadding="2px">
    <th>Type</th><th>PN</th><th>Description</th><th>Serial</th><th>Delete</th>
    <!--<tr><td colspan="7" style="border:none" align="right"><b>Total: <b></td><td id="total" style="border:none" align="left">123</td></tr> -->
</table>
<input type="button" value="Add content" onclick="add_content()">
    
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="New purchase" onsubmit="return add_new_purchase()"> 
    </div>
    <input type="hidden" id="content_field" name="content">
    <input type="hidden" id="content_length" name="content_length">
</form>
</div>
</div>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_stock_complect.js"></script>
<script>
    $("select").combobox();
</script>

    