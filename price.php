<?php
require_once 'functions/fns.php';
require_once 'functions/invoice_func.php';
do_page_header('A-Z Price list','A-Z Price list');
startSession();
security();
if(check_access('acl_stock', 1)) exit('Access denied.');
?>
<script type="text/javascript" src="java/java_func.js"></script>
<div id="wrap" onclick="cancel()"></div>
<div id="invis_wrap" onclick="close_menu()"></div>
<div id="window" class="hidden" style="width:90%;height:90%;"></div>
<div id="side_menu">
<input type="button" value="Add new item" onclick="display('new_price')">
<?php echo select_stock_class('',1,'id="stock_view" onchange="show_price_table(1)"') ?>
<span style="position:fixed; right:5px;">Fast search: <input type="search" id="price_search" placeholder = "Enter part number" oninput="show_price_table()"></span>
</div>
<div id="main_div_menu">TEST</div>
<div id="new_price" class="hidden">
    <form name="new_price_form" action="add_price.php" method="POST" >
        <h2 align="center">New item</h2>
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
            <tr>
                <td><b>Class</b></td>
                <td><?php select_stock_class('');  ?></td>
            </tr>
            <tr>
                <td><b>Part number</b></td><td>
                <input required type="text" name="pn" length="20"></td>
            </td></tr>
            <tr>
                <td><b>Manufacturer</b></td>
                <td><?php echo select_customer('');?></td>
            </tr>
            <tr>
                <td><b>Description</b></td>
                <td><input required type="text" name="description" maxlength="250"></td>
            </tr>
            <tr>
                <td><b>Date</b></td>
                <td><input required type="text" class="datepicker" name="date" placeholder="yyyy-mm-dd" value="<?php echo date("Y-m-d") ?>"></td>
            </tr>
            <tr>
                <td><b>Currency</b></td>
                <td><?php echo select_currency();?>
            </tr>
            <tr>
                <td><b>Price</b></td>
                <td><input required type="text" name="price"></td>
            </tr>
            <tr>
                <td><b>Discount (%)</b></td>
                <td><input required type="text" name="discount"</td>
            </tr>
        </table>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add item" > 
        <input type="button" value="Close" onclick="cancel('new_price')">
    </div>
    <div id="vote_status" align="center" width="100%" style="padding: 10px"></div>
    </form>
</div>


<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_price.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>