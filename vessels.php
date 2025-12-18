<?php
require_once 'functions/fns.php';
require_once 'functions/selector.php';
startSession();
security();
if(check_access('acl_service', 1)) exit('Access denied.');

$page_title = 'Vessels';
include 'header.php';
?>
<div id="invis_wrap" onclick="close_menu()"></div>

<div id="side_menu">
    <button class="button" onclick="vessel_new()">Add new vessel</button> 
    <!-- FILTERS BLOCK -->
    <span class="hidden_conteiner" onclick="display_menu('hidden_div','list_sign')">Show filters <span id="list_sign" class="sign">&#9660</span></span>
        <div class="hidden_div" id="hidden_div">
        <span style="padding-right:2em">Owner <?php echo select_customer('',1,'id="owner" class="combobox"','ownr'); ?></span>
        <span style="padding-right:2em">Agent <?php echo select_customer('',1,'id="agent" class="combobox"','agnt'); ?></span>
        <span style="padding-right:2em">Operator <?php echo select_customer('',1,'id="operator" class="combobox"','optr'); ?></span>
    </div>
    <!-- END FILTERS BLOCK -->
    <span> Keyword: <input type="search" id="vessel_search" placeholder = "Search..." oninput="show_vessel_table(1)"></span>
    <button class="button" onclick="show_vessel_table(1)">Filter</button> 
    <button class="button" onclick="vessel_reset_filters()">Reset filter</button>
</div>

<main id="main_div_menu">Loading..</main>

<?php include 'footer.php';?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_vessels.js"></script>
<script type="text/javascript" src="java/java_equipment_func.js"></script>
<script type="text/javascript" src="java/selector.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script>document.addEventListener("DOMContentLoaded", show_vessel_table('1')); </script>