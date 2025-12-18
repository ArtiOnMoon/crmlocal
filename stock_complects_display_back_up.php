<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
session_start();
if(check_access('acl_stock', 1)) exit('Access denied.');

$compl_cat=clean($_POST['compl_cat']);
$manufacturer=clean($_POST['manufacturer']);
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$keyword=clean($_POST['keyword']);

if ($manufacturer=='All')$manufacturer='';

$limit=20;
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;

$db =  db_connect();
$query='SELECT stock_id,stnmc_manuf,stnmc_type,stnmc_pn,stnmc_type_model,stnmc_descr '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'WHERE stock_new.stock_deleted=0 AND stock_is_compl=1';
$condition='';
if ($compl_cat!=='All') $condition.=' AND stnmc_type="'.$compl_cat.'"';
if ($manufacturer!='') $condition.=' AND stnmc_manuf="'.$manufacturer.'"';
if ($keyword!='') $condition.=' AND (stnmc_pn LIKE ("%'.$keyword.'%") OR stnmc_type_model LIKE ("%'.$keyword.'%") OR stnmc_descr LIKE ("%'.$keyword.'%"))';
$query.=$condition;
$result2=$db->query($query);
$num = $result2->num_rows;
if ($num<=0) exit('No results');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;
//$_SESSION['stock_page']=$page; //номер страницы в переменную сессии

$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

//SORT
$sort = ' ORDER BY stock_id DESC ';
if ($sort_field!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
//
 $query='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name,'
        . 'stockl_name, stock_cat_name, curr_name, our_companies.id, our_companies.our_name,sales_id '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN currency ON stock_currency=curr_id '
        . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
        . 'LEFT JOIN sales ON sales_no=stock_so AND sales_our_comp=stock_so_comp '
        . 'WHERE stock_new.stock_deleted=0 AND stock_is_compl=1';
$query.=$condition.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$complects=$db->query($query);
if ($complects->num_rows===0) exit('No results');
?>
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_nmnc_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_nmnc_table('.($next_page).')" value="Next page"></span>';
echo '<span style="float:right">Records:<span id="rec1">'.$num.'</span>&nbsp</span>';
?>
<form action="stock_multi_edit.php" method="POST" target='_blank'>
<div id="table_wrap">
    <table id="stock_table" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <thead onclick="table_sort(event,'stock')">
        <th width="50" keyword="complect_id" <?php sort_class('complect_id',$sort_field,$sort_type);?>>Complect</th>
        <th width="50" keyword="stock_id" <?php sort_class('stock_id',$sort_field,$sort_type);?>>ID</th>
        <th width="75" keyword="stock_stat_name" <?php sort_class('stock_stat_name',$sort_field,$sort_type);?>>Status</th>
        <th width="100" keyword="stock_cat_name" <?php sort_class('stock_cat_name',$sort_field,$sort_type);?>>Category</th>
        <th width="150" keyword="mnf_short_name" <?php sort_class('mnf_short_name',$sort_field,$sort_type);?>>Maker</th>
        <th width="100" keyword="stnmc_pn" <?php sort_class('stnmc_pn',$sort_field,$sort_type);?>>P/N or type</th>
        <th width="75" keyword="stnmc_type_model" <?php sort_class('stnmc_type_model',$sort_field,$sort_type);?>>Type\model</th>
        <th keyword="stnmc_descr" <?php sort_class('stnmc_descr',$sort_field,$sort_type);?>>Description</th>
        <th width="100" keyword="stock_serial" <?php sort_class('stock_serial',$sort_field,$sort_type);?>>Serial</th>
        <th width="75" keyword="stockl_name" <?php sort_class('stockl_name',$sort_field,$sort_type);?>>Stock</th>
        <th width="25" keyword="stock_place" <?php sort_class('stock_place',$sort_field,$sort_type);?>>Place</th>
        <th width="25" keyword="stock_condition" <?php sort_class('stock_condition',$sort_field,$sort_type);?>>Condition</th>
        <th width="50" keyword="our_name" <?php sort_class('our_name',$sort_field,$sort_type);?>>Owner</th>
        <th keyword="stock_note" <?php sort_class('stock_note',$sort_field,$sort_type);?>>Note</th>
        <th width=50 keyword="stock_po" <?php sort_class('stock_po',$sort_field,$sort_type);?>>PO №</th>
        <th width=50 keyword="stock_so" <?php sort_class('stock_so',$sort_field,$sort_type);?>>SO №</th>
    </thead>
    <tbody>
<?php
if (!$complects->num_rows===0 || !$complects) {exit('<tr><td colspan="18">No results</td></tr></table>');}
while($complect = $complects->fetch_assoc()){
    //Вывод строки комплекта
    echo '<tr class="stock_complects_row">'
        .stock_view_link($complect['stock_id'],$complect['stock_compl_id'],$complect['stock_is_compl'])
        , '<td ',color_table($complect['stock_status'],$complect['stock_condition']),'>',$complect['stock_stat_name'],'</td>'
            , '<td>',$complect['stock_cat_name'],'</td>'
            , '<td>',$complect['mnf_short_name'],'</td>'
            , '<td>',$complect['stnmc_pn'],'</td>'
            , '<td>',$complect['stnmc_type_model'],'</td>'
            , long_td_string($complect['stnmc_descr'],60)
            , '<td>',$complect['stock_serial'],'</td>'
            , '<td>',view_stocklist_item($complect['stock_stock_id'], $complect['stockl_name']).'</td>'
            , '<td>',$complect['stock_place'],'</td>'
            ,condition_decode($complect['stock_condition'])
            , '<td>', view_our_company_link($complect['id'], $complect['our_name']),'</td>'
            , long_td_string($complect['stock_note'],20)
            //, '<td>',$row['stock_price'],'</td>'
            //, '<td>',$row['curr_name'],'</td>'
            , '<td>',stock_view_po($complect['po_id'],$complect['stock_po_comp'],$complect['stock_po']),'</td>'
            , '<td>',stock_view_sales($complect['sales_id'],$row['stock_so_comp'],$row['stock_so']).'</td>'
        . '</tr>';
    //Вывод содержимого комплекта
    $query='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name,'
        . 'stockl_name, stock_cat_name, curr_name, our_companies.id, our_companies.our_name,sales_id '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN currency ON stock_currency=curr_id '
        . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
        . 'LEFT JOIN sales ON sales_no=stock_so AND sales_our_comp=stock_so_comp '
        . 'WHERE stock_new.stock_deleted=0 AND stock_compl_id='.$complect['stock_id'];
    $result=$db->query($query);
    if (!$result->num_rows===0 || !$result) continue;
    while($row = $result->fetch_assoc()){
        echo '<tr>';
        echo '<td></td><td><a href="#" onclick="stock_edit(',(int)$row['stock_id'],')">',$row['stock_id'],'</td>'
            , '<td ',color_table($row['stock_status'],$row['stock_condition']),'>',$row['stock_stat_name'],'</td>'
            , '<td>',$row['stock_cat_name'],'</td>'
            , '<td>',$row['mnf_short_name'],'</td>'
            , '<td>',$row['stnmc_pn'],'</td>'
            , '<td>',$row['stnmc_type_model'],'</td>'
            , long_td_string($row['stnmc_descr'],60)
            , '<td>',$row['stock_serial'],'</td>'
            , '<td>',view_stocklist_item($row['stock_stock_id'], $row['stockl_name']).'</td>'
            , '<td>',$row['stock_place'],'</td>'
            , condition_decode($row['stock_condition'])
            , '<td>', view_our_company_link($row['id'], $row['our_name']),'</td>'
            , long_td_string($row['stock_note'],20)
            //, '<td>',$row['stock_price'],'</td>'
            //, '<td>',$row['curr_name'],'</td>'
            , '<td>',stock_view_po($row['po_id'],$row['stock_po_comp'],$row['stock_po']),'</td>'
            , '<td>',stock_view_sales($row['sales_id'],$row['stock_so_comp'],$row['stock_so']).'</td>'
            , '</tr>';
    }
}
?>
    </tbody></table>

</div>
<!--<input type="submit" value="Edit selected">-->
<input type="button" value="Edit selected" onclick="stock_edit2()">
</form>