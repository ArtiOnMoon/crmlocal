<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/service.php';
require_once 'functions/message_fns.php';
//if(check_access('acl_invoices', 2)) exit('Access denied.');

$page_title = 'Start page';
include 'header.php';


?>
<main id="main_div_menu">Start page</main>
<?php 
include 'footer.php';