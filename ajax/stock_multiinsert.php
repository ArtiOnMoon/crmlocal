<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/stock_fns.php';
//$po_id=clean($_POST['po_id']);
$comp_list = get_our_companies_list(1);
$curr_list = get_currency_list();
?>
<div class="window_internal window_container" style="width:1280px;height:90%;">
    <div class="grid_window_header">
    <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<?php

?>
    <h2 align="center">Multiple insert</h2>
    <form class="multiinsert_header_form">
        <table width="100%">
            <tr>
                <td><b>Owner</b></td><td><?php select_our_company(0, 'id="stock_our_company" required name="stock_our_company"')?></td>
                <td><b>Date of receipt</b></td><td><input type="text" size="8" id="date_receipt" name="date_receipt" required class="datepicker" value="<?php echo date('Y-m-d');?>"></td>
                <td><b>Stock</b></td><td><?php echo select_stock('','id="stock" required');?></td>
                <td><b>Purchase order</b></td><td><?php echo select_our_company2($comp_list,0,'id="stock_po_comp"'); ?><input type="text" size="10"  id="stock_po"></td>
                <td><b>Sales order</b></td><td><?php echo select_our_company2($comp_list,0,'id="stock_so_comp"'); ?><input type="text" size="10"  id="stock_so"></td>
            </tr>
        </table>
    </form>
        <hr>
    </div>
    <div class="grid_window_body1"></div>
    <div class="grid_window_body2">
        <form id="stock_multi_insert">
            <table class="block_div2 multiinsert_line">
                <tr>
                    <td style="background: #AAA;"><b>Base item</b></td><td colspan="3" style="background: #AAA;"><?php echo selector('stock_nmnc','name="stock_nmnc_id[]"');?></td>
                    <td><b>Supplier</b></td><td><?php echo selector('customers','name="stock_supplier[]"');?></td>
                    <td><b>Status</b></td><td><?php echo select_stock_stat(1,0,$headers='onchange="select_control(this)" name="stock_status[]"');?></td>
                    <td><b>Condition</b></td><td><?php echo select_condition('1','0', 'name="stock_condition[]" onchange="select_control(this)"');?></td>
                    <td><b>Serial</b></td><td><input class="input_serial" type="text" name="stock_serial[]" size="12" style="background: #e8ffec;"></td>
                    <td><a class="knopka" onclick="add_insert(this)" href="#">New line &#8595</a></td>
                </tr>
                <tr>
                    <td><b>Currency</b></td><td><?php echo select_currency2($curr_list, 0, 'name="stock_currency[]" required onchange="select_control(this)"');?></td>
                    <td><b>Price</b></td><td><input type="number" step="0.01" style="width:65px" name="stock_price[]"></td>
                    <td><b>CCD</b></td><td><input type="text" name="stock_ccd[]" maxlength="300" size="20"></td>
                    <td><b>Note</b></td><td><input type="text" name="stock_note[]" maxlength="300" size="20"></td>
                    <td><b>Place</b></td><td><input type="text" size="5" name="stock_place[]"></td>
                    <td><b>Complect</b></td><td><input type="text" size="8" name="stock_compl_id[]"></td>
                    <td><a class="knopka" href="#" onclick="delete_multi_line(this)">Delete</a></td>
                </tr>
            </table>
        </form>
    </div>
    <div class="grid_window_footer" align="center">
    <hr>
        <a href="#" class="knopka green_button" onclick="multiple_stock_insert(this)">Add to stock</a>
        <a href="#" class="knopka" onclick="window_close(this);">Close</a>
    </div>
</div>