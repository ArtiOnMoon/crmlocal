<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/stock_fns.php';
require_once '../functions/transfers.php';

$comp_list = get_our_companies_list(1);
?>
<style>
    .simple_grid_container{
        width:1024px;
        height:90%;
        display:grid;
        grid-template-rows: auto 1fr auto;
        overflow:hidden;
    }
    .simple_grid_header{
        grid-row: 1;
        grid-column: 1;
        border-bottom: 2px solid black;
    }
    .simple_grid_body{
        grid-row: 2;
        grid-column: 1;
    }
    .simple_grid_footer{
        border-top:1px solid black;
        grid-row: 3;
        grid-column: 1;
        padding: 10px;
        text-align: center;
    }
    .selector_stock_menu{
        background: #EEE;
        padding:10px;
    }
</style>
<form method="POST" action="/scripts/transfers_new.php">
<div class="window_internal simple_grid_container">
    <div class="simple_grid_header">
        <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
        <h2 align="center">Select from stock</h2>
        <div class="block_div2">
            <table width="100%">
                <tr>
                    <td><b>Transfer ID</b></td><td><input type="text" name="sales_no" size="5" maxlength="10" value="Auto" disabled="true"></td>
                    <td><b>Status</b></td><td><?php echo select_transfer_status();?></td>
                    <td><b>From stock</b></td><td><?php echo select_stock(0,'name="transfers_from"');?></td>
                    <td><b>To stock</b></td><td><?php echo select_stock(0,'name="transfers_to"');?></td>
                    <td><b>Note</b></td><td rowspan="2"><textarea></textarea></td>
                </tr>
                <tr>
                    <td><b>Date</b></td><td><input type="text" required="true" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="sales_date" value="<?php echo date('Y-m-d');?>"></td>
                    <td><b>AWB</b></td><td><input type="text" size="10"  name="transfers_awb" value=""></td>
                    <td><b>Ship date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="transfers_from_date" value=""></td>
                    <td><b>Delivery date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="transfers_to_date" value=""></td>
                </tr>
            </table>
        </div>
        <div style="text-align: center; margin-bottom: 5px;"><a class="button" href="#" onclick="transfers_stock_select(this)">Select items from stock</a></div>
    </div>
    <div class="stock_selector_sub_form simple_grid_body">

    </div>  
    <div class="simple_grid_footer">
        <a class="knopka green_button" href="#" onclick="return transfers_new_form_submit_outer(this)">Save</a>
        <a class="knopka" href="#" onclick="window_close(this);">Cancel</a> 
    </div>
</div>
</form>