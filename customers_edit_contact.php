<?php
require_once 'functions/fns.php';
startSession();
security ();
$id=$_POST['id'];
$db =  db_connect();
$query= 'select * from customers_contacts where id = "'.$id.'"';
$result=$db->query($query);

if ($result-> num_rows==1){
    $row=$result->fetch_assoc();
    ?>
<form name="edit_contact_form" method="POST" onsubmit="return edit_contact('<?php echo $row['customer_id'];?>',this)">
<h2 align="center">Edit contact <?php echo $id;?></h2>

<table width="100%" border="1px" cellspacing = "0" cellpadding="2px"><tr>
    <tr>
        <td><b>Department</b></td>
        <td><input name="new_department" size="50" value="<?php echo $row['department'];?>"></td>
    </tr>
    <tr>
        <td><b>Name</b></td>
        <td><input maxlength="100" size="50"  name="new_name" value="<?php echo $row['name'];?>"></td>
    </tr>
    <tr>
        <td><b>Position</b></td>
        <td><input maxlength="300" size="50" name="new_position" value="<?php echo $row['position'];?>"></td>
    </tr>
    <tr>
        <td><b>E-mail</b></td>
        <td><input maxlength="100" size="50" name="new_email" value="<?php echo $row['email'];?>"></td>
    </tr>
    <tr>
        <td><b>Phone</b></td>
        <td><input maxlength="50" size="50" name="new_phone" value="<?php echo $row['phone'];?>"></td>
    </tr>
    <tr>
        <td><b>Mobile</b></td>
        <td><input maxlength="50" name="new_mob" size="50" value="<?php echo $row['mob'];?>"></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td><input name="new_note" size="50" value="<?php echo $row['note'];?>"></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><label><font color="red"><b>Delete</b></font>
                <input type="checkbox" name="deleted" value="1"<?php if ($row['deleted']==1) echo ' chekced';?>></label></td>
    </tr>
    <input type="hidden" name="id" value="<?php echo $_POST['id'];?>">
    <input type="hidden" name="customer_id" value="<?php echo $row['customer_id'];?>">
</table>
<div align="center" width="100%" style="padding: 10px">
    <input type="submit" value="Save" class='green_button'>
    <input type="button" value="Close" onclick="document.getElementById('edit_contact').style.display='none'">
</div>
</form>
<?php
} 
else {
    echo 'Nothing found';
    exit();
}