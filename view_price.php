<?php
require_once 'functions/fns.php';
require_once 'functions/invoice_func.php';
$id=$_GET['price_id'];
do_page_header('View invoice');
echo'<div id="main_div_menu">';
echo '<h1> Item № '.$_GET['price_id'].'</h1>';
$db =  db_connect();
$query= 'select * from price where id = "'.$id.'"';
$result=$db->query($query);
echo $db->error;
if ($result-> num_rows==1){
$row=$result->fetch_assoc();

echo '<form action="change_price.php" method="POST">';
echo '<table width="100%" rules="rows" frame="void" border="1px" cellspacing = "0" cellpadding="2px">';

echo '<td><b>Class</b></td><td>';
echo select_stock_class($row['class']);
echo '</td></tr>';
    
echo '<tr><td><b>Part number</b></td><td><b>';
echo '<input required type="text" name="pn" value="'.$row['pn'].'">';
echo '</b></td>';
echo '<tr>';

echo '<tr>';
echo '<td><b>Description</b></td><td>';
echo '<textarea required maxlength="50" cols="60" name="description">'.$row['description'].'</textarea></td>';
echo '</tr>';

echo '<td><b>Company</b></td><td>';
echo select_customer($row['manufacturer_id']);
echo '</td></tr>';

echo '<tr>';
echo '<td><b>Date</b></td><td>';
echo '<input required type="text" name="date" placeholder="yyyy-mm-dd" value="'.$row['date'].'"></td>';
echo '</tr>';

echo '<tr>';
echo '<td><b>Currency</b></td><td>';
echo select_currency(htmlspecialchars_decode($row['currency']));
echo '</td></tr>';

echo '<tr>';
echo '<td><b>Price</b></td><td>';
echo '<input required type="text" name="price" maxlength="7" value="'.$row['price'].'"></td>';
echo '</tr>';

echo '<tr>';
echo '<td><b>Discount</b></td><td>';
echo '<input required type="text" maxlength="3" name="discount" placeholder="yyyy-mm-dd" value="'.$row['price_discount'].'"></td>';
echo '</tr>';

echo '<tr>';
echo '<td><b>User</b></td><td>';
echo $row['user'];
echo '</tr>';

echo '<tr>';
echo '<td colspan="2" align="center"><label><font color="red"><b>Delete</b></font>';
echo '<input type="checkbox" name="price_deleted" value="1"';
if ($row['price_deleted']==1) echo ' checked';
echo'></label></td></tr>';
echo '</table>';
echo '<input type="hidden" name="id" value="'.$_GET['price_id'].'">';
?>
<div align="center" width="100%" style="padding: 10px">
<input type="submit" value="Apply changes">
</div></form>
<?php
};


