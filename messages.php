<?php
require_once 'functions/fns.php';
require_once 'functions/message_fns.php';
startSession();
do_page_header('Messages');
?>
<div id="side_menu">
<a class="knopka" href="#" onclick="document.getElementById('new_message').style.display='block'">New message</a>
</div>
<div id="message_1">
<h2>Input messages</h2>
<?php show_input_message(); ?>
</div><div id="message_2">
<h2>Output messages</h2>
<?php show_output_message(); ?>
</div>

<?php
if (isset($_GET['message_id']))echo'<h1>HERE be a message text</h1>';
?>

<!-- ФОРМА ОТправки нового сообщения -->
<div id="new_message">
<h1 align="center">New message</h1>
<form enctype="multipart/form-data" action="message_send.php" name="new_message" method="POST">
<table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
<?php
echo '<tr><td><b>Send to</b></td><td>';
echo select_user();
echo '</td></tr>';
?>
<tr><td><b>Subject</b></td><td>
<input type="text" required name="message_subject">
</td></tr>

<tr><td width="100"><b>Message text</b></td><td><textarea required rows="3" cols="50" name="message_content"></textarea></td></tr>
</table>
<div align="right" width="100%" style="padding: 10px">
<input type="submit" class="new" value="Send message">
<input type="button" class="new" value="Close" onclick="document.getElementById('new_message').style.display='none'"><p>
Attach file: <input name="userfile" type="file" multiple/>';
</div>
</form>
<div id="message_status" align="center" width="100%" style="padding: 10px"></div>
</div>

