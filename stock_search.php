<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
do_page_header('Stock advanced search');
if(check_access('acl_stock', 1)) exit('Access denied.');
echo '<h1 align="center">Stock advanced search</h1>';
$db =  db_connect();
$query='select * from stock where ';
if ($_POST[stock_class]!=='All') {$query.='class =("'.$_POST[stock_class].'")';}
else {$query.='class LIKE("%")';}
if ($_POST[stock_pn]!='') {$query.=' AND type_or_pn LIKE("%'.$_POST[stock_pn].'%")';}
if ($_POST[stock_descr]!='') {$query.=' AND descr LIKE("%'.$_POST[stock_descr].'%")';}
if ($_POST[stock_note]!='') {$query.=' AND note LIKE("%'.$_POST[stock_note].'%")';}
if ($_POST[stock_complect]!='') {$query.=' AND complect LIKE("%'.$_POST[stock_complect].'%")';}
if ($_POST[stock_item_status]!=='') {$query.=' AND status LIKE("%'.$_POST[stock_item_status].'%")';}
if ($_POST[stock_serial]!='') {$query.=' AND serial LIKE("%'.$_POST[stock_serial].'%")';}

echo '<a href="stock.php">Return to stock</a>';
$result=$db->query($query);
?>
<div id="main_div_menu">
<table id="stock_table" class="sortable" width="100%" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <thead>
        <th width=50>ID</th><th width=50>Class</th><th>P/N or type</th><th>Serial</th><th>Description</th><th>Place</th><th>Note</th>
    <tbody>
<?php
while($row = $result->fetch_assoc()){
    echo '<tr>';
    echo '<td>'.stock_id_link($row['id']).'</td>'
            . '<td>'.$row['class'].'</td><td>'.$row['type_or_pn'].'</td><td>'.$row['serial'].'</td><td>'.$row['descr'].'</td><td>'.$row['place'].'</td><td>'.$row['note'].'</td></tr>';
}
?>
</tbody></table></div>