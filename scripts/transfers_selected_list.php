<?php
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/main.php';
require_once '../functions/stock_fns.php';
require_once '../functions/selector.php';
startSession();
security ();

//echo 'existed_id'.$_POST['existed_id'];
$stock_items=json_decode($_POST['list']);
$existed=json_decode($_POST['list_of_existed']);
$existed_id=$_POST['existed_id'];
$existed_date=json_decode($_POST['list_of_existed_date']);
$db =  db_connect();
$query='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name, stock_cat_name '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id ';
if (count($existed)>0){
    $query.='WHERE stock_new.stock_deleted=0 AND (stock_new.stock_id IN ('.implode(',',$existed).') OR stock_new.stock_id IN ('.implode(',',$stock_items).'))';
} else {
    $query.='WHERE stock_new.stock_deleted=0 AND stock_new.stock_id IN ('.implode(',',$stock_items).')';
}
$result=$db->query($query);
?>
<table class="sort_table" width="100%">
    <thead class="stock_thead" >
        <th colspan="2">ID</th>
        <th>Status </th>
        <th>Category</th>
        <th>Maker</th>
        <th>P/N</th>
        <th>Type\model</th>
        <th>Description</th>
        <th>Serial</th>
        <th>PO №</th>
        <th>Delivered</th>
        <th></th>
    </thead>
    <tbody>
<?php
if ($result->num_rows===0 || !$result) {exit('<tr><td colspan="18">No results</td></tr></table>');}
while($row = $result->fetch_assoc()){
    if(in_array($row['stock_id'], $existed) and $existed_id>0){
        $subquery='SELECT tc_delivered_date FROM transfers_content WHERE tc_transfer_id="'.$existed_id.'" AND tc_stock_id="'.$row['stock_id'].'"';
        $subrow=$db->query($subquery)->fetch_assoc();
        $date=$subrow['tc_delivered_date'];
    }else $date='';
    echo '<tr class="',stock_tr_is_sold($row['stock_status']),'"><input type="hidden" name="tc_stock_id[]" value="'.$row['stock_id'].'">';
    echo stock_view_link($row['stock_id'],$row['stock_compl_id'],$row['stock_is_compl'])//,'</td>'
            , '<td ',color_table($row['stock_status'],$row['stock_condition']),'>',$row['stock_stat_name'],'</td>'
            , '<td>',$row['stock_cat_name'],'</td>'
            , '<td>',$row['mnf_short_name'],'</td>'
            , '<td>',$row['stnmc_pn'],'</td>'
            , '<td>',$row['stnmc_type_model'],'</td>'
            , long_td_string($row['stnmc_descr'],60)
            , '<td>',$row['stock_serial'],'</td>'
            , '<td>',stock_view_po($row['stock_po_comp'],$row['stock_po'],$row['stock_po_type']),'</td>'
            , '<td><input size="10" type="text" class="datepicker" name="tc_delivered_date[]" value="',$date,'"></td>'
            , '<td><button type="button" onclick="table_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>'
            , '</tr>';
}
?>
    </tbody>
</table>