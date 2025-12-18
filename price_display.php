<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/invoice_func.php';
startSession();
security();
if(check_access('acl_stock', 1)) exit('Access denied.');
$limit=50;
if (isset($_POST['page'])) $page=$_POST['page'];
if (!is_numeric($page)) $page=1;
if ($_POST['filter']!=='All') $filter.=' and description like "%'.$_POST['filter'].'%"';
else $filter='';
if ($_POST['class']!=='All') $class.=' and class="'.$_POST['class'].'"';
else $class='';
$db =  db_connect();
$result2=$db->query('select id from price where price_deleted=0'.$class.$filter);
$num = $result2->num_rows;
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;        
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

$query= 'select price.*, stock_cats.stock_cat_name from price LEFT JOIN customers ON manufacturer_id=cust_id left join stock_cats ON class=stock_cats.id WHERE price_deleted=0';
$query=$query.$class.$filter.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);

echo '<br>Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_price_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_price_table('.($next_page).')" value="Next page"></span>';
//echo '<input type="button" onclick="go_to()" value="Go to"><input id="go_to" type="text" value="" size="3"><br>';
//
//Создание таблицы
echo '<table id="price_table" class="sortable" width="100%" rules="columns"  border="1px" cellspacing = "0" cellpadding="2px">'
. '<thead><tr>'
. '<th axis="num" width="70">ID</th>'
. '<th width="100">Class</th>'
. '<th width="100">Part number</th>'
. '<th>Description</th>'
. '<th width="150">Manufacturer</th>'
. '<th width="100">Currency</th>'
. '<th width="50">Price</th>'       
. '<th width="50">Discount %</th>'
. '<th width="50">Net</th>'
. '<th width="50">Date</th>'
. '</thead><tbody>';
while($row = $result->fetch_assoc()){
    $net=($row['price']/100)*(100-$row['price_discount']);
    echo '<tr>'
    , '<td class="num">'.view_price_link($row['id'], $row['id'])
    , '<td>'.$row['stock_cat_name']
    , '<td>'.$row['pn']
    , '<td>'.$row['description']
    , '<td>'.view_company_link($row['cust_short_name'],$row['cust_id'])
    , '<td>'.$row['currency']
    , '<td>'.$row['price']
    , '<td>'.$row['price_discount']
    , '<td><b>'.$net
    , '</b><td>'.$row['date']
    , '</tr>';
}
echo '</tbody></table>';
