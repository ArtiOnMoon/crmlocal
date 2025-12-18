<?php
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/main.php';
startSession();
security ();

//if (!access_check([],[],1)) exit ('Access denied');
$db =  db_connect();
$query= 'INSERT INTO service_equipment SET '
        . 'srv_eq_cat = "'.clean($_POST['srv_eq_cat']).'", '
        . 'srv_eq_manuf = "'.clean($_POST['srv_eq_manuf']).'", '
        . 'srv_eq_name = "'.clean($_POST['srv_eq_name']).'"';
if ($db->query($query)){
    echo 'Added successfully';
}
else {
    echo '<font color="red">Problem: </font>'.$db->error;;   
}