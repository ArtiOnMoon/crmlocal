<?php
require_once '../functions/db.php';
require_once '../functions/stock_fns.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';

$stock_class=clean($_POST['stock_class']);
$stock_maker=clean($_POST['stock_maker']);
$stock_po=clean($_POST['stock_po']);
$stock_pn=clean($_POST['stock_pn']);

$condition='';
if ($stock_class!=='All') $condition.=' and stnmc_type="'.$stock_class.'"';
//if (!empty($status)) $condition.=' and stock_status IN ('.implode(',',$status).')';
//if ($cond!='0') $condition.=' and stock_condition="'.$cond.'"';
//if ($stock!='All') $condition.=' and stock_stock_id="'.$stock.'"';
if ($stock_maker!=='All') $condition.=' and stnmc_manuf="'.$stock_maker.'"';
if ($stock_po!=='') $condition.=' and CONCAT_WS(".",stock_po_comp,stock_po) like ("%'.$stock_po.'%")';
if ($stock_pn!=='') $condition.=' and stnmc_pn like ("%'.$stock_pn.'%")';

$db =  db_connect();
$query='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name,'
        . 'stockl_id, stockl_name, stock_cat_name, curr_symb, our_companies.id, our_companies.our_name '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN currency ON stock_currency=curr_id '
        . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
        . 'WHERE stock_new.stock_deleted=0 ';
$query.=$condition.' LIMIT 100';
$result=$db->query($query);
?>
<div id="table_wrap">
    <table class="sort_table">
    <thead class="stock_thead" >
        <th width="10px"><input type='checkbox' id='main_checkbox' onchange='check_all_checkboxes(this)'></th>
        <th width="60px" colspan="2" keyword="stock_id">ID</th>
        <th>Status </th>
        <th>Category</th>
        <th>Maker</th>
        <th>P/N</th>
        <th>Type\model</th>
        <th>Description</th>
        <th>Serial</th>
        <th>Stock</th>
        <th>Place</th>
        <th>Condition</th>
        <th>Note</th>
        <th>PO №</th>
        <th>SO №</th>
    </thead>
    <tbody>
<?php
if ($result->num_rows===0 || !$result) {exit('<tr><td colspan="18">No results</td></tr></table>');}
while($row = $result->fetch_assoc()){
    echo '<tr class="',stock_tr_is_sold($row['stock_status']),'">';
    echo '<td><input type="checkbox" class="table_checkbox" value="',$row['stock_id'],'"></td>';
    echo stock_view_link($row['stock_id'],$row['stock_compl_id'],$row['stock_is_compl'])//,'</td>'
            , '<td ',color_table($row['stock_status'],$row['stock_condition']),'>',$row['stock_stat_name'],'</td>'
            , '<td>',$row['stock_cat_name'],'</td>'
            , '<td>',$row['mnf_short_name'],'</td>'
            , '<td>',$row['stnmc_pn'],'</td>'
            , '<td>',$row['stnmc_type_model'],'</td>'
            , long_td_string($row['stnmc_descr'],60)
            , '<td>',$row['stock_serial'],'</td>'
            , '<td>',$row['stockl_name'],'</td>'
            , '<td>',$row['stock_place'],'</td>'
            ,condition_decode($row['stock_condition'])
            , long_td_string($row['stock_note'],20)
            , '<td>',stock_view_po($row['stock_po_comp'],$row['stock_po'],$row['stock_po_type']),'</td>'
            , '<td>',stock_view_sales($row['stock_so_comp'],$row['stock_so'],$row['stock_so_type']).'</td>'
            , '</tr>';
}
?>
    </tbody></table>
</div>