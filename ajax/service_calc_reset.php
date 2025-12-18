<?php
require_once '../functions/auth.php';
require_once '../functions/db.php';

$db= db_connect();
$service_our_comp=$_POST['service_our_comp'];
$query='SELECT rate_cat_id,rate_cat_name FROM service_rates_cat WHERE rate_our_comp="'.$service_our_comp.'" ORDER BY rate_order';
$result=$db->query($query);
$i=1;
?>
<thead>
        <th width="5%">№</th>
        <th width="60%">Description</th>
        <th width="7%">Q-ty</th>
        <th width="7%">Unit price</th>
        <th width="7%">Discount %</th>
        <th width="7%">Amount</th>
        <th width="7%"></th>
    </thead>
    <tbody>
        <tr class="header_row "><td colspan="6">Product service report <?php echo $row['service_no'];?></td>
            <td><input type="button" class="small_button" value="Reset form" onclick="reset_rates()"></td></tr>
<?php
if ($result->num_rows>0){
    while ($entries=$result->fetch_assoc()){
        ?>
                </tbody>
                <tbody>
                <tr>
                <td class="number_td calc_fancy_td"><strong><?php echo $i;?></strong></td>
                <td class="calc_fancy_td align_left">
                    <span class="table_input input_header"><?php echo $entries['rate_cat_name'];?></span>
                    <input class='table_input input_header' type="hidden" maxlength="250" name='entry_text[]' value="<?php echo $entries['rate_cat_name'];?>">
                </td>
                <td colspan="5" class="calc_action_td calc_fancy_td"><input type="button" class='small_button' value="Add" onclick="entry_rate(this)">
                    <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
                    <input type='hidden' value='0' class="qty_input" name='entry_qty[]'>
                    <input type='hidden' value='0' class="price_input" name='entry_price[]'>
                    <input type='hidden' value='0' class="discount_input" name='entry_discount[]'>
                    <input type='hidden' value='0' class="amount_input">
                    <input type="hidden" name='entry_type[]' value='3'>
                    <input type="hidden" class="entry_base_id" name='entry_base_id[]' value='<?php echo (int)$entries['rate_cat_id'];?>'>
                </td></tr>
                <?php
                $i++;
    }
}
?>
</tbody><tbody id="entries_spares_body">
    <tr><td class="number_td calc_fancy_td"><strong><?php echo $i++;?></strong></td>
        <td class="calc_fancy_td"><input type="text" value="Spare parts and materials:" readonly class='table_input input_header'></td>
        <td colspan="5" class="calc_action_td calc_fancy_td">
            <input type="button" class='small_button' value="Add spare" onclick="entry_spare(this)">
            <input type='hidden' value='0' class="qty_input">
            <input type='hidden' value='0' class="price_input" >
            <input type='hidden' value='0' class="discount_input">
            <input type='hidden' value='0' class="amount_input">
        </td></tr>
</tbody><tbody>
<tr>
    <td class="number_td calc_fancy_td"><strong><?php echo $i++;?></strong></td>
    <td class="calc_fancy_td"><input class='table_input input_header' type="text" maxlength="250" name='entry_text[]' value="Port expenses:"></td>
    <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
    <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="20" maxlength="6" name='entry_price[]'></td>
    <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="0" maxlength="6" name='entry_discount[]'></td>
    <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="20">
        <input type="hidden" name='entry_type[]' value='4'>
        <input type="hidden" name='entry_base_id[]' value='0'>
    </td>
    <td class="calc_fancy_td calc_action_td">
        <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
    </td>
 </tr>
 <tr>
    <td class="calc_fancy_td number_td"><strong><?php echo $i++;?></strong></td>
    <td class="calc_fancy_td"><input class='table_input input_header' type="text" maxlength="250" name='entry_text[]' value="Administrative fees:"></td>
    <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
    <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="20" maxlength="6" name='entry_price[]'></td>
    <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="0" maxlength="6" name='entry_discount[]'></td>
    <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="20">
        <input type="hidden" name='entry_type[]' value='4'>
        <input type="hidden" name='entry_base_id[]' value='0'>
    </td>
    <td class="calc_fancy_td calc_action_td">
        <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
    </td>
 </tr>
 <tr><td colspan="7"><input type="button" class='small_button' value="Add other expenses" onclick="entry_header_calc(this)"></tr>