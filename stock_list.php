<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
startSession();
if(check_access('acl_stock', 1)) {exit('Access denied.');}

$page_title = 'Stock List';
include 'header.php';
?>
<div id="side_menu">
    <a class="knopka" href="#"  onclick="display('new_stock')">New stock</a> 
</div>
<div id="wrap" onclick="cancel()"></div>

<!--NEW STOCK -->
<div id="new_stock" class="hidden" style="height:500px">
<h2 align="center">New stock</h2>

<form name="new_stock_form" method="POST" action="/scripts/stock_list_add.php">
<table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
    <tr>
        <td><b>Name</b></td>
        <td><input type="text" required name="stockl_name"  size="50" maxlength="150"></td>
    </tr>
    <tr>
        <td><b>Description</b></td>
        <td><?php echo select_country('','name="stockl_country" class="combobox"') ?></td>
    </tr>
    <tr>
        <td><b>Address</b></td>
        <td><textarea name="stockl_address" rows="5" cols="100" maxlength="500"></textarea></td>
    </tr>
    <tr>
        <td><b>Phone</b></td>
        <td><input type="text" name="stockl_phone" size="30" maxlength="20"></td>
    </tr>
    <tr>
        <td><b>E-mail</b></td>
            <td><input type="text" name="stockl_email" size="30" maxlength="50"></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td><textarea name="stockl_note" maxlength="500" rows="5" cols="100"></textarea></td>
    </tr>
</table>
<div align="right" width="100%" style="padding: 10px">
<input type="submit" value="Add stock item">
<input type="button" value="Close" onclick="cancel()"> 
</div>
</form>

<div id="new_stock_status" align="center" width="100%" style="padding: 10px"></div>
</div>
<!--END NEW STOCK --> 

<div id="main_div_menu">
    
<table id="stock_list" class="sortable" width="100%" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <thead>
        <th width=150>Stock</th>
        <th width=50>Country</th>
        <th>Address</th>
        <th width='150'>Phone</th>
        <th width='200'>E-mail</th>
        <th>Note</th>
    </thead>
    <tbody>
<?php
$query= 'select * from stock_list';
$db= db_connect();
$result=$db->query($query);
while($row = $result->fetch_assoc()){
    echo '<tr>';
    echo '<td>'.view_stocklist_item($row['stockl_id'],$row['stockl_name']).'</td>'
            . '<td>'.$row['stockl_country'].'</td>'
            . '<td>'.$row['stockl_address'].'</td>'
            . '<td>'.$row['stockl_phone'].'</td>'
            . '<td>'.$row['stockl_email'].'</td>'
            . '<td>'.$row['stockl_note'].'</td>'
            . '</tr>';    
}
?>
    </tbody>
</table>
</div>
<?php include 'footer.php';?>
<script type="text/javascript" src="java/java_func.js"></script>