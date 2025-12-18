<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'functions/transfers.php';
session_start();

if(check_access('acl_stock', 1)) exit('Access denied.');
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$our_companies_list= get_our_companies_list(1);

$limit=100;
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;
$db =  db_connect();
$query='SELECT transfer_id FROM transfers '
        . 'WHERE transfers_deleted=0';
$query.=$cond;
if (!$result2=$db->query($query)) {
    echo $db->error;
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
$query='SELECT transfers.*,transfers_status.transfers_status_name,sl1.stockl_name as from_stock, sl2.stockl_name as to_stock FROM transfers '
        . 'LEFT JOIN transfers_status ON transfers_status=transfers_status.transfers_status_id '
        . 'LEFT JOIN stock_list sl1 ON sl1.stockl_id=transfers_from '
        . 'LEFT JOIN stock_list sl2 ON sl2.stockl_id=transfers_to '
        . 'WHERE transfers_deleted=0';
$query.=$cond.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
echo $query;
if(!$result=$db->query($query)){
    echo $db->error;
}
if ($result->num_rows===0) exit('No results');
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
<table id="stock_table" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <thead>
        <th keyword="transfer_id" <?php sort_class('transfer_id',$sort_field,$sort_type);?>>ID</th>
        <th width="80" keyword="transfers_status" <?php sort_class('transfers_status',$sort_field,$sort_type);?>>Status</th>
        <th width="75" keyword="from_stock" <?php sort_class('from_stock',$sort_field,$sort_type);?>>From</th>
        <th width="80" keyword="to_stock" <?php sort_class('to_stock',$sort_field,$sort_type);?>>To</th>
        <th width="120" keyword="transfers_from_date" <?php sort_class('transfers_from_date',$sort_field,$sort_type);?>>Shipped</th>
        <th width="120" keyword="transfers_to_date" <?php sort_class('transfers_to_date',$sort_field,$sort_type);?>>Delivered</th>
        <th keyword="transfers_note" <?php sort_class('transfers_note',$sort_field,$sort_type);?>>Note</th>
    </thead>
<?php
while ($row=$result->fetch_assoc()){
    echo'<tr>'
        , '<td>',transfers_view($row['transfer_id']),'</td>'
        , '<td>',$row['transfers_status_name'],'</td>'
        , '<td>',$row['from_stock'],'</td>'
        , '<td>',$row['to_stock'],'</td>'
        , '<td>',$row['transfers_from_date'],'</td>'
        , '<td>',$row['transfers_to_date'],'</td>'
        , long_td_string($row['transfers_note'],40)
    , '</tr>';
}
?>
</tbody></table>