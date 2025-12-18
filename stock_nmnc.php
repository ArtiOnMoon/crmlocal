<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';

startSession();
security();
if(check_access('acl_stock', 1)) exit('Access denied.');
$currency_data=get_currency_list();

$page_title = 'Stock nomenclature';
include 'header.php';

?>

<div id="side_menu">
    <a class="knopka" onclick="stock_nmnc_new()">Add new item</a>
    <a class="knopka" onclick="display('nmnc_multi_insert')">Multi insert</a>
    <label>Category <?php echo select_stock_class('',1,'id="stock_view" onchange="show_nmnc_table(1)"'); ?></label>
    <span style="padding-right:2em">Manufacturer <?php echo select_manufacturer('',' id="manufacturer" onchange="show_nmnc_table(1)"',1); ?></span>
    Keyword: <input type="search" id="stock_search" placeholder = "Enter keyword" onchange="show_nmnc_table(1)">
    <div style="display:inline-block; background:#AAA; padding:2px;">
        Stock <?php echo select_stock(0, 'id="nmnc_stock_for_sort" onchange="show_nmnc_table(1)"', 1)?>
        <label>Hide out of stock<input type="checkbox" id="nmnc_hide_0" onchange="show_nmnc_table(1)"></label>
    </div>
</div>

<!-- Multiinsert -->
<div id="nmnc_multi_insert" class="hidden" style="width: 80%;height:80%;">
    <div class="close_button_div"><a class="close_button" href="#" onclick="cancel();">&#10006;</a></div>
    <form name="multi_insert_form" action="/scripts/stock_nmnc_multiinsert.php" method="POST" >
        <h2 align="center">New item</h2>
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
            <thead>
            <th>Category</th><th>Description</th><th>Spare part</th><th>Part number</th><th>Type\model</th><th>Manufacturer</th><th>Currency</th><th>Price</th><th>Discount (%)</th><th>Note</th><th></th>
            </thead>
            <tbody id="multi_insert_tbody">
            </tbody>
        </table>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="button" value="Add line" onclick="nmnc_multiinsert_add_line()"> 
        <input type="submit" value="Insert"> 
    </div>
    </form>
    <div style="display:none;">
        <table>
            <tr id="multi_insert_tr">
                <td><?php echo select_stock_class(0, '', 'name="stnmc_type[]" onchange="select_control(this)"');?></td>
                <td><input size="20" type="text" name="stnmc_descr[]" maxlength="250"></td>
                <td><select name="stnmc_is_spare[]" onchange="select_control(this)">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </td>
                <td><input size="10" type="text" name="stnmc_pn[]" maxlength="30" placeholder="Empty if not spare part"></td>
                <td><input size="10" type="text" name="stnmc_type_model[]" maxlength="30"></td>
                <td><?php echo select_manufacturer('','name="stnmc_manuf[]" onchange="select_control(this)"');?></td>
                <td><?php echo select_currency2($currency_data, 0, 'name="stock_currency[]" onchange="select_control(this)"');?></td>
                <td><input type="text" size="5" name="stnmc_price[]"></td>
                <td><input type="number" size="3" length="3" value="0" name="stnmc_discount[]"></td>
                <td><textarea name="stnmc_note[]" maxlength="500" rows="2" cols="20" style="resize: none;"></textarea> </td>
                <td>
                    <input type="button" value="Copy" onclick="nmnc_multiinsert_copy_line(this)">
                    <input type="button" value="Delete" onclick="nmnc_multiinsert_delete_row(this)">
                </td>
            </tr>
        </table>
    </div>
</div>

<main id="main_div_menu"></main>
<?php include 'footer.php';?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_stock_nmnc.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", show_nmnc_table(1));
</script>