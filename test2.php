<?php
require_once 'functions/auth.php';
require_once 'functions/main.php';
require_once 'functions/tasks_fns.php';
require_once 'functions/db.php';
require_once 'classes/Task.php';
require_once 'classes/Order_name_engine.php';

$db =  db_connect();

$order_engine = new Order_name_engine();
$order_engine -> init($db);

$order_engine -> resolve_order('SR-MSS-02126');
//echo $order_engine ->get_order();
//echo $order_engine -> type;
//echo $order_engine -> comp_id;
//echo $order_engine -> num;
$order_engine->resolve_id($db);
//print_r($order_engine);
echo '<br>ID: '.$order_engine->id;
echo is_null($order_engine->id);
