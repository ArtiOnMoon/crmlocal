<?php
require_once 'functions/stock_fns.php';
require_once 'functions/fns.php';
startSession();
security ();
$id=$_GET['id'];
if (isset($_POST['id'])) $id=$_POST['id'];
//do_page_header('View stock item Info','Stock');
echo'<div style="width: 1024px; align-content: left; text-align:left; display: inline-block; padding:10px">';
echo '<h1> Stock Item №'.$id.'</h1>';

$db =  db_connect();
$query= 'select * from stock where id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1) exit('Nothing found.');
$row=$result->fetch_assoc();
if ($row['deleted']) $delete='checked';
?>
<!--<a class="knopka" href="deconsolidation.php?id=<?php echo $id;?>">Deconsolidation</a><p>-->
<form id="change_stock_form" onsubmit="return change_stock_item(<?php echo $_SESSION['stock_page'];?>)">
<table width="100%" border="1px" cellspacing = "0" cellpadding="2px" style="text-align: left">
    <tr>
        <td><b>ID</b></td>
        <td><?php echo $row['id'];?></td>
    </tr>
    <tr>
        <td><b>Manufacturer</b></td>
        <td><?php echo select_customer($row['manufacturer']);?></td>
    </tr>
    <tr>
        <td><b>Supplier</b></td>
        <td><?php echo select_customer($row['supplier'],'','class="combobox" name="supplier"');?>
    </tr>
    <tr>
        <td><b>Class</b></td>
        <td><?php echo select_stock_class($row['class']);?></td>
    </tr>
    <tr>
        <td><b>Status</b></td>
        <td><?php echo select_stock_stat($row['status']);?></td>
    </tr>
    <tr>
        <td><b>Type or P/N</b></td>
        <td><input type="text" maxlength="30" name="new_type_or_pn" required value="<?php echo $row['type_or_pn'];?>"></td>
    </tr>
    <tr>
        <td><b>Serial</b></td>
        <td><input type="text" maxlength="50" name="new_serial" required value="<?php echo $row['serial'];?>"></td>
    </tr>
    <tr>
        <td><b>Description</b></td>
        <td><input type="text" maxlength="50" name="new_descr" value="<?php echo $row['descr'];?>"></td>
    </tr>
    <tr>
        <td><b>Stock</b></td>
        <td>
            <?php echo select_stock($row['stock']);?> 
            <a href="stocklist_view.php?id=<?php echo $row['stock'];?>">Stock info</a>
        </td>
    </tr>
    <tr>
        <td><b>Place</b></td>
        <td><input type="text" maxlength="20" name="new_place" required value="<?php echo $row['place'];?>"></td>
    </tr>
    <tr>
        <td><b>Condition</b></td>
        <td><?php echo select_condition($row['cond'],'0','name="new_cond"');?></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td><textarea name="new_note" maxlength="200" cols="100" rows="2" style="resize: none;"><?php echo $row['note']; ?></textarea></td>
    </tr>
    <tr>
        <td><b>PO №</b></td>
        <td><input type="text" maxlength="8"  name="purchase_order" value="<?php echo $row['purchase_order'];?>"> <?php echo view_purchase_link($row['purchase_order']);?></td>
    </tr>
    <tr>
        <td><b>SO №</b></td>
        <td><input type="text" maxlength="8"  name="sales_order" value="<?php echo $row['sales_order'];?>"> <?php echo view_sales_link($row['sales_order']);?></td>
    </tr>
    <tr>
        <td><b>Date of receipt</b></td>
        <td><input type="text" class="datepicker" name="new_date_receipt" value="<?php echo $row['date_receipt'];?>"></td>
    </tr>
    <tr>
        <td><b>Warranty up to</b></td>
        <td><input type="text" name="warranty" class="datepicker" value="<?php echo $row['warranty'];?>"> Days left:<?php echo days_left($row['warranty']);?></td>
    </tr>
    <tr>
        <td><b>Net price</b></td>
        <td><input type="text" maxlength="10" name="new_net_price" value="<?php echo $row['net_price'];?>"></td>
    </tr>
    <tr>
        <td><b>Minimal price</b></td>
        <td><input type="text" maxlength="30" name="min_price" value="<?php echo $row['min_price'];?>"></td>
    </tr>
    <tr>
        <td><b>Currency</b></td>
        <td><?php echo select_currency($row['currency']);?></td>
    </tr>
    <tr>
        <td><b>Freight</b></td>
        <td><input type="text" maxlength="30" name="new_freight" value="<?php echo $row['freight'];?>"></td>
    </tr>
    <tr>
        <td><b>Customs Declaration</b></td>
        <td><input type="text" maxlength="50" name="new_freight" value="<?php echo $row['customs_dec'];?>"></td>
    </tr>
    <tr>
        <td><b>Repair price</b></td>
        <td><input type="text" name="new_repair_price" value="<?php echo $row['repair_price'];?>"></td>
    </tr>
    <tr>
        <td><b>Profit</b></td>
        <td><input type="text" name="new_profit" value="<?php echo $row['profit'];?>"></td>
    </tr>
    <tr>
        <td><b>Engineer</b></td>
        <td><input type="text" name="new_engineer_code" maxlength="20" value="<?php echo $row['engineer_code'];?>"></td>
    </tr>
    <tr>
        <td><b>On balance</b></td>
        <td><?php echo select_stock_on_balance($row['on_balance'], 'name="on_balance"') ?></td>
    </tr>
    <tr>
        <td><b>Complect</b></td>
        <td><?php echo select_complect($row['is_complect'],'complect_id_field');?></td>
    </tr>
    <tr>
        <td><b>Complect id</b></td>
        <td><input type="text" name="complect_id" required id="complect_id_field" oninput="stock_complect_search(this,'stock_complect_div')"
            <?php if($row['is_complect']!=="2") echo' disabled ' ?> value="<?php echo $row['complect_id'];?>">
            <div class="search" id="stock_complect_div">TEST
            TEST</div>
            <?php if($row['is_complect']==="2") echo view_stock_link($row['complect_id'],'View main item'); ?> 
        </td>
    </tr>
    <tr>
        <td><b>DELETE position</b></td>
        <td><input type="checkbox" name="delete" value="1" <?php echo $delete ?>>delete</td>
    </tr>
</table>
<div align="center"><input type="submit" value="Apply changes"> <input type="button" value="Close" onclick="cancel()"></div>
<input type="hidden" name="vessel_id" value="<?php echo $row['vessel_id'];?>">
<input type="hidden" name="stock_id" value="<?php echo $row['id'];?>">
</form>
<?php
if($row['is_complect']==="1"){
    $query='select id, type_or_pn, serial, descr, note from stock where complect_id='.$row['id'];
    $result=$db->query($query);
    ?>
<p><h2>Complect parts</h2>
<table width="100%">
    <thead>
    <th>ID</th><th>Part number</th><th>Serial</th><th>Description</th><th>Note</th>
    </thead>
    <tr>
<?php
if ($result->num_rows>0) {
while ($row=$result->fetch_assoc()){
    echo'<td>'.stock_id_link($row['id']).'</td>'
            . '<td>'.$row['type_or_pn'].'</td>'
            . '<td>'.$row['serial'].'</td>'
            . '<td>'.$row['descr'].'</td>'
            . '<td>'.$row['note'].'</td>'
            . '</tr>';
}
}
echo '</table>';
}
?>
</div>
