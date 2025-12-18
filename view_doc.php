<?php
require_once 'functions/fns.php';
startSession();
security();
do_page_header('Documents');
echo '<div id="main_div_menu">';
$id=$_GET['id'];
$query= 'select * from documents where id="'.$id.'"';
$db =  db_connect();
$result=$db->query($query);
if (!$result) exit('Error: ' . $db->error);
if ($result->num_rows===0) exit('Not found.');
$row=$result->fetch_assoc();
?>
<form action="change_document.php" method="POST" enctype="multipart/form-data">
        <table width="50%" align="center">
        <tr>
            <td><b>Type</b></td>
            <td><?php echo select_doc_type($row['type']) ?></td>
        </tr>
        <tr>
            <td><b>Description</b></td>
            <td><input type="text" name="doc_name" required maxlength="140" value="<?php echo $row['doc_name'] ?>"></td>
        </tr>
        <tr>
            <td><b>Start date</b></td>
            <td><input type="text" name="start_date" required class="datepicker" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d',$row['start_date']) ?>"></td>
        </tr>
        <tr>
            <td><b>Expire</b></td>
            <td><input type="text" name="expire_date" required class="datepicker" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d',$row['expire_date']) ?>"></td>
        </tr>
        <tr>
            <td><b>Warning interval</b></td>
            <td><input type="text" name="alarm" required size="5" maxlength="4" value="<?php echo $row['alarm'] ?>"> days</td>
        </tr>
        <tr>
            <td><b>Person incharge</b></td>
            <td><?= select_user($row['incharge'],'name="user"')?></td>
        </tr>
        </table>
    <div align="center" width="100%" style="padding: 10px">
    <input type="submit" value="Change document">
    <input type="hidden" name="id" value="<?php echo $id ?>">
    </div>
</form>
<form action='delete_document.php' method='POST'>
    <div align="center" width="100%" style="padding: 10px">
    <input type="submit" class="red_button" value="Delete" onclick="return check_delete()">
    <input type="hidden" name="id" value="<?php echo $id ?>">
    </div>
</form>
<div class="block_div">
<?php
    if ($row['file']==='*NO_FILE*') {
?>
<form action="upload_document.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
Send this file: <input name="userfile" type="file">
<input type="hidden" name="id" value="<?php echo $id;?>">
<input type="submit" value="Upload file">
</form>
<?php
    }
    else {
        ?>
</form><form action="download_doc.php" name="file_form" method="POST">
<i><?php echo $row['file'];?></i><br>
<input type="submit" name="download_button" value="Download file"><br><br>
<input type="submit" name="delete_button" value="Delete file" onclick="return check_delete()">
<input type="hidden" name="id" value="<?php echo $id;?>">
<input type="hidden" name="file" value="<?php echo $row['file'];?>">
</form>
</div>
<?php
        }
    ?>
<script>
$( function() {
    $( ".datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd'
    });
  } );

function check_delete()
{
if (confirm("Delete this record?"))
    return true;
    else return false;
}
</script>