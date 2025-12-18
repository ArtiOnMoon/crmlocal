<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$id=clean($_POST['id']);
$db =  db_connect();
$query= 'SELECT * FROM manufacturers WHERE mnf_id = "'.$id.'"';
if(!$result=$db->query($query))exit($db->error);
$row = $result->fetch_assoc();
?>
<h2>Edit manufacturer</h2>
    <form method="POST" action="/scripts/manufacturer_change.php">
    <input type='hidden' name='mnf_id' value='<?php echo $id;?>'>
    <table width="100%">
        <tr>
            <td><strong>Short name</strong></td><td><input type="text" name="mnf_short_name" value='<?php echo $row['mnf_short_name'];?>'></td>
        </tr>
        <tr>
            <td><strong>Full name</strong></td><td><input type="text" name="mnf_full_name" value='<?php echo $row['mnf_full_name'];?>'></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" class="green_button" value="Save">
                <input type="button" value="Cancel" onclick="cancel()">
            </td>
        </tr>
    </table>
    </form>