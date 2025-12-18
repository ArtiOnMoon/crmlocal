<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/invoice_fns.php';
require_once '../functions/selector.php';
//$cur_list=get_currency_list();
//$comp_list = get_our_companies_list(1);
$order_list = get_order_types();

$type=clean($_POST['type']); //01 - service 02 - sales 03 - PO
$comp_id=clean($_POST['comp_id']);
$number=clean($_POST['number']);

$db =  db_connect();
$query='SELECT form ';
