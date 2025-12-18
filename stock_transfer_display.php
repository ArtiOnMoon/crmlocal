<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
session_start();
if(check_access('acl_stock', 1)) exit('Access denied.');
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$our_companies_list= get_our_companies_list(1);

$from_stock=clean($_POST['from_stock']);
$to_stock=clean($_POST['to_stock']);
//DATE
$ship_date_start=clean($_POST['ship_date_start']);
$ship_date_end=clean($_POST['ship_date_end']);
$receipt_date_start=clean($_POST['receipt_date_start']);
$receipt_date_end=clean($_POST['receipt_date_end']);
$sold=clean($_POST['sold']);

$cond='';
if($from_stock!='All') $cond.=' AND from_stock="'.$from_stock.'"';
if($to_stock!='All') $cond.=' AND to_stock="'.$to_stock.'"';
if ($ship_date_start!=='') $cond.=' and ship_date >= ("'.$ship_date_start.'")';
if ($ship_date_end!=='') $cond.=' and ship_date <= ("'.$ship_date_end.'")';
if ($receipt_date_start!=='') $cond.=' and receipt_date >= ("'.$receipt_date_start.'")';
if ($receipt_date_end!=='') $cond.=' and receipt_date <= ("'.$receipt_date_end.'")';
if ($sold!=='2') $cond.=' and stock_officialy_sold="'.$sold.'"';

//SEARCH
$search_field=clean($_POST['search']);
$search='';
if ($search_field!=='')$search=' AND (stnmc_pn LIKE ("%'.$search_field.'%") '
        . 'OR stnmc_descr LIKE ("%'.$search_field.'%") '
        . 'OR stock_note LIKE ("%'.$search_field.'%") '
        . 'OR stock_serial LIKE ("%'.$search_field.'%") '
        . 'OR stnmc_type_model LIKE ("%'.$search_field.'%")) ';
//END SEARCH

$limit=100;
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;
$db =  db_connect();
$query='SELECT transfer_id '
        . 'FROM stock_transfer '
        . 'LEFT JOIN stock_new ON stock_transfer.stock_id=stock_new.stock_id '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'WHERE is_deleted=0';
$query.=$cond.$search;
if (!$result2=$db->query($query)) {
//    echo $db->error;
    echo $query;
}
$num = $result2->num_rows;
$pages = ceil($num/$limit);

$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;
//SORT
$sort = ' ORDER BY transfer_id DESC';
if ($sort_field!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
$query='SELECT stock_transfer.*,stock_compl_id,stock_is_compl,stock_status,stock_stat_name,stock_condition,stock_cat_name,mnf_short_name,stnmc_pn,stnmc_type_model,stnmc_descr,stock_serial,stock_officialy_sold, '
        . 'sl1.stockl_name as from_stock, sl2.stockl_name as to_stock FROM stock_transfer '
        . 'LEFT JOIN stock_list sl1 ON sl1.stockl_id=from_stock '
        . 'LEFT JOIN stock_list sl2 ON sl2.stockl_id=to_stock '
        . 'LEFT JOIN stock_new ON stock_transfer.stock_id=stock_new.stock_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'WHERE is_deleted=0';
$query.=$cond.$search.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
if(!$result=$db->query($query)){
    echo $db->error;
}
if ($result->num_rows===0) exit('No results');
?>
<div id="main_subheader">
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_transfer_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_transfer_table('.($next_page).')" value="Next page"></span>';
echo '<span style="float:right">Records:<span id="rec1">'.$num.'</span>&nbsp</span>';
//echo '<input type="button" onclick="go_to()" value="Go to"><input id="go_to" type="text" value="" size="3"><br>';
?>
</div>
<div id="main_subbody">
<table id="stock_table" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <thead>
        <th width="10px"><input type='checkbox' id='main_checkbox' onchange='check_all_checkboxes(this)'></th>
        <th colspan="2">ID</th>
        <th width="80" keyword="stock_stat_name" <?php sort_class('stock_stat_name',$sort_field,$sort_type);?>>Status</th>
        <th width="75" keyword="stock_cat_name" <?php sort_class('stock_cat_name',$sort_field,$sort_type);?>>Category</th>
        <th width="80" keyword="mnf_short_name" <?php sort_class('mnf_short_name',$sort_field,$sort_type);?>>Maker</th>
        <th width="120" keyword="stnmc_pn" <?php sort_class('stnmc_pn',$sort_field,$sort_type);?>>P/N or type</th>
        <th width="120" keyword="stnmc_type_model" <?php sort_class('stnmc_type_model',$sort_field,$sort_type);?>>Type\model</th>
        <th keyword="stnmc_descr" <?php sort_class('stnmc_descr',$sort_field,$sort_type);?>>Description</th>
        <th width="125" keyword="stock_serial" <?php sort_class('stock_serial',$sort_field,$sort_type);?>>Serial</th>
        <th width="80">From stock</th>
        <th width="80">To stock</th>
        <th width="80">Ship date</th>
        <th width="80">Receipt date</th>
        <th>AWB</th>
        <th>Shipped on</th>
        <th>Note</th>
        <th></th>
        <th>Edit</th>
    </thead>
<?php
while ($row=$result->fetch_assoc()){
    if ($row['stock_officialy_sold']=='1')$sold='x';
    else $sold='';
    echo'<tr>';
    echo '<td><input type="checkbox" class="table_checkbox" name="edit[]" value="',$row['stock_id'],'"></td>'
        , stock_view_link($row['stock_id'],$row['stock_compl_id'],$row['stock_is_compl'])
        , '<td ',color_table($row['stock_status'],$row['stock_condition']),'>',$row['stock_stat_name'],'</td>'
        , '<td>',$row['stock_cat_name'],'</td>'
        , '<td>',$row['mnf_short_name'],'</td>'
        , '<td>',$row['stnmc_pn'],'</td>'
        , '<td>',$row['stnmc_type_model'],'</td>'
        , long_td_string($row['stnmc_descr'],40)
        , '<td>',$row['stock_serial'],'</td>'
        . '<td>',$row['from_stock'],'</td>'
        . '<td>',$row['to_stock'],'</td>'
        . '<td>',$row['ship_date'],'</td>'
        . '<td>',$row['receipt_date'],'</td>'
        . '<td>',$row['awb'],'</td>'
        . '<td>',$row['shipped_on'],'</td>'
        . '<td>',$row['note'],'</td>'
        . '<td>',$sold,'</td>'
        . '<td><a href="#" onclick="stock_transfer_edit(\'',$row['transfer_id'],'\')">Edit</a></td>'
        . '</tr>';
}
?>
</tbody></table>
</div>
<div id="main_subfooter">
    <input type="button" value="Edit selected" onclick="stock_edit2()">
</div>