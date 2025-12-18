<?php
require_once 'functions/stock_fns.php';
require_once 'functions/fns.php';
startSession();
security ();
if(check_access('acl_stock', 1)) exit('Access denied.');
$id=$_GET['id'];
if (isset($_POST['id'])) $id=$_POST['id'];
$db =  db_connect();
$query= 'select * from for_order where order_id = "'.$id.'" and order_deleted=0';
$result=$db->query($query);
if ($result-> num_rows!==1) exit('Nothing found.');
?>
<div style="width: 1024px; align-content: left; text-align:left; display: inline-block; padding:10px">
<h1><?php echo $id;?></h1>
<form action="order_change.php" method="POST">
    <table>
        
    </table>    
</form>
</div>