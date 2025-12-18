<?php
    require_once '../functions/main.php';
    require_once '../functions/db.php'; 
    require_once '../functions/auth.php';
    if(check_access('acl_sales', 1)) exit('Access denied.');
    $data = clean($_POST["data"]);
    $db =  db_connect();
    $query= 'SELECT mnf_short_name,stock_cat_name, stnmc_pn, stnmc_descr, stnmc_type_model, stnmc_id, stnmc_price, stnmc_discount,stnmc_curr,'
            . '(SELECT count(stock_id) FROM stock_new WHERE stock_nmnc_id=stnmc_id AND stock_status=1) as count_left '
            . 'FROM stock_nmnc '
            . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
            . 'LEFT JOIN manufacturers ON mnf_id=stnmc_manuf '
            . 'WHERE stnmc_deleted=0 AND (stnmc_descr LIKE("%'.$data.'%") OR stnmc_type_model LIKE("%'.$data.'%") OR stnmc_pn LIKE("%'.$data.'%") '
            . 'OR CONCAT_WS(" ",stnmc_pn,stnmc_descr,stnmc_type_model) LIKE("%'.$data.'%")) limit 100';
    $result=$db->query($query);
    if($result->num_rows===0) exit ('Nothing found');
    while ($row = $result->fetch_assoc()) {
        echo '<div class="selector_result_div row_white" data-id="'.$row['stnmc_id'].'" data-value="'.trim($row['stnmc_pn'].' '.$row['stnmc_descr'].' '.$row['stnmc_type_model']).'" '
        , 'data-price="'.$row['stnmc_price'].'" data-discount="'.$row['stnmc_discount'].'" data-currency="'.$row['stnmc_curr'].'">'
        ,'<strong>'.$row['stock_cat_name'].'| '.$row['mnf_short_name'].'</strong> | '
        , $row['stnmc_pn'],' ',$row['stnmc_descr'],' ',$row['stnmc_type_model']
        , '<br><i>Avaliable on stock:<strong>',$row['count_left'],'</strong></i>'
        , '</div>';
    }