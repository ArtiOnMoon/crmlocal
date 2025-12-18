<?php 
require_once 'functions/fns.php';
require_once 'functions/doc_fns.php';
startSession();

$page_title = 'Documents';
include 'header.php';

if(check_access('acl_documents', 1)) exit('Access denied.');

?>
<div id="side_menu">
    <input type="button" value="New document" onclick="display('new_document')">
    <label>Type <?php echo select_doc_type('',1);?></label>
    <label>Company <?php select_our_company(0, 'id="doc_our_company" onchange="show_docs_table(1)"',1);?></label>
    <label>Show archived <input type="checkbox" id="doc_archived" onchange="show_docs_table(1)"></label>
    <span style="float:right;">Fast search: <input type="search" id="doc_search" placeholder = "Enter document name" oninput="show_docs_table(1)"></span>
</div>
<div id="wrap" onclick="cancel()"></div>
<div class="hidden" id="new_document" style="width: 400px; height: 400px;">
    <h2 align="center">Add new document</h2>
    <form enctype="multipart/form-data" action="doc_control_add.php" method="POST">
        <table width="100%" align="center" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Type</b></td>
            <td><?php echo select_doc_type('') ?></td>
        </tr>
        <tr>
            <td><b>Company</b></td>
            <td><?php select_our_company(0,'required name="our_company"');?></td>
        </tr>
        <tr>
            <td><b>Description</b></td>
            <td><input type="text" name="doc_name" required maxlength="140"></td>
        </tr>
        <tr>
            <td><b>Document number</b></td>
            <td><input type="text" name="doc_number" maxlength="150"></td>
        </tr>
        <tr>
            <td><b>Start date</b></td>
            <td><input type="text" name="start_date" required class="datepicker" placeholder="yyyy-mm-dd"></td>
        </tr>
        <tr>
            <td><b>Expire</b></td>
            <td><input type="text" name="expire_date" required class="datepicker" placeholder="yyyy-mm-dd"></td>
        </tr>
        <tr>
            <td><b>Warning interval</b></td>
            <td><input type="text" name="alarm" required size="5" maxlength="4"> days</td>
        </tr>
        <tr>
            <td><b>Person incharge</b></td>
            <td><?php echo select_user($_SESSION['uid'],'name="user"');?></td>
        </tr>
        </table>
        <p>
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
        Send this file: <input name="userfile" type="file">
        <div align="right" width="100%" style="padding: 10px">
            <input type="submit" value="Add document" onclick="return check_file(this)">
            <input type="button" value="Close" onclick="cancel()"> 
        </div>
    </form>
</div>

<div id="main_div_menu"></div>

<?php include 'footer.php';?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_docs.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", show_docs_table(1));
</script>