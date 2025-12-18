<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
startSession();
security();
if(check_access('acl_full_access', 2)) exit('Access denied.');
$db =  db_connect();
if (clean($_POST['delete'])==='1') {
    $query= 'select rate_id from service_rates where rate_cat='.clean($_POST['rate_cat_id']);
    $result=$db->query($query);
    if ($result->num_rows>0)exit('There is some rates in this category. You must change\delete them first.');
    $query= 'delete from service_rates_cat where rate_cat_id='.clean($_POST['rate_cat_id']);
    if ($db->query($query)) {
        header('Location: /service_rates.php');
    }
    else{
        exit($db->error);
    }
}
$query= 'update service_rates_cat set '
        . 'rate_cat_name = "'.clean($_POST['rate_cat_name']).'" '
        . 'where rate_cat_id="'.clean($_POST['rate_cat_id']).'"';
if ($db->query($query)) {
    header('Location: /service_rates.php');
}
else{
    exit($db->error);
}