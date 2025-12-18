<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
startSession();
security ();
if(check_access('acl_invoices', 1)) exit('Access denied.');
do_page_header('Calculation','Service');
$id=clean($_GET['service_id']);
$saved=isset($_GET['saved']);
$db =  db_connect();

//Проверка существования проформы
$proforma_exist=1;
$query= 'select service_proforma.*, service.service_no from service_proforma, service where service.service_id=service_proforma.proforma_id AND proforma_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1){
    //Проформа не найдена,проверка в сервисе
    $proforma_exist=0;
    $query= 'select * from service where service_id = "'.$id.'"';
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Service order not found');
    }
}
$service=$result->fetch_assoc();
?>
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
<form method="POST" action="/scripts/service_proforma_save.php" onsubmit="save_proforma()">
<table width="100%" style="text-align: left">
    <tr>
        <td><strong>Customer</strong></td>
        <td><?php echo select_customer($service['comp_id']);?></td>
        <td><strong>Our company</strong></td>
        <td><?php echo select_our_company($service['proforma_our_company'], 'name="our_company" required', 0);?></td>
    </tr>
    <tr>
        <td><strong>Customer PO</strong></td>
        <td><input type='text' onchange="changed()" name="po" value="<?php echo $service['PO'];?>"></td>
        <td><strong>Currency</strong></td>
        <td><?php echo select_currency2(get_currency_list(),$service['currency'], 'name="currency" id="currency_select" onchange="rates_currency_filter()"');?></td>
    </tr>
    <tr>
        <td><strong>Service order</strong></td>
        <td><?php echo view_order_link($id, $service['service_no']);?></td>
        <td><strong>Payment terms</strong></td>
        <td><input type="text" onchange="changed()" name="pay_terms"  maxlength="300" value="<?php echo $service['pay_terms'];?>"></td>
    </tr>
    </table>

<!-- RATES -->
<p>
<strong>Rates:</strong>
</p>
<table id="rates_table" width="100%">
    <thead>
        <th>Description</th>
        <th width="50px">Q-ty</th>
        <th width="50px">Unit price</th>
        <th width="50px">Amount</th>
        <th>Action</th>
    </thead>
    <tbody>
        <?php // Вставка RATES
        if ($proforma_exist===1){
        $rates=$db->query('select * from service_proforma_rates where spr_proforma_id="'.$id.'"');
        if ($rates->num_rows > 0){
            while($rate=$rates->fetch_assoc()){
                echo'<tr><td>',
                    select_service_rate($rate['spr_rate_id'],1 ),'</td><td>',
                    '<input size=10 maxlength=8 type="text" required onchange="calculate(this)" value="'.$rate['spr_rate_qnt'].'"></td><td>',
                    '<input size=10 maxlength=10 type="text" required onchange="calculate(this)" value="'.$rate['spr_rate_price'].'"></td><td>',
                    '<input size=10 type="text" required readonly value="'.$rate['spr_rate_qnt']*$rate['spr_rate_price'].'"></td><td>',
                    '<input type="button" onclick="delete_row(this)" value="Delete"></td></tr>';
            }
        }
        }
        ?>
    </tbody>
</table>
<input type="button" value="Add item" onclick="add_rate()">
<!-- SPAREPARTS AND MATERIALS -->
<p>
<strong>Spare parts and materials:</strong>
</p>
<table id="spares_table" width="100%">
    <thead>
        <th width="100px">Type</th>
        <th width="150px">Part number</th>
        <th width="300px">Description</th>
        <th width="100px">Q-ty</th>
        <th width="100px">Unit price</th>
        <th width="100px">Amount</th>
        <th width="100px">Action</th>
    </thead>
    <tbody>
        <?php // Вставка SPARE
        if ($proforma_exist===1){
            $spares=$db->query('select * from service_proforma_items where spi_proforma_id="'.$id.'"');
            if ($spares->num_rows > 0){
                while($spare=$spares->fetch_assoc()){
                    echo'<tr><td>'
                    ,select_stock_class($spare['spi_type']).'</td><td>'
                    ,'<input type="text" required onkeyup="live_search(this)" style="width:100%" value="'.$spare['spi_pn'].'">'
                    ,'<div class="search"></div></td><td>'
                    ,'<input type="text" required style="width:100%" onchange="calculate(this)" value="'.$spare['spi_descr'].'"></td><td>'
                    ,'<input type="text" required style="width:100%" onchange="calculate(this)" value="'.$spare['spi_qnt'].'"></td><td>'
                    ,'<input type="text" required style="width:100%" onchange="calculate(this)" value="'.$spare['spi_price'].'"></td><td>'
                    ,'<input type="text" required style="width:100%" readonly value="'.$spare['spi_price']*$spare['spi_qnt'].'"></td><td>'
                    ,'<input type="button" onclick="delete_row(this)" value="Delete"></td>';
                    echo '</tr>';
                }
                
            }
        }
        ?>
    </tbody>
</table>
<input type="button" value="Add" onclick="add_spare()">
<p>
<div style="text-align: right; width:100%;">
    <span><strong>Total:</strong></span>
    <span id="total" style="display:inline-block;min-width:150px; border:1px solid black"><?php echo $service['proforma_total']; ?></span>
    <input type="hidden" id="total_to_send" name="proforma_total" value="<?php echo $service['proforma_total'];?>">
</div>
<input type="submit" value="Save" <?php if($saved==='1') echo 'style="background:greenyellow;"';?>>
<input type="hidden" id="rates" name="rates">
<input type="hidden" id="spare" name="spare">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="proforma_exist" value="<?php echo $proforma_exist; ?>">
<p>
<table width="100%" style="text-align: left">
    <tr><td><i>Note:</i></td></tr>
    <tr><td rowspan=""><textarea onchange="changed()" name="proforma_note" rows="2" maxlength="1000" style="resize: none; width: 100%;"><?php echo $service['proforma_note'];?></textarea></td></tr>
</table>
</form>
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
</script>