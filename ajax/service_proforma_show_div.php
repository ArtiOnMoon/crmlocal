<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
$id=clean($_POST['id']);
$query='SELECT service_proforma_rates.*, service_rates.rate_name FROM service_proforma_rates, service_rates WHERE spr_rate_id=rate_id AND spr_proforma_id="'.$id.'"';
$db=db_connect();
$result=$db->query($query);
if ($result-> num_rows!==0){
    echo'Service rates:<table width="100%">',
        '<thead><th>Rate</th><th>Q-ty</th><th>Price</th><th>Amount</th></thead>';
    
}
while ($row=$result->fetch_assoc()){
    echo '<tr><td>',$row['rate_name'],'</td><td>',
    $row['spr_rate_qnt'],'</td><td>',
    $row['spr_rate_price'],'</td><td>',
    $row['spr_rate_qnt']*$row['spr_rate_price'],'</td></tr>';
}
echo '</table><br>';

$query='SELECT service_proforma_items.*, stock_cats.stock_cat_name FROM service_proforma_items LEFT JOIN stock_cats ON spi_type=stock_cats.id WHERE spi_proforma_id="'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==0){
    echo'Spare parts and materials:<table width="100%">',
        '<thead><th>Category</th><th>P/N</th><th>Description</th><th>Q-ty</th><th>price</th><th>Amount</th></thead>';
}
while ($row=$result->fetch_assoc()){
    echo '<tr><td>',$row['stock_cat_name'],'</td><td>',
    $row['spi_pn'],'</td><td>',
    $row['spi_descr'],'</td><td>',
    $row['spi_qnt'],'</td><td>',
    $row['spi_price'],'</td><td>',
    $row['spi_qnt']*$row['spi_price'],'</td></tr>';
}
echo '</table>';