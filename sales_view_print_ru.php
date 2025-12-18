<?php
require_once 'functions/fns.php';
require_once 'functions/sales_fns.php';
require_once 'PATHS.php';
require_once 'functions/selector.php';
require_once 'classes/Order_name_engine.php';

$sale_id=clean($_GET['id']);

if(check_access('acl_sales', 1)) exit('Access denied.');

$db =  db_connect();
$query= 'SELECT sales.*,customers.*, our_companies.*, c1.name AS our_country, c2.name AS cust_country, curr_name '
        . 'FROM sales '
        . 'LEFT JOIN customers ON sales_customer=cust_id '
        . 'LEFT JOIN our_companies ON sales_our_comp=id '
        . 'LEFT JOIN countries c1 ON our_companies.our_country=c1.id '
        . 'LEFT JOIN countries c2 ON customers.country=c2.id '
        . 'LEFT JOIN currency ON sales_currency=curr_id '
        . 'WHERE sales_id = "'.$sale_id.'"';
$result=$db->query($query);
if ($result-> num_rows==1){
    $row=$result->fetch_assoc();
    
    $on = new Order_name_engine();
    $on->init($db);
    $on->type = 'SL';
    $on->comp_id = $row['sales_our_comp'];
    $on->num = $row['sales_no'];
    $on->get_order();
    
    
    $query = 'SELECT * FROM customers';
    
    if ($row['sales_qte_date']=='') {
        $date = new DateTime($row['sales_date']);
        $date->modify('+3 days');
        $date=$date->format('Y-m-d');
    }
    else $date=$row['sales_qte_date'];
    
?>
<html>
<head>
    <title><?php echo $on->order.' - QTE'; ?></title>
    <link rel="stylesheet" type="text/css" href="css/print.css">
    <style type="text/css" media="print">
        button {display: none; }
    </style>
</head>
<div class="print_content">
<button onclick="window.print();" class="print_button">Печать</button>
<table class="print_table">
    <col width="30%"><col width="20%"><col width="50%">
    <tr class="border_bottom">
        <td><img class="print_logo" src="/public/<?php echo $row['our_logo']; ?>"></td>
        <td class="align_right" colspan="2"><strong class="head_text">Коммерческое предложение</strong></td>
    </tr>
    <tr></tr>
</table>
<br>
<table class="print_table3" width="100%">
    <col width="50%">
    <tr>
        <td><strong>От:</strong></td>
        <td><strong>КП №:</strong></td>
        <td><?php echo $on->order; ?></td>
    </tr>
    <tr>
        <td><?php echo $row['our_ru_name']; ?></td>
        <td><strong>Дата:</strong></td>
        <td><?php echo $row['sales_date']; ?></td>
    </tr>
    <tr>
        <td><?php echo $row['our_fact_addr']; ?></td>
        <td><strong>Действительно до:</strong></td>
        <td><?php echo $date;?></td>
    </tr>
    <tr>
        <td><?php echo $row['our_fact_addr2']; ?></td>
        <td><strong>Условия оплаты:</strong></td>
        <td><?php echo $row['sales_pay_terms']; ?></td>
    </tr>
    <tr style="min-height: 12px">
        <td></td>
    </tr>
    <tr>
        <td><strong>Кому:</strong></td>
        <td><strong>№ заявки:</strong></td>
        <td><?php echo $row['sales_request']; ?></td>
    </tr>
    <tr>
        <td><?php echo $row['cust_full_name']; ?></td>
        <td><strong>Условия поставки:</strong></td>
        <td><?php echo $row['sales_delevery_terms']; ?></td>
    </tr>
    <tr>
        <td><?php echo $row['InvoicingAddress2']; ?></td>
    </tr>
    <tr>
        <td><?php echo $row['InvoicingAddress']; ?></td>
    </tr>
</table>

<?php
} 
else {
    echo 'Nothing found';
    exit();
}
?>
<br><br>
    <div style="overflow:auto;">
        <table class="print_table2">
            <thead>
                <th width="10px">№</th>
                <th>Наименование</th>
                <th class="align_left">Срок поставки</th>
                <th class="align_left" width="40px">К-во</th>
                <th width="10%">Цена</th>
                <th width="10%">Сумма</th>
            </thead>
            <tbody>
<?php
//SALE CONTENT
$i=1;
$query= 'SELECT * FROM sales_content WHERE scont_sale_id = "'.$sale_id.'" and scont_qty>0';
if(!$result=$db->query($query))exit($db->error);
if($result->num_rows>0){
    while($sales_cont = $result->fetch_assoc()){
        $scont_delivery=($sales_cont['scont_delivery']=='')?'в наличии':$sales_cont['scont_delivery'];
        ?>
        <tr>
            <td><?php echo $i++;?></td>
            <td><?php echo $sales_cont['scont_text'];?></td>
            <td><?php echo $scont_delivery;?></td>
            <td class="align_center"><?php echo $sales_cont['scont_qty'];?></td>
            <td class="align_right"><?php echo number_format($sales_cont['scont_price']*$sales_cont['scont_currency_rate']*(1-$sales_cont['scont_discount']/100),2,',',' ');?></td>
            <td class="align_right"><?php echo number_format($sales_cont['scont_price']*$sales_cont['scont_currency_rate']*$sales_cont['scont_qty']*(1-$sales_cont['scont_discount']/100),2,',',' ');?></td>
        </tr>
<?php
    }
}
?>
        </tbody>
        <tr>
            <td colspan="3" style="border:0;"></td>
            <td colspan="2" style="text-align: right; border:0;"><b>Итого: </b></td><td class="align_right"><strong><?php echo number_format($row['sales_total'],2,',',' ');?></strong></td>
        </tr>
        <tr>
            <td colspan="3" style="border:0;"></td>
            <td colspan="2" style="text-align: right; border:0;"><b>Валюта: </b></td><td class="align_right"><?php echo $row['curr_name'];?></td>
        </tr>
        <tr>
            <td colspan="3" style="border:0;"></td>
            <td colspan="2" style="text-align: right; border:0;"><b>В т.ч. НДС: </b></td><td class="align_right"><?php echo number_format( $row['sales_total_vat'],2,',',' ');?></td>
        </tr>
        </table>
    </div>
    <?php
    if ($row['sales_qte_note']!==''){
        echo '<b>Примечание:</b>';
        echo '<div style="font-style:italic">',nl2br($row['sales_qte_note']),'</div>';
    }
    ?>
    <div class="print_footer">
        <?php echo $row['qte_footer_ru'];?>
    </div>
</div>
</body>
</html>   