<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/stock_fns.php';
?>
<div class="window_internal" style="width:900px;height:300px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
    <form method="POST" onsubmit="return stock_complect_add(this)">
        <input type="hidden" name="return-path" value="window">
        <h2 align="center">New complect</h2>
<div class="block_div2">
<table width="100%">
    <tr>
        <td><b>Complect name</b></td>
        <td class="fancy_td" colspan="3"><input type="text" maxlength="200" size="100" name="complect_name"></td>
    </tr>
    <tr>
        <td><b>Category</b></td>
        <td><?php echo select_stock_class('', 0, 'reqired name="complect_cat"');?></td>
        <td><b>Manufacturer</b></td>
        <td><?php echo select_manufacturer('','reqired name="complect_maker"');?></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td class="fancy_td" colspan="3"><textarea name="complect_note" maxlength="200" rows="2" cols="100" style="resize: none;"></textarea></td>
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