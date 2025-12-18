<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
startSession();
security();
if(check_access('acl_full_access', 1)) exit('Access denied.');
$db =  db_connect();
if (clean($_POST['delete'])==='1') {
    $query= 'delete from service_rates where rate_id='.clean($_POST['rate_id']);
    if ($db->query($query)) {
        header('Location: /service_rates.php');
    }
    else{
        exit($db->error);
    }
}
$query= 'update service_rates set '
        . 'rate_cat = "'.clean($_POST['service_rates_category']).'", '
        . 'rate_name = "'.clean($_POST['rate_name']).'", '
        . 'rate_price = "'. clean($_POST['rate_price']).'", '
        . 'rate_currency = "'. clean($_POST['currency']).'" '
        . 'where rate_id="'.clean($_POST['rate_id']).'"';
if ($db->query($query)) {
    header('Location: /service_rates.php');
}
else{
    exit($db->error);
}