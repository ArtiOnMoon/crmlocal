<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/stock_fns.php';
$comp_list = get_our_companies_list(1);
?>
<style>
    .stock_view_grid_conteiner{
        display:grid;
    }
</style>
<div class="window_internal" style="width:1024px;height:400px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<h2 align="center">Add stock item</h2>
<form name="new_stock_item_form" width="300Px" method="POST" onsubmit="return stock_new_add(this)">
<div class="stock_view_grid_conteiner">
    <div class="block_div2 calc_fancy_div">
        <div class="calc_fancy_div">
        <b>Base item</b>
            <?php // echo selector_nmnc_long(get_stock_category_list(), get_manufacturers_list());?> 
            <?php echo selector('stock_nmnc','name="stock_nmnc_id"');?> 
            <a href="#" onclick="stock_nmnc_new()" style="font-size: larger;">+</a>
        </div>
    </div>
        <div class="block_div2 calc_fancy_div">
            <div class="calc_fancy_div"><b>Serial</b> <input type="text" maxlength="50" name="stock_serial" value=""></div>
            <div class="calc_fancy_div"><b>Status</b> <?php echo select_stock_stat();?></div>
            <div class="calc_fancy_div"><b>Condition</b> <?php echo select_condition('','0','name="stock_condition"');?></div>
            <div class="calc_fancy_div"><b>Owner</b> <?php echo select_our_company2($comp_list,'','required name="stock_our_company"'); ?></div>
            <div class="calc_fancy_div"><label><b>Officialy sold</b> <input type="checkbox" name="stock_officialy_sold" value="1" ></label></div>
        </div>
        <div class="block_div2 calc_fancy_div">
            <div class="calc_fancy_div"><b>Supplier</b> <?php echo selector('customers','name="stock_supplier"');?></div>
            <div class="calc_fancy_div">
                <b>Received by</b> <input type="text" size="10"  name="stock_po" value="<?php echo $row['stock_po'];?>">
            </div>
            <div class="calc_fancy_div">
                <b>Sold by</b> <input type="text" size="12"  name="stock_so" value="<?php echo $row['stock_so'];?>">
            </div>
        </div>
        <div class="block_div2 calc_fancy_div">
            <div class="calc_fancy_div"><b>Stock</b> <?php echo select_stock();?></div>
            <div class="calc_fancy_div"><b>Place</b> <input type="text" size="10" maxlength="30" name="stock_place"></div>
            <div class="calc_fancy_div"><b>Date of receipt</b> <input type="text" size="10" class="datepicker" name="stock_date_receipt" value="<?php echo date('Y-m-d');?>"></div>
            <div class="calc_fancy_div"><b>Date of sale</b> <input type="text" size="10" class="datepicker" name="stock_sale_date" value=""></div>
            <div class="calc_fancy_div"><b>CCD</b> <input type="text" size="20" name="stock_ccd" value=""></div>
        </div>
        <div class="block_div2 calc_fancy_div">
            <div class="calc_fancy_div"><b>Price</b> <input type="number" step="0.01" style="width:65px" name="stock_price"></div>
            <div class="calc_fancy_div"><b>Freight</b> <input type="number" step="0.01" style="width:65px" name="stock_freight"></div>
            <div class="calc_fancy_div"><b>Currency</b> <?php echo select_currency2(get_currency_list(), '', 'name="stock_currency"');?></div>
            <div class="calc_fancy_div"><b>Warranty up to</b> <input type="text" size="10" name="stock_warranty_to" class="datepicker" value=""></div>
            <div class="calc_fancy_div"><b>Is complect</b> <input type="checkbox" name="stock_is_compl" value="1"></div>
            <div class="calc_fancy_div complect_conteiner"><b>Complect</b> <input type="text" size="5" name="stock_compl_id" value=""><img title="View complect" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="complect_view_add(this)"></div>
        </div>
        <table class="fancy_table">
            <tr>
                <td class="fancy_td block_div2">
                    <b>Note</b>
                    <textarea name="stock_note" maxlength="500" rows="2" style="width:990px;"><?php echo $row['stock_note']; ?></textarea>
                </td>
            </tr>
        </table>
</div>
<div align="right" width="100%" style="padding: 10px">
    <input type="submit" value="Add stock item">
    <input type="button" value="Close" onclick="window_close(this);"> 
</div>
</form>
</div>