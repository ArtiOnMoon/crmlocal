<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/stock_fns.php';
$stock_id=$_POST['id'];
?>
<div class="window_internal" style="width:800px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this)">&#10006;</a></div>
<h2 align="center">Transfer of stock item #<?php echo $stock_id;?></h2>
<form>
<input type="hidden" name="stock_id" value="<?php echo $stock_id;?>">
<table width="100%">
    <tr>
        <td>From stock</td><td><?php echo select_stock(0,'name="from_stock"');?></td>
        <td>To stock</td><td><?php echo select_stock(0,'name="to_stock"');?></td>
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
        <td>Note</td><td colspan="3"><textarea rows="3" style="width:90%;resize: none;" name="note"></textarea></td>
    </tr>
</table>
<a href="#" class="knopka green_button" onclick="stock_transfer_submit(this)">Save</a> <a href="#" class="knopka" onclick="window_close(this)">Cancel</a>
</form>
</div>