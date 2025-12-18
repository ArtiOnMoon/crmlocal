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
<!--MAIN BLOCK-->
<div id="main_block">
</div>    
</div>


<!--DOWNLOAD_FORM-->
<form id="download_file" method="POST" action="/scripts/docs_download_file.php">
    <input type="hidden" id="type" name="type">
    <input type="hidden" id="id" name="id">
    <input type="hidden" id="file_name" name="file_name">    
</form>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/docs_forms.js"></script>