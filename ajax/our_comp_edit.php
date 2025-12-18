<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$id=clean($_POST['id']);
$db =  db_connect();
$query= 'SELECT * FROM our_details WHERE our_details.id = "'.$id.'"';
if(!$result=$db->query($query))exit($db->error);
$row = $result->fetch_assoc();
$name=$row['name'];
$our_comp_id=$row['our_comp_id'];
$pay_comment=$row['pay_comment'];
$query= 'SELECT param_name, param_value FROM our_details_sub WHERE details_id = "'.$id.'"';
if(!$result=$db->query($query))exit($db->error);
?>
<form action="../scripts/our_comp_details_change.php" method="POST">
<input type="hidden" name="id" value="<?php echo $id;?>">
<input type="hidden" name="our_comp_id" value="<?php echo $row['our_comp_id'];?>">
<table width="100%" class="fancy_table" id="t_edit_bank_details">
    <tr>
        <td class="fancy_td"><strong>Name</strong></td>
        <td class="fancy_td" colspan="2"><input type="text" name="name" maxlength="20" value="<?php echo $name;?>"></td>
    </tr>
    <tr>
        <td class="fancy_td"><strong>Payment comment</strong></td>
        <td class="fancy_td" colspan="2"><textarea name="pay_comment"><?php echo $pay_comment;?></textarea></td>
    </tr>
    <tr>
        <td colspan="3" align="center"><strong>Bank details</strong></td>
    </tr>
    <tr>
        <td class="fancy_td"><strong>Parametr</strong></td>
        <td class="fancy_td"><strong>Value</strong></td>
        <td class="fancy_td"><strong>Delete</strong></td>
    </tr>
<?php
WHILE ($row = $result->fetch_assoc()){
    echo'<tr><td class="fancy_td"><input name="param_name[]" type="text" value="'.$row['param_name'].'"></td>'
            . '<td class="fancy_td"><input type="text" name="param_value[]" value="'.$row['param_value'].'"></td>'
            . '<td class="fancy_td"><a href="#" onclick="this.parentNode.parentNode.parentNode.deleteRow(this.parentNode.parentNode.rowIndex)">Delete</a></td></tr>';
}
?>
    </table>
<table width="100%">
    <tr>
        <td colspan="2" align="center"><a href="#" onclick="edit_bank_det_add_line()">Add line</a></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><input class="button" type="submit" value="Apply changes"></td>
    </tr>
</table>
</form>