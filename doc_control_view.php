<?php
require_once 'functions/fns.php';
require_once 'functions/selector.php';

startSession();
security();
?>
<div class="window_internal" style="width:800px;height:720px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<?php
$id=$_POST['id'];
$query= 'select * from documents where id="'.$id.'"';
$db =  db_connect();
$result=$db->query($query);
if (!$result) exit('Error: ' . $db->error);
if ($result->num_rows===0) exit('Not found.');
$row=$result->fetch_assoc();
?>
<h1 align="center">Document <?php echo $id;?></h1>
<form action="/scripts/doc_control_change.php" method="POST" enctype="multipart/form-data" onsubmit="return doc_control_change(this)">
        <table width="50%" align="center">
        <tr>
            <td><b>Type</b></td>
            <td><?php echo select_doc_type($row['type']) ?></td>
        </tr>
        <tr>
            <td><b>Company</b></td>
            <td><?php select_our_company($row['our_company'],'required name="our_company"');?></td>
        </tr>
        <tr>
            <td><b>Description</b></td>
            <td><input type="text" name="doc_name" required maxlength="140" value="<?php echo $row['doc_name'] ?>"></td>
        </tr>
        <tr>
            <td><b>Document number</b></td>
            <td><input type="text" name="doc_number" maxlength="150" value="<?php echo $row['doc_number'] ?>"></td>
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
            <td><?php echo select_user($row['incharge'],'name="user"')?></td>
        </tr>
        <tr>
            <td><b>Archived</b></td>
            <td><input type="checkbox" name="is_archive" value="1" <?php if($row['is_archive']==='1') echo "checked";?>></td>
        </tr>
        </table>
    <div align="center" width="100%" style="padding: 10px">
    <input type="submit" class="green_button" value="Save">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    </div>
</form>
<form action='delete_document.php' method='POST'>
    <div align="center" width="100%" style="padding: 10px">
    <input type="submit" class="red_button" value="Delete" onclick="return check_delete()">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    </div>
</form>
<div class="block_div">
<?php
    if ($row['file']==='*NO_FILE*') {
?>
<form action="upload_document.php" method="POST" enctype="multipart/form-data" onsubmit="return doc_upload_file(this,'<?php echo $id;?>')">
    <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
    Send this file: <input name="userfile" type="file">
    <input type="hidden" name="id" value="<?php echo $id;?>">
    <input type="submit" value="Upload file">
</form>
<?php
    }
    else {
        ?>
<i><?php echo $row['file'];?></i><br>
<form name="file_form" action="download_doc.php" method="POST">
    <input type="submit" name="download_button" value="Download file"><br><br>
    <input type="hidden" name="id" value="<?php echo $id;?>">
    <input type="hidden" name="file" value="<?php echo $row['file'];?>">
</form>
<form name="file_form" onsubmit="return doc_delete_file(this,'<?php echo $id;?>')">
    <input type="submit" name="delete_button" value="Delete file" onclick="return check_delete()">
    <input type="hidden" name="id" value="<?php echo $id;?>">
    <input type="hidden" name="file" value="<?php echo $row['file'];?>">
</form>
</div>
<?php
        }
    ?>
</div>