<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'functions/stock_fns.php';
$comp_list = get_our_companies_list(1);
$id=$_POST['id'];
$db= db_connect();
$query='SELECT * FROM stock_transfer WHERE transfer_id = "'.$id.'"';
$result=$db->query($query);
$row=$result->fetch_assoc();
?>
<div class="window_internal" style="width:800px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<h2 align="center">Transfer of stock item #<?php echo $row['stock_id'];?></h2>
<form>
<input type="hidden" name="transfer_id" value="<?php echo $row['transfer_id'];?>">
<table width="100%">
    <tr>
        <td>From stock</td><td><?php echo select_stock($row['from_stock'],'name="from_stock"');?></td>
        <td>To stock</td><td><?php echo select_stock($row['to_stock'],'name="to_stock"');?></td>
    </tr>
    <tr>
        <td>Ship date</td><td><input type="text" name="ship_date" class="datepicker" value="<?php echo $row['ship_date'];?>"></td>
        <td>Receipt date</td><td><input type="text" name="receipt_date" class="datepicker" value="<?php echo $row['receipt_date'];?>"></td>
    </tr>
    <tr>
        <td>AWB</td><td><input type="text" name="awb" value="<?php echo $row['awb'];?>"></td>
        <td>Shipped on</td><td><input type="text" name="shipped_on" value="<?php echo $row['shipped_on'];?>"></td>
    </tr>
    <tr>
        <td>Note</td><td colspan="3"><textarea rows="3" style="width:90%;resize: none;" name="note"><?php echo $row['note'];?></textarea></td>
    </tr>
    <tr>
        <td><strong>Delete</strong></td><td colspan="3"><input type="checkbox" name="is_deleted" value="1"></td>
    </tr>
</table>
<a href="#" class="knopka green_button" onclick="stock_transfer_change(this)">Save</a> <a class="knopka" href="#" onclick="window_close(this);">Cancel</a>
</form>
</div>
