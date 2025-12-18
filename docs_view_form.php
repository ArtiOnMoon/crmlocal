<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'PATHS.php';
startSession();
security();
do_page_header('View form');
echo '<div id="main_div_menu">';
$id=$_GET['id'];
$query= 'select docs_forms.*, u1.full_name as uploader, u2.full_name as mod_by '
        . 'from docs_forms, users as u1, users as u2 '
        . 'where u1.username=docs_forms.uploader and u2.username=docs_forms.modified_by and ';
$query='select docs_forms.*, u1.full_name uploader_name, u2.full_name as modified_name '
        . 'from docs_forms LEFT JOIN users u1 ON docs_forms.uploader=u1.username LEFT JOIN users u2 ON docs_forms.modified_by=u2.username '
        . 'where id="'.$id.'"';
$db =  db_connect();
$result=$db->query($query);
if (!$result) exit('Error: ' . $db->error);
if ($result->num_rows===0) exit('Not found.');
$row=$result->fetch_assoc();
?>
<form action="docs_change.php" method="POST" enctype="multipart/form-data">
        <table width="100%">
        <tr>
            <td><b>Name</b></td>
            <td><input type="text" name="name" required maxlength="100" value="<?php echo $row['name'] ?>"></td>
        </tr>
        <tr>
            <td><b>Description</b></td>
            <td><textarea name="description" required  rows="5" cols="50" maxlength="500"><?php echo $row['description'] ?></textarea></td>
        </tr>
        <tr>
            <td><b>Uploader</b></td>
            <td><?php echo $row['uploader_name'];?></td>
        </tr>
        <tr>
            <td><b>Modified by</b></td>
            <td><i><?php echo $row['modified_name'];?> at <?php echo $row['modified'];?></i></td>
        </tr>
        </table>
    <div align="center" width="100%" style="padding: 10px">
    <input type="submit" value="Change document">
    <input type="hidden" name="id" value="<?php echo $id ?>">
</form>
<form action='delete_document.php' method='POST'>
    <br>
    <input type="submit" value="Delete" onclick="return check_delete()">
    <input type="hidden" name="id" value="<?php echo $id ?>">
</form>
    </div>
    <?php
    if ($row['file_name']==='') echo
        '<form action="upload_document.php" method="POST" enctype="multipart/form-data">'
        . '<input type="hidden" name="MAX_FILE_SIZE" value="3000000">'
        . 'Send this file: <input name="userfile" type="file">'
        . '<input type="hidden" name="id" value="'.$id.'">'
        . '<input type="submit" value="Upload file">'
        . '</form>';
    else echo
        '<form action="/scripts/docs_download_file.php" method="POST">'
        . '<i>'.$row['file_name'].'</i><br>'
        . '<input type="submit" name="download_button" value="Download file"><br><br>'
        . '<input type="submit" name="delete_button" value="Delete file" onclick="return check_delete()">'
        . '<input type="hidden" name="id" value="'.$id.'">'
        . '<input type="hidden" name="file_name" value="'.$row['file_name'].'">'
        . '<input type="hidden" name="type" value="'.$row['cat'].'">'
        . '</form>';
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