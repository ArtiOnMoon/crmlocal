<?php
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_stock', 2)) exit('Access denied.');
if (!isset($_POST['order_pn'])) exit('No items added!');

$db=db_connect();
$db->autocommit(false);
$i=0;
foreach ($_POST['order_pn'] as $key){
    $query='INSERT INTO for_order SET '
            . 'order_pn="'.clean($_POST['order_pn'][$i]).'", '
            . 'order_descr="'.clean($_POST['order_descr'][$i]).'", '
            . 'order_qnt="'.clean($_POST['order_qnt'][$i]).'", '
            . 'order_status="0", '
            . 'order_type="'.clean($_POST['order_type']).'", '
            . 'order_link="'.clean($_POST['order_link']).'", '
            . 'order_date="'.clean($_POST['order_date']).'", '
            . 'order_user="'.$_SESSION['uid'].'", '
            . 'order_urgency="'.clean($_POST['order_urgency']).'", '
            . 'order_note="'.clean($_POST['order_note']).'"';
    $i++;
    if(!$db->query($query)){
        echo $db->error;
        $db->rollback();
        exit();
    }
}
$db->commit();
header('Location: /for_order.php');