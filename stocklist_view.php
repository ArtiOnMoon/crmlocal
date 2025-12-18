<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_stock', 1)) exit('Access denied.');

$id=$_GET['id'];
do_page_header('View stock','Stock');
echo'<div id="main_div_menu2"><div style="width: 1024px; align-content: left; text-align:left; display: inline-block">';
echo '<h1> Stock #'.$id.'</h1>';

$db =  db_connect();
$query= 'select * from stock_list where stockl_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1) exit('Nothing found.');

$row=$result->fetch_assoc();
if ($row['deleted']) $delete='checked';
?>
<form action="/scripts/stock_list_change.php" method="POST">
<table width="100%" border="1px" cellspacing = "0" cellpadding="2px" style="text-align: left">
    <tr>
        <td><b>Name</b></td>
        <td><input type="text" maxlength="150" size="50" name="stockl_name" required value="<?php echo $row['stockl_name'];?>"></td>
    </tr>
    <tr>
        <td><b>Country</b></td>
        <td><?php select_country($row['stockl_country'],'name="stockl_country" class="combobox"');?></td>
    </tr>
    <tr>
        <td><b>Address</b></td>
        <td><textarea name="stockl_address" required rows="5" cols="100" maxlength="500" style="resize: none"><?php echo $row['stockl_address'];?></textarea></td>
    </tr>
    <tr>
        <td><b>Phone</b></td>
        <td><input type="text" maxlength="20" name="stockl_phone" value="<?php echo $row['stockl_phone'];?>"></td>
    </tr>
    <tr>
        <td><b>E-mail</b></td>
        <td><input type="text" maxlength="50" name="stockl_email" value="<?php echo $row['stockl_email'];?>"></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td><textarea name="stockl_note" maxlength="500" cols="100" rows="5" style="resize: none;"><?php echo $row['stockl_note']; ?></textarea></td>
    </tr>
    <tr>
        <td><b>DELETE</b></td>
        <td><input type="checkbox" name="delete" value="1" <?php echo $delete ?>>delete</td>
    </tr>
</table>
    <input type="hidden" name="stockl_id" value="<?php echo $id;?>">
<div align="center"><input type="submit" value="Apply changes"></div>
</form>
</div>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_stock_func.js"></script>