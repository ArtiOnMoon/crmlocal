<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
$params= json_decode($_POST['params']);
$headers='reuiqred name="'.$params[0].'" class="'.$params[2].'"';
if ($params[1]!='')$headers.=' id="'.$params[1].'"';
if ($_POST['flag']==1){
    echo select_customer ($_POST['elem_id'], $_POST['type'],$headers, $_POST['cat'],1);
    ?>
    <a style="margin-left: 2em;" href="#" onclick="display('new_company')">New customer</a>
    <?php
}
elseif ($_POST['flag']==2){
    echo select_vessel($_POST['elem_id'], $headers,$_POST['type'],1);
    ?>
    <a style="margin-left: 2em;" href="#" onclick="display('new_vessel')">New vessel</a>
    <?php
}