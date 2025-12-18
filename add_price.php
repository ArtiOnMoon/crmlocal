<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_sales', 2)) exit('Access denied.');
$db =  db_connect();
$query= 'insert into price set '
        . 'class = "'.clean($_POST['stock_class']).'", '
        . 'pn = "'.clean($_POST['pn']).'", '
        . 'manufacturer_id = "'.clean($_POST['new_customer']).'", '
        . 'description = "'.clean($_POST['description']).'", '
        . 'currency = "'.clean($_POST['currency']).'", '
        . 'price = "'.clean($_POST['price']).'", '
        . 'price_discount = "'.clean($_POST['discount']).'", '
        . 'date = "'.clean($_POST['date']).'", '
        . 'user = "'.$_SESSION['uid'].'"';

$result=$db->query($query);
if (!$result) {
    echo '<font color="red">Problem: </font>'.$db->error;
    echo '<meta http-equiv="Refresh" content="5; url=/price.php">';
    echo '<p>You will be redirected to previous page in 5 seconds...';
}
    else {
    header('Location: /price.php');
}
