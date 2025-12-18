<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
startSession();
security();
if(check_access('acl_service', 1)) exit('Access denied.');

$db =  db_connect();
$query= 'insert into service_rates set '
        . 'rate_cat = "'.clean($_POST['service_rates_category']).'", '
        . 'rate_name = "'.clean($_POST['rate_name']).'", '
        . 'rate_currency = "'.clean($_POST['currency']).'", '
        . 'rate_price = "'. clean($_POST['rate_price']).'"';
if ($db->query($query)) {
    header('Location: /service_rates.php');
}
else{
    exit($db->error);
}