<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
startSession();
security();
if(check_access('acl_service', 2)) exit('Access denied.');
$db =  db_connect();
$query= 'insert into service_rates_cat set '
        . 'rate_our_comp = "'.clean($_POST['rate_our_comp']).'", '
        . 'rate_cat_name = "'.clean($_POST['rate_cat_name']).'"';
if ($db->query($query)) {
    header('Location: /service_rates.php');
}
else{
    exit($db->error);
}