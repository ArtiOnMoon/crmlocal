<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
do_page_header('Stock nomenclature','Stock nomenclature');
startSession();
security();
if(check_access('acl_stock', 1)) exit('Access denied.');
?>
<script type="text/javascript" src="java/java_func.js"></script>
<div id="wrap" onclick="cancel()"></div>
<div id="invis_wrap" onclick="close_menu()"></div>
<div id="window" class="hidden" style="width:1280px;height:90%;"></div>
<div id="side_menu">
    <a class="knopka" onclick="stock_nmnc_new()">Add new item</a>
    <a class="knopka" onclick="display('nmnc_multi_insert')">Multi insert</a>
    <label>Category <?php echo select_stock_class('',1,'id="stock_view" onchange="show_balance_table(1)"'); ?></label>
    <span style="padding-right:2em">Manufacturer <?php echo select_manufacturer('',' id="manufacturer" onchange="show_balance_table(1)"',1); ?></span>
    Keyword: <input type="search" id="stock_search" placeholder = "Enter keyword" onchange="show_balance_table(1)">
</div>
<?php $currency_data=get_currency_list();?>
<!--//New item-->
<div id="new_item" class="hidden">
    <div class="close_button_div"><a class="close_button" href="#" onclick="cancel();">&#10006;</a></div>
    <form name="new_item_form" action="/scripts/stock_nmnc_new.php" method="POST" >
        <h2 align="center">New item</h2>
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
            <tr>
                <td><b>Category</b></td>
                <td><?php echo select_stock_class('');?></td>
            </tr>
            <tr>
                <td><b>Description</b></td>
                <td><input type="text" name="stnmc_descr" size="80" maxlength="250"></td>
            </tr>
            <tr>
                <td><b>Type/model</b></td>
                <td><input size="80" type="text" name="stnmc_type_model" maxlength="30"></td>
            </tr>
            <tr>
                <td><b>Spare part</b></td>
                <td>
                    <input type="checkbox" name="stnmc_is_spare" value="1"> 
                </td>
            </tr>
            <tr>
                <td><b>Part number</b></td>
                <td><input size="80" type="text" name="stnmc_pn" maxlength="30"></td>
            </tr>
            <tr>
                <td><b>Manufacturer</b></td>
                <td><?php echo select_manufacturer('', 'class="combobox" name="stnmc_manuf"');?></td>
            </tr>
            <tr>
                <td><b>Currency</b></td>
                <td><?php echo select_currency2($currency_data, 0, 'name="stnmc_currency" onchange="select_control(this)"');?></td>
            </tr>
            <tr>
                <td><b>Price</b></td>
                <td><input type="text" size="10" name="stnmc_price"></td>
            </tr>
            <tr>
                <td><b>Our discount (%)</b></td>
                <td><input type="number" size="10" value="0" name="stnmc_discount"></td>
            </tr>
            <tr>
                <td><b>Commodity code</b></td>
                <td><input type="text" size="20" name="stnmc_commod_code"></td>
            </tr>
            <tr>
                <td><b>Country of origin</b></td>
                <td><?php echo select_country(0, 'name="stnmc_origin"',2);?>
            </tr>
            <tr>
                <td><b>Note</b></td>
                <td><textarea name="stnmc_note" maxlength="500" rows="3" cols="80" style="resize: none;"></textarea> </td>
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
        <input class="button" type="submit" value="Insert"> 
    </div>
    </form>
    <div style="display:none;">
        <table>
            <tr id="multi_insert_tr">
                <td><?php echo select_stock_class(0, '', 'name="stnmc_type[]" onchange="select_control(this)"');?></td>
                <td><input type="text" name="stnmc_descr[]" size="50" maxlength="250"></td>
                <td><select name="stnmc_is_spare[]" onchange="select_control(this)">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </td>
                <td><input type="text" name="stnmc_pn[]" maxlength="30" placeholder="Empty if not spare part"></td>
                <td><input type="text" name="stnmc_type_model[]" maxlength="30"></td>
                <td><?php echo select_manufacturer('','name="stnmc_manuf[]" onchange="select_control(this)" style="width:100%"');?></td>
                <td><?php echo select_currency2($currency_data, 0, 'name="stock_currency[]" onchange="select_control(this)"');?></td>
                <td><input type="text" size="5" name="stnmc_price[]"></td>
                <td><input type="number" size="3" length="3" value="0" name="stnmc_discount[]"></td>
                <td><textarea name="stnmc_note[]" maxlength="500" rows="2" cols="40" style="resize: none;"></textarea> </td>
                <td>
                    <input type="button" value="Copy" onclick="nmnc_multiinsert_copy_line(this)">
                    <input type="button" value="Delete" onclick="nmnc_multiinsert_delete_row(this)">
                </td>
            </tr>
        </table>
    </div>
</div>

<div id="main_div_menu"></div>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_stock_balance.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", show_balance_table(1));
</script>