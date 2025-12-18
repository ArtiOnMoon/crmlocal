<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
require_once 'functions/selector.php';
startSession();
security ();
if(check_access('acl_invoices', 1)) exit('Access denied.');
do_page_header('Calculation','Service');
$id=clean($_GET['service_id']);
$saved=isset($_GET['saved']);
$db =  db_connect();

//Проверка существования проформы
$proforma_exist=1;
$query= 'SELECT service_calculation.*, service.service_no FROM service_calculation, service WHERE service.service_id=service_calculation.calc_id AND calc_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1){
    //Проформа не найдена,проверка в сервисе
    $proforma_exist=0;
    $query= 'SELECT * from service_calculation WHERE service_id = "'.$id.'"';
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Service order not found');
    }
}
$service=$result->fetch_assoc();
?>
<link rel="stylesheet" href="css/calculation.css">
<div id="wrap" onclick="cancel()"></div>
<div id="window" class="hidden" style="width:90%;height:90%;"></div>
<div id="main_div_menu2" style="align-content: center;text-align: center">
<div style="width: 1024px; align-content: center; display: inline-block">
<h1>Service <?php echo $service['service_no'];?></h1>
<span id="saved" class="greentext">
<?php if($saved) echo 'Successfully saved!';?>
</span>
<div style="text-align: left; display:none;">
    <!--кнопка отправить запрос -->
    <a id="pdf_button"<?php if($proforma_exist===0) echo 'class="button_disabled"';else echo 'class="knopka"';?>
        href="/scripts/service_proforma_sent.php?service_id=<?php echo $id;?>">Send</a>
    <?php if($proforma_exist && $service['proforma_on_sign']) echo'<i> Proforma sent</i>';?>
</div>     
<br>
<form method="POST" action="/scripts/service_calc_save.php" onsubmit="save_proforma()">
<table width="100%" style="text-align: left">
    <tr>
        <td><strong>Customer</strong></td>
        <td><?php echo select_customer($service['calc_cust_id']);?></td>
        <td><strong>Our company</strong></td>
        <td><?php echo select_our_company($service['calc_our_company'], 'name="our_company" required', 0);?></td>
    </tr>
    <tr>
        <td><strong>Customer PO</strong></td>
        <td><input type='text' onchange="changed()" name="calc_your_ref" value="<?php echo $service['calc_your_ref'];?>"></td>
        <td><strong>Currency</strong></td>
        <td><?php echo select_currency2(get_currency_list(),$service['currency'], 'name="currency" id="currency_select" onchange="rates_currency_filter()"');?></td>
    </tr>
    <tr>
        <td><strong>Service order</strong></td>
        <td><?php echo view_order_link($id, $service['service_no']);?></td>
        <td><strong>Payment terms</strong></td>
        <td><input type="text" onchange="changed()" name="pay_terms"  maxlength="300" value="<?php echo $service['calc_pay_terms'];?>"></td>
    </tr>
    </table>

<!-- ENTRIES -->
<p>
<table id="entries_table" width="100%">
    <thead>
        <th width="40%">Description</th>
        <th width="10%">Q-ty</th>
        <th width="10%">Unit price</th>
        <th width="10%">Amount</th>
        <th width="30%">Action</th>
    </thead>
    <tbody id="entries_table_body">
<?php
//Проверка ENTRIES
if ($proforma_exist=='1'){
    $query='SELECT * FROM service_calc_entries WHERE entry_related_id="'.$id.'"';
    $result=$db->query($query);
    while ($row=$result->fetch_assoc()){
        echo'<tr>';
        switch ($row['entry_type']){
            case 0:
                ?>
                <td><input class='table_input' type="text" maxlength="250" name='entry_text[]' value="<?php echo $row['entry_text'];?>"></td>
                <td><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="<?php echo $row['entry_qty'];?>" maxlength="6" name='entry_qty[]'></td>
                <td><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $row['entry_price'];?>" maxlength="6" name='entry_price[]'></td>
                <td><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="<?php echo $row['entry_price']*$row['entry_qty'];?>"></td>
                <td>
                    <input type="hidden" name='entry_type[]' value='0'>
                    <input type="hidden" name='entry_base_id[]' value='0'>
                    <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
                    <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
                    <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
                    <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
                </td>
                <?php
                break;
            case 1:
                ?>
                <td><?php echo comboselect_rates($row['entry_text']);?></td>
                <td><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="<?php echo $row['entry_qty'];?>" maxlength="6" name='entry_qty[]'></td>
                <td><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $row['entry_price'];?>" maxlength="6" name='entry_price[]'></td>
                <td><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="<?php echo $row['entry_price']*$row['entry_qty'];?>"></td>
                <td>
                    <input type="hidden" name='entry_type[]' value='1'>
                    <input type="hidden" name='entry_base_id[]' value='0'>
                    <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
                    <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
                    <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
                    <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
                </td>
                <?php
                break;
            case 2:
                ?>
                <td><?php echo calc_selector_nmnc($row['entry_base_id'],$row['entry_text']); ?></td>
                <td><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="<?php echo $row['entry_qty'];?>" maxlength="6" name='entry_qty[]'></td>
                <td><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $row['entry_price'];?>" maxlength="6" name='entry_price[]'></td>
                <td><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="<?php echo $row['entry_price']*$row['entry_qty'];?>"></td>
                <td>
                    <input type="hidden" name='entry_type[]' value='2'>
                    <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
                    <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
                    <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
                    <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
                </td>
                <?php
                break;
            case 3:
                ?>
                <td colspan="4"><input class='table_input input_header' type="text" maxlength="250" name='entry_text[]' value="<?php echo $row['entry_text'];?>"></td>
                <td>
                    <input type='hidden' value='0' class="qty_input" name='entry_qty[]'>
                    <input type='hidden' value='0' class="price_input" name='entry_price[]'>
                    <input type='hidden' value='0' class="amount_input">
                    <input type="hidden" name='entry_type[]' value='3'>
                    <input type="hidden" name='entry_base_id[]' value='0'>
                    <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
                    <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
                    <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
                    <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
                </td>
                <?php
                break;
        }
        echo '</tr>';
    }
}    
?>
    </tbody>
</table>
<input type="button" value="Add line" onclick="entry_freeline(0)">
<input type="button" class='small_button' value="Add header" onclick="entry_header(0)">
<input type="button" class='small_button' value="Add rate" onclick="entry_rate(0)">
<input type="button" class='small_button' value="Add spare" onclick="entry_spare(0)">
<p>
<div style="text-align: right; width:100%;">
    <span><strong>Total:</strong></span>
    <input id="total" type='number' step="0.01" style="display:inline-block;min-width:150px; border:1px solid black" name="proforma_total" value="<?php echo $service['calc_total'];?>">
</div>
<input type="submit" value="Save" <?php if($saved==='1') echo 'style="background:greenyellow;"';?>>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="proforma_exist" value="<?php echo $proforma_exist; ?>">
<table width="100%" style="text-align: left">
    <tr><td><i>Note:</i></td></tr>
    <tr><td rowspan=""><textarea onchange="changed()" name="calc_note" rows="2" maxlength="1000" style="resize: none; width: 100%;"><?php echo $service['calc_note'];?></textarea></td></tr>
</table>
</form>
<!-- ROWS_FOR_INSERT-->
<table style='display: none'>
    <tr id='entry_freeline_tr'>
        <td><input class='table_input' type="text" maxlength="250" name='entry_text[]'></td>
        <td><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
        <td><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" maxlength="6" name='entry_price[]'></td>
        <td><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6"></td>
        <td>
            <input type="hidden" name='entry_type[]' value='0'>
            <input type="hidden" name='entry_base_id[]' value='0'>
            <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
            <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
            <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
            <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
        </td>
    </tr>
    <tr id='entry_header_tr'>
        <td colspan="4"><input class='table_input input_header' type="text" maxlength="250" name='entry_text[]'></td>
        <td>
            <input type='hidden' value='0' class="qty_input" name='entry_qty[]'>
            <input type='hidden' value='0' class="price_input" name='entry_price[]'>
            <input type='hidden' value='0' class="amount_input">
            <input type="hidden" name='entry_type[]' value='3'>
            <input type="hidden" name='entry_base_id[]' value='0'>
            <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
            <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
            <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
            <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
        </td>
    </tr>
    <tr id='entry_rate_tr'>
        <td><?php echo comboselect_rates('');?></td>
        <td><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
        <td><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" maxlength="6" name='entry_price[]'></td>
        <td><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6"></td>
        <td>
            <input type="hidden" name='entry_type[]' value='1'>
            <input type="hidden" name='entry_base_id[]' value='0'>
            <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
            <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
            <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
            <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
        </td>
    </tr>
    <tr id='entry_spare_tr'>
        <td><?php echo calc_selector_nmnc(); ?></td>
        <td><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
        <td><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" maxlength="6" name='entry_price[]'></td>
        <td><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6"></td>
        <td>
            <input type="hidden" name='entry_type[]' value='2'>
            <input type="button" class='small_button' value="Line" onclick="entry_freeline(this)">
            <input type="button" class='small_button' value="Header" onclick="entry_header(this)">
            <input type="button" class='small_button' value="Rate" onclick="entry_rate(this)">
            <input type="button" class='small_button red_button' value="Delete" onclick="delete_row(this)">
        </td>
    </tr>
</table>
<!-- END ROWS_FOR_INSERT-->
</div>
</div>
<div style="display:none">
    <?php 
    echo select_stock_class();
    echo select_service_rate();
    ?>
</div>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_service_proforma.js"></script>
<script>
document.addEventListener("DOMContentLoaded", rates_currency_filter());
var d=document.getElementsByName('new_customer');
d[0].removeAttribute('onchange');
d[0].onchange=changed;
function invoice_name(elem){
    if (elem.checked){
        document.getElementById('invoice_num').disabled=true;
    }
    else{
        document.getElementById('invoice_num').disabled=false;
    }
}
function entry_freeline(elem){
    let t_body = document.getElementById('entries_table_body');
    let line = document.getElementById('entry_freeline_tr').cloneNode(true);
    line.removeAttribute('ID');
    if (elem == 0) {
        t_body.appendChild(line);
        return;
    }
    let tr=elem.closest('TR');
    insertAfter(line,tr);
}
function entry_header(elem){
    let t_body = document.getElementById('entries_table_body');
    let line = document.getElementById('entry_header_tr').cloneNode(true);
    line.removeAttribute('ID');
    if (elem == 0) {
        t_body.appendChild(line);
        return;
    }
    let tr=elem.closest('TR');
    insertAfter(line,tr);
}
function entry_rate(elem){
    let t_body = document.getElementById('entries_table_body');
    let line = document.getElementById('entry_rate_tr').cloneNode(true);
    line.removeAttribute('ID');
    if (elem == 0) {
        t_body.appendChild(line);
        return;
    }
    let tr=elem.closest('TR');
    insertAfter(line,tr);
}
function entry_spare(elem){
    let t_body = document.getElementById('entries_table_body');
    let line = document.getElementById('entry_spare_tr').cloneNode(true);
    line.removeAttribute('ID');
    if (elem == 0) {
        t_body.appendChild(line);
        return;
    }
    let tr=elem.closest('TR');
    insertAfter(line,tr);
}
</script>