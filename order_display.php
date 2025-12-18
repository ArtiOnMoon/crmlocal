<?php
require_once 'functions/db.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/order_func.php';
if(check_access('acl_stock', 1)) exit('Access denied.');
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);

$limit=100;
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;

$db =  db_connect();
$query='select order_id from for_order where order_deleted=0';
$condition='';
//ДЛЯ СОРТИРОВКИ
$query.=$condition;
$result2=$db->query($query);
echo $db->error;
$num = $result2->num_rows;
if ($num===0) exit('No results');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;
$_SESSION['order_page']=$page; //номер страницы в переменную сессии

$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

//SORT
$sort = ' ORDER BY order_id DESC ';
if ($sort_field!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
$query='select * from for_order where order_deleted=0 ';
$query.=$condition.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
if(!$result=$db->query($query))    exit($db->error);
if ($result->num_rows===0) exit('No results');
?>
<div style="position:fixed; width: 100%; z-index:2; height:50px; padding:0px; margin:0px;">
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
if ($page<$pages)echo '<span><input type="button" onclick="show_stock_table('.($next_page).')" value="Next page"></span>';
if ($page>1)echo '<span><input type="button" onclick="show_stock_table('.$previous_page.')" value="Previous page"></span>';
echo '<span style="float:right">Records:<span id="rec1">'.$num.'</span>&nbsp</span>';
//echo '<input type="button" onclick="go_to()" value="Go to"><input id="go_to" type="text" value="" size="3"><br>';
?>
<form action="stock_multi_edit.php" method="POST" target='_blank'>
<div id="table_wrap">
    <div id="table_wrap">
    <table id="stock_table" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <thead id="thead" onclick="table_sort(event,'order')">
        <th width="10px"><input type='checkbox' id='main_checkbox' onchange='check_all_checkboxes(this)'></th>
        <th width="50" keyword="order_id" <?php sort_class('order_id',$sort_field,$sort_type);?>>ID</th>
        <th width="75" keyword="order_status" <?php sort_class('order_status',$sort_field,$sort_type);?>>Status</th>
        <th width=75 keyword="order_pn" <?php sort_class('order_pn',$sort_field,$sort_type);?>>P/n</th>
        <th width=50 keyword="order_qnt" <?php sort_class('order_qnt',$sort_field,$sort_type);?>>Quantity</th>
        <th width=100 keyword="order_type" <?php sort_class('order_type',$sort_field,$sort_type);?>>Type</th>
        <th keyword="order_link" <?php sort_class('order_link',$sort_field,$sort_type);?>>Link</th>
        <th width="150" keyword="order_date" <?php sort_class('order_datee',$sort_field,$sort_type);?>>Date</th>
        <th width="25" keyword="order_urgency" <?php sort_class('order_urgency',$sort_field,$sort_type);?>>Urgency</th>
        <th width="25" keyword="order_request_by" <?php sort_class('order_request_by',$sort_field,$sort_type);?>>User</th>
        <th keyword="order_note" <?php sort_class('order_note',$sort_field,$sort_type);?>>Note</th>
        <th width="25" keyword="order_po" <?php sort_class('order_po',$sort_field,$sort_type);?>>PO</th>
    </thead>
    <tbody>
<?php
while($row = $result->fetch_assoc()){
    echo '<tr><td><input type="checkbox" class="table_checkbox" name="edit[]" value="',$row['order_id'],'"></td>';
    echo '<td>',$row['order_id'],'</td>'
            , '<td>',$row['order_status'],'</td>'
            , '<td>',$row['order_pn'],'</td>'
            , '<td>',$row['order_qnt'],'</td>'
            , '<td>', order_type_decode($row['order_type']),'</td>'
            , '<td>',$row['order_link'],'</td>'
            , '<td>',$row['order_date'],'</td>'
            , '<td>', order_urgency_decode($row['order_urgency']),'</td>'
            , '<td>',$row['order_request_by'],'</td>'
            , '<td>',$row['order_note'],'</td>'
            , '<td>',$row['order_po'],'</td>'
            , '</tr>';
}
?>
    </tbody>
    </table>
</div>