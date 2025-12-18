<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/purchase_fns.php';
startSession();
do_page_header('New purchase');
if(check_access('acl_purchase', 1)) exit('Access denied.');
?>
<div id="main_div_menu2" style="align-content: center;text-align: center">
<div style="width: 1024px; align-content: center; display: inline-block">
<form name="new_purchase_form" method="POST" action="add_purchase.php" onsubmit="add_new_purchase()" multipart="" enctype="multipart/form-data">
        <h2 align="center">New purchase</h2>
            <table width="100%" border="1px" cellspacing = "0" cellpadding="2px" style="text-align: left">
                <tr>
                    <td><b>Our company</b></td><td>
                        <?php select_our_company(); ?></td>
                </tr>
                <tr>
                    <td><b>Purchase order</b></td><td>
                        <input type="text" name="purchase_order" required maxlength="30"></td>
                </tr>
                <tr>
                    <td><b>Sales order</b></td><td>
                        <input type="text" name="sales_order" required maxlength="30"></td></td>
                </tr>
                <tr>
                    <td><b>PO date</b></td><td>
                        <input type="text" placeholder='yyyy-mm-dd' name="po_date" required class="datepicker" value="<?php echo date('Y-m-d'); ?>"></td>
                </tr>
                <tr>
                    <td><b>Status</b></td><td>
                        <?php select_purchase_status(); ?></td>
                </tr>
                <tr>
                    <td><b>Shipment date</b></td><td>
                        <input type="text" placeholder='yyyy-mm-dd' name="shipment_date" class="datepicker"></td>
                </tr>
                <tr>
                    <td><b>Customer</b></td>
                    <td><?php echo select_customer(''); ?></td>
                </tr>
                <tr>
                        <td><b>Shipper</b></td><td>
                            <?php echo select_customer('','','shipper'); ?></td>
                </tr>
                <tr>
                    <td><b>AWB</b></td>
                    <td><input type="text" name="new_awb"></td>
                </tr>
                <tr>
                    <td><b>Order of acknowledgement</b></td>
                    <td><input type="text" name="order_ackn"></td>
                </tr>
                <tr>
                        <td><b>Currency</b></td><td>
                            <?php echo select_currency('none'); ?></td>
                </tr>
                        <td><b>Invoice</b></td><td>
                            <input type="text" name="invoice" maxlength="30"></td>
                </tr>
                </tr>
                        <td><b>Invoice date</b></td><td>
                            <input type="text" name="invoice date" class="datepicker" maxlength="30"></td>
                </tr>
                <tr>
                        <td><b>Delivery address</b></td>
                        <td><textarea name="delivery_addr" maxlength="500" cols="100" rows="5" style="resize: none;"></textarea></td>
                </tr>
                <tr>
                        <td><b> Note</b></td>
                        <td><textarea name="note" maxlength="500" cols="100" rows="5" style="resize: none;"></textarea></td>
                </tr>
            </table>

<h2>Content</h2>     

<table id="purchase_content" width="100%" border="1px" cellspacing = "0" cellpadding="2px">
    <th>Type</th><th>PN</th><th>Description</th><th>Quantity</th><th>Delete</th>
    <!--<tr><td colspan="7" style="border:none" align="right"><b>Total: <b></td><td id="total" style="border:none" align="left">123</td></tr> -->
</table>
<input type="button" value="Add content" onclick="add_content()">
    
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="New purchase" onsubmit="add_new_purchase()"> 
    </div>
    <div id="purchase_status" align="center" width="100%" style="padding: 10px"></div>
    <input type="hidden" id="content_field" name="content">
    <input type="hidden" id="content_length" name="content_length">
</form>
</div>
</div>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_purchase.js"></script>
<script>
    $("select").combobox();
</script>

    