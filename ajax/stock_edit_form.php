<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/stock_fns.php';
require_once '../functions/selector.php';
$comp_list = get_our_companies_list(1);
?>
<div id="stock_edit" class="window_internal" style="height:700px;">
    <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
    <h2>Edit stock items</h2>
<form id="stock_edit_form" method="POST" onsubmit="return stock_edit_form(this,<?php echo $_SESSION['stock_page'];?>)" action="stock_edit.php">
<table width="100%" class="block_div2" style="margin:0;">
    <tr>
        <td><input type="checkbox" name="input_status" value="1" onchange="activate_td(this)"></td>
        <td><b>Status</b></td>
        <td class="td_for_disable disabledbutton"><?php echo select_stock_stat('',0,'name="stat"');?></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_supplier" value="1" onchange="activate_td(this)"></td>
        <td><b>Supplier</b></td>
        <td class="td_for_disable disabledbutton"><?php echo selector('customers','name="suppl"');?></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_stock" value="1" onchange="activate_td(this)"></td>
        <td><b>Stock</b></td>
        <td class="td_for_disable disabledbutton"><?php echo select_stock('','name="stock"');?></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_place" value="1" onchange="activate_td(this)"></td>
        <td><b>Place</b></td>
        <td class="td_for_disable disabledbutton"><input type="text" name="place" maxlength="20"></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_date_receipt" value="1" onchange="activate_td(this)"></td>
        <td><b>Date of receipt</b></td>
        <td class="td_for_disable disabledbutton"><input type="text" class="datepicker" name="stock_date_receipt" maxlength="20"></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_date_sale" value="1" onchange="activate_td(this)"></td>
        <td><b>Date of sale</b></td>
        <td class="td_for_disable disabledbutton"><input type="text" class="datepicker" name="stock_sale_date" maxlength="20"></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_note" value="1" onchange="activate_td(this)"></td>
        <td><b>Note</b></td>
        <td class="td_for_disable disabledbutton"><textarea name="stock_note" maxlength="500" rows="3"  style="resize: none;width:100%;"></textarea></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_condition" value="1" onchange="activate_td(this)"></td>
        <td><b>Condition</b></td>
        <td class="td_for_disable disabledbutton"><?php echo select_condition('0',0,'name="cond"')?></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_po" value="1" onchange="activate_td(this)"></td>
        <td><b>Recieved by</b></td>
        <td class="td_for_disable disabledbutton">
            <input type="text" size="12" name="stock_po"></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_so" value="1" onchange="activate_td(this)"></td>
        <td><b>Sold by</b></td>
        <td class="td_for_disable disabledbutton">
            <input type="text" size="12" name="stock_so">
        </td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_complect" value="1" onchange="activate_td(this)"></td>
        <td><b>Complect</b></td>
        <td class="td_for_disable disabledbutton">
            <span class="complect_conteiner"><input type="text" size="5" name="stock_compl_id"><img title="View complect" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="complect_view_add(this)"></span>
        </td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_stock_ccd" value="1" onchange="activate_td(this)"></td>
        <td><b>CCD</b></td>
        <td class="td_for_disable disabledbutton"><input type="text"  name="stock_ccd" maxlength="20"></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_stock_currency" value="1" onchange="activate_td(this)"></td>
        <td><b>Currency</b></td>
        <td class="td_for_disable disabledbutton"><?php echo select_currency2(get_currency_list(), 0, 'name="stock_currency"');?></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_stock_price" value="1" onchange="activate_td(this)"></td>
        <td><b>Price</b></td>
        <td class="td_for_disable disabledbutton"><input type="number" step="0.01" name="stock_price" maxlength="20"></td>
    </tr>
    <tr>
        <td><input type="checkbox" name="input_stock_freight" value="1" onchange="activate_td(this)"></td>
        <td><b>Freight</b></td>
        <td class="td_for_disable disabledbutton"><input type="number" step="0.01" name="stock_freight" maxlength="20"></td>
    </tr><tr>
        <td><input type="checkbox" name="input_stock_sold" value="1" onchange="activate_td(this)"></td>
        <td><b>Officialy sold</b></td>
        <td class="td_for_disable disabledbutton"><input type="checkbox" name="stock_officialy_sold"></td>
    </tr>
</table>
    
    <h2>Add transfer info</h2>
    
<table width="100%" class="block_div2" style="margin:0;">
    <tr>
        <td><input type="checkbox" name="transfers_flag" onchange="activate_td(this)"></td>
        <td colspan="2" class="td_for_disable disabledbutton">
        <table>
            <tr>
                <td>From stock</td><td><?php echo select_stock($row['from_stock'],'name="from_stock"');?></td>
                <td>To stock</td><td><?php echo select_stock($row['to_stock'],'name="to_stock"');?></td>
            </tr>
            <tr>
                <td>Ship date</td><td><input type="text" name="ship_date" class="datepicker"></td>
                <td>Receipt date</td><td><input type="text" name="receipt_date" class="datepicker"></td>
            </tr>
            <tr>
                <td>AWB</td><td><input type="text" name="awb"></td>
                <td>Shipped on</td><td><input type="text" name="shipped_on"></td>
            </tr>
            <tr>
                <td>Note</td><td colspan="2"><input type="text" style="width:90%;" name="note"></td>
            </tr>
        </table>
        </td>
    </tr>
</table>
    
    <div style='text-align: center;'>
        <input class="green_button" type="submit" value="Save"> <input type="button" onclick="window_close(this);" value="Cancel">
    </div>
    <p>
        Affected items:<input type="text" name="item_list" id="stock_edit_test" style="border: 2px solid red;">
    </p>
</form>
</div>