<?php
require_once 'functions/fns.php';
startSession();
security ();
$id=$_POST['id'];
$db =  db_connect();
$query= 'select * from customers_branches where id = "'.$id.'"';
$result=$db->query($query);

if ($result-> num_rows==1){
    $row=$result->fetch_assoc();
    ?>
<form name="edit_branch_form" method="POST" onsubmit="return edit_branch('<?php echo $row['customer'];?>')">
<h2 align="center">Edit contact <?php echo $id;?></h2>

<table width="100%" border="1px" cellspacing = "0" cellpadding="2px"><tr>
    <tr>
        <td><b>Name</b></td>
        <td><input maxlength="100" name="branch_name" value="<?php echo $row['branch_name'];?>"></td>
    </tr>
    <tr>
        <td><b>E-mail</b></td>
        <td><input maxlength="30" name="branch_email" value="<?php echo $row['branch_email'];?>"></td>
    </tr>
    <tr>
        <td><b>Phone</b></td>
        <td><input maxlength="15" name="branch_phone" value="<?php echo $row['branch_phone'];?>"></td>
    </tr>
    <tr>
        <td><b>Address</b></td>
        <td><input name="branch_address" value="<?php echo $row['branch_address'];?>"></td>
    </tr>
    <tr>
        <td><b>Note</b></td>
        <td><input name="branch_note" value="<?php echo $row['branch_note'];?>"></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><label><font color="red"><b>Delete</b></font>
        <input type="checkbox" name="deleted" value="1"<?php if ($row['deleted']==1) echo ' chekced';?>></label></td>
    </tr>
    <input type="hidden" name="id" value="<?php echo $id;?>">
</table>
<div align="center" width="100%" style="padding: 10px">
    <input type="submit" value="Apply changes">
    <input type="button" value="Close" onclick="document.getElementById('edit_contact').style.display='none'">
</div>
</form>
<?php
} 
else {
    echo 'Nothing found';
    exit();
}