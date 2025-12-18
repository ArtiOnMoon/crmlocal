<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'classes/Adm.php';

$adm = new Adm();
$user_list = get_user_list();

$page_title = 'Administrative';
include 'header.php';
if(check_access('acl_sales', 1)) exit('Access denied.');
$cur_list=get_currency_list();
?>
<div id="side_menu">
    <a class="knopka" href="#"  onclick="adm_new()">New order</a>
    <label></label>
    <div class="menu_container">
        <label class="caption_label">Company<br /><?php select_our_company($_SESSION['default_company'], 'id="adm_our_company" onchange="adm_dispay(1)"',0)?></label> 
    </div>
    <div class="menu_container">
        <label class="caption_label">Status<br />
            <?php echo select_from_list($adm->status_list, 0, 'id="adm_status" onchange="adm_dispay(1)"',1); ?></label> 
    </div>
    <div class="menu_container">
        <label class="caption_label">Incharge<br />
            <?php echo select_from_list($user_list, 0, 'id="adm_incharge" onchange="adm_dispay(1)"',1); ?></label> 
    </div>
    <div class="menu_container">
        <label class="caption_label">Customer</label><br />
        <?php  echo selector('customers','id="adm_customer" onchange="adm_dispay(1)"',0);?>
    </div>
    <div class="menu_container">
        <label class="caption_label">Search<br /><input oninput="adm_dispay(1)" type="search" id="adm_search"></label> 
    </div>
</div>

<div id="main_div_menu"></div>

<?php include 'footer.php';?>
<link rel="stylesheet" type="text/css" href="css/new_table.css">
<script>
document.addEventListener("DOMContentLoaded", adm_dispay(1));
</script>