<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/stock_fns.php';
?>
<div class="window_internal" style="width:900px;height:300px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
    <form method="POST" onsubmit="return stock_nmnc_new_add(this)">
        <input type="hidden" name="return-path" value="window">
        <h2 align="center">New nomenclature</h2>
<div class="block_div2">
<table width="100%">
    <tr>
        <td><b>Category</b></td>
        <td><?php echo select_stock_class('');?></td>
        <td><b>Type/model</b></td>
        <td><input  type="text" maxlength="30" id="stnmc_type_model" name="stnmc_type_model" value=""></td>
    </tr>
    <tr>
        <td><b>Manufacturer</b></td>
        <td><?php echo select_manufacturer('','name="stnmc_manuf"');?></td>
        <td><b>P/N</b></td>
        <td><input type="text" maxlength="30" id="spare_pn" name="stnmc_pn" value=""></td>
    </tr>
    <tr>
        <td><b>Description</b></td>
        <td><input type="text" maxlength="250" size="50" name="stnmc_descr" value=""></td>
        <td><b>Spare part</b></td>
        <td><input type="checkbox"  name="stnmc_is_spare" value="1"></td>
    </tr>
    <tr>
        <td><b>Country of origin</b></td>
        <td><?php echo select_country($row['stnmc_origin'], 'name="stnmc_origin"',2);?>
        <td><b>Commodity code</b></td>
        <td><input type="text" size="20" name="stnmc_commod_code" value=""></td>
    </tr>
    <tr>
        <td><b>Currency</b></td>
        <td><?php echo select_currency2(get_currency_list(), '', 'name="stnmc_currency"');?></td>
        <td><b>List price</b></td>
        <td><input type="number" step="0.01" maxlength="10" style="width:5em" name="stnmc_price" value=""></td>
    </tr>
    <tr>
        <td><b>Discount (%)</b></td>
        <td><input type="text" maxlength="4"  size="5" name="stnmc_discount" value=""></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td class="fancy_td" colspan="3"><textarea name="stnmc_note" maxlength="200" rows="2" cols="100" style="resize: none;"></textarea></td>
    </tr>
</table>
</div>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" class="green_button" value="Save" > 
        <input type="button" value="Close" onclick="window_close(this);">
    </div>
</form>
</div>