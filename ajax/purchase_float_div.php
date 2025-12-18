<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$id=clean($_POST['id']);
$query= 'SELECT purchase_content.* FROM purchase_content '
        . 'WHERE po_con_po_id = "'.$id.'"';
$db =  db_connect();
if(!$result=$db->query($query))exit($db->error);
if($result->num_rows>0){
    ?>
        <table width="100%" id="sale_content" border="1px" cellspacing = "0" cellpadding="2px">
            <thead><th>№</th><th>Descr</th><th>Q-ty</th><th>Price</th><th>Amount</th></thead>
            <tbody>
    <?php
    $sales_query='SELECT curr_symb FROM purchase LEFT JOIN currency ON curr_id=po_currency WHERE po_id = "'.$id.'"';
    if(!$sales_currency=$db->query($sales_query))exit($db->error);
    $currency=$sales_currency->fetch_assoc();
    $total=0;
    $i=1;
    while($row = $result->fetch_assoc()){
        $amount=$row['po_con_price']*$row['po_con_qty']*(1-$row['po_con_discount']/100);
        $cfm_amount=$row['po_con_price']*$row['scont_cfm_qty']*(1-$row['po_con_discount']/100);
        $total+=$amount;
        echo '<tr><td>',$i++,'</td>',
            '<td>'.$row['po_con_text'],'</td>',
            '<td>',$row['po_con_qty'],'</td>',
            '<td>',number_format($row['po_con_price']*(1-$sales_cont['po_con_discount']/100),2,',',' '),'</td>',
            '<td>',number_format($amount,2,',',' '),'</td>',
            '</tr>';
    }
    echo '</tbody></table>';
    echo '<div style="position:relative;float:right;">
            Total: <input type="text" size="10" readonly style="border:2px solid black;" value="',$currency['curr_symb'],' ',number_format($total,2,',',' '),'">
            </div>';
}
else exit('empty');