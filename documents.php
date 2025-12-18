<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/doc_fns.php';
do_page_header('Documents and forms');
?>
<link rel="stylesheet" type="text/css" href="css/tabs.css">
<div id="wrap" onclick="cancel()"></div>
<div id="main_div_menu2">
<div class="tab_bar">
  <button class="tab_button selected_tab" tab="docs" tab_name="Documents" onclick="openTab(this)">Documents</button>
  <button class="tab_button" tab="forms" tab_name="Forms" onclick="openTab(this)">Forms</button>
  <button class="tab_button" tab="contracts" tab_name="Contracts" onclick="openTab(this)">Contracts</button>
  <button class="tab_button" tab="procs" tab_name="Procedures" onclick="openTab(this)">Procedures</button>
  <button class="tab_button" tab="insts" tab_name="Instructions" onclick="openTab(this)">Instructions</button>
</div>
  <table width="100%">
      <tr>
          <td width="15%" align="left"><a class="knopka" href="#" onclick="display('new_form')">New form</a></td>
          <td width="70%" align="center"><h2><span id="docs_header"></span></h2></td>
          <td width="15%" align="right">Search <input type="search" id="keyword" oninput="load_documents()"></td>
      </tr>
  </table>
<!--DOCUMENTS-->
<div id="docs" class="tab">
    <div id="docs_div" class="tab_content"></div>
</div>
<!--FORMS-->
<div id="forms" class="tab" style="display:none" onclick="tr_to_link(event,'forms')">
</div>
<!--Contracts-->
<div id="contracts" class="tab" style="display:none">
</div>
<!--PROCEDURES-->
<div id="procs" class="tab" style="display:none">
  <h2>Procedures</h2>
  <p>Tokyo is the capital of Japan.</p>
</div>
<!--INSTRICTIONS-->
<div id="insts" class="tab" style="display:none">
  <h2>Instructions</h2>
  <p>test test.</p>
</div>
    
    
</div>

<!--NEW_FORM-->
<div class="hidden" id="new_form" style="width: 400px; height: 400px;">
    <h2 align="center">Add new document</h2>
    <form enctype="multipart/form-data" action="/scripts/docs_new_form.php" method="POST">
        <table width="100%" align="center" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Category</b></td>
            <td><?php echo select_docs_type(); ?></td>
        </tr>
        <tr>
            <td><b>Name</b></td>
            <td><input type="text" name="name" required maxlength="100"></td>
        </tr>
        <tr>
            <td><b>Description</b></td>
            <td><input type="text" name="description" required maxlength="500"></td>
        </tr>
        </table>
        <p>
        <input type="hidden" name="MAX_FILE_SIZE" value="3000000">
        Send this file: <input name="userfile" type="file">
        </p>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add document" onclick="return check_file(this)">
        <input type="button" value="Close" onclick="cancel()"> 
    </div>
    </form>
</div>

<!--DOWNLOAD_FORM-->
<form id="download_file" method="POST" action="/scripts/docs_download_file.php">
    <input type="hidden" id="type" name="type">
    <input type="hidden" id="id" name="id">
    <input type="hidden" id="file_name" name="file_name">    
</form>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/docs_forms.js"></script>