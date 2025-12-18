<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$id=clean($_POST['id']);
$query= 'SELECT sales_content.*, currency.curr_name FROM sales_content '
        . 'LEFT JOIN currency ON scont_currency=curr_id '
        . 'WHERE scont_sale_id = "'.$id.'"';
$db =  db_connect();
if(!$result=$db->query($query))exit($db->error);
if($result->num_rows>0){
    ?>
        <table width="100%" id="sale_content" border="1px" cellspacing = "0" cellpadding="2px">
            <thead><th>№</th><th>Descr</th><th>Q-ty</th><th>Cfm.</th><th>Price</th><th>Currency</th><th>VAT</th><th>Rate</th><th>Amount</th><th>Amount cfm.</th></thead>
            <tbody>
    <?php
    $sales_query='SELECT curr_symb FROM sales LEFT JOIN currency ON curr_id=sales_currency WHERE sales_id = "'.$id.'"';
    if(!$sales_currency=$db->query($sales_query))exit($db->error);
    $currency=$sales_currency->fetch_assoc();
    $total=0;
    $i=1;
    while($row = $result->fetch_assoc()){
        $amount=$row['scont_price']*$row['scont_currency_rate']*$row['scont_qty']*(1-$row['scont_discount']/100);
        $cfm_amount=$row['scont_price']*$row['scont_currency_rate']*$row['scont_cfm_qty']*(1-$row['scont_discount']/100);
        $total+=$amount;
        $total_cfm+=$cfm_amount;
        echo '<tr><td>',$i++,'</td>',
            '<td>'.$row['scont_text'],'</td>',
            '<td>',$row['scont_qty'],'</td>',
            '<td class="cfm_field">',$row['scont_cfm_qty'],'</td>',
            '<td>',number_format($row['scont_price']*(1-$sales_cont['scont_discount']/100),2,',',' '),'</td>',
            '<td>',$row['curr_name'],'</td>',
            '<td>',$row['scont_vat'],'</td>',
            '<td>',number_format($row['scont_currency_rate'],4,',',' '),'</td>',
            '<td>',number_format($amount,2,',',' '),'</td>',
            '<td class="cfm_field">',number_format($cfm_amount,2,',',' '),'</td>',
            '</tr>';
    }
    echo '</tbody></table>';
    echo '<div style="position:relative;float:right;">
            Total: <input type="text" size="10" readonly style="border:2px solid black;" value="',$currency['curr_symb'],' ',number_format($total,2,',',' '),'">
            Total cfm.: <input class="cfm_field" type="text" size="10" readonly style="border:2px solid black;" value="',$currency['curr_symb'],' ',number_format($total_cfm,2,',',' '),'">
            </div>';
}
else exit('empty');