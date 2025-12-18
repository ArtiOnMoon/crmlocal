<?php
require_once 'functions/fns.php';
startSession();
security();
do_page_header('Change task');
$id=$_GET['question_id'];
$query= 'select * from questions where id="'.$id.'"';
$db =  db_connect();
echo '<div id="main_div_menu">';
$result=$db->query($query);
if ($result->num_rows===0){
    exit('Not found');
}
$row=$result->fetch_assoc();
 if (!access_check([''],['vk','kvv',$row['user']])) exit ('Access denied.');
else $disabled=0;
?>

<form action="change_question.php" method="POST">
<table width="100%" border="1px" cellspacing = "0" cellpadding="2px"><tr>
<td><b>Question</b></td><td>
<textarea name="text" required maxlength="490" cols="100" rows="3"><?php echo $row['text']?></textarea></td>
</tr><tr>
    <td>Change status</td><td>
        <select name="status" value='published' <?php echo $disabled ?>>
    <option selected>published</option>
    <option>accepted</option>
    <option>declined</option>
</select>
</td></tr>
<td><b>Answer</b></td><td>
<textarea maxlength="490" name="answer" cols="100" rows="3" <?php echo $disabled ?>>
<?php echo $row['answer'] ?></textarea></td>
</tr></table>
    <div align="right" width="100%" style="padding: 10px">
    <input type="submit" value="Change question">
    </div>
    <input type="hidden" name="id" value="<?php echo $id ?>">
    <input type="hidden" name="disabled" value="<?php echo $disabled ?>">
</form>
<!--Удаление -->
<form action='delete_question.php' method='POST'>
<div align="right" width="100%" style="padding: 10px">
    <input type="submit" value="Delete" onclick="return check_delete()">
    <input type="hidden" name="id" value="<?php echo $id ?>">
    </div>
</form>
</div>
<script type="text/javascript"> 
function check_delete()
{
if (confirm("Delete this record?"))
    return true;
    else return false;
}
</script>