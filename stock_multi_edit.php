<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
startSession();
security();
if(check_access('acl_stock', 1)) exit('Access denied.');
IF (!isset($_POST['edit']))exit ('Nohing found');
$elements= implode(',', $_POST['edit']);
$db=db_connect();
$query='select * from stock where id in('.$elements.')';
$result=$db->query($query);
$db->close();
?>
<link rel="icon" href="img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="java/jquery-ui-1.12.1/jquery-ui.min.css">
<script src="java/jquery-3.1.1.min.js"></script>
<script src="java/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<h2>Stock edit</h2>
<form action="./scripts/stock_multi_change.php" method="POST">
<table><thead>
    <th>ID</th><th>P/N</th><th>S/N</th><th>Description</th><th>Gategory</th><th>Status</th>
    <th>Condition</th><th>Note</th><th>Date</th><th>Manufacturer</th><th>Supplier</th>
    <th>Stock</th><th>Place</th><th>On balance</th>
</thead>
<tbody>
<?php
while ($row= $result->fetch_assoc()){
echo '<tr><td><input readonly size="6" type="text" name="id[]" value="',$row['id'],'"></td>'
        . '<td><input type="text" name="pn[]" value="',$row['type_or_pn'],'"></td>'
        . '<td><input type="text" name="serial[]" value="',$row['serial'],'"></td>'
        . '<td><input type="text" name="descr[]" value="',$row['descr'],'"></td>'
        . '<td>', select_stock_class($row['class'], 0, 'name="cat[]"'),'</td>'
        . '<td>', select_stock_stat($row['status'], 0, 'name="stat[]"'),'</td>'
        . '<td>', select_condition($row['cond'], 0, 'name="cond[]"'),'</td>'
        . '<td><input type="text" name="note[]" value="',$row['note'],'"></td>'
        . '<td><input type="text" name="date[]" size="10" class="datepicker" value="',$row['date_receipt'],'"></td>'
        . '<td>', select_customer($row['manufacturer'], 0, 'name="manuf[]" style="width:150px"'),'</td>'
        . '<td>', select_customer($row['supplier'], 0, 'name="suppl[]" style="width:150px"'),'</td>'
        . '<td>', select_stock($row['stock'], 'name="stock[]"'),'</td>'
        . '<td><input type="text" name="place[]" size=5 value="',$row['place'],'"></td>'
        . '<td>', select_stock_on_balance($row['on_balance'], 'name="on_balance[]"'),'</td>';
echo '</tr>';
}
?>
</tbody>
</table>
    <input type="submit" value="Save changes" >
</form>
<script type="text/javascript" src="java/java_func.js"></script>