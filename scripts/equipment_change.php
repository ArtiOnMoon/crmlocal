<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
startSession();
security ();
if (!access_check([],[],1)) exit ('Access denied');
$id=$_POST['id'];
$db =  db_connect();

if (isset($_POST['delete'])){
    $query='update vessel_equipment set equip_deleted=1 where id="'.$id.'"';
    $result=$db->query($query);
    if ($result) {
        echo 'true';
    }
    else{
        echo '<p>Error: </p>'; echo $db->error;
    }
    $db->close();
    exit();
}

if ($_POST['check_date']=='') $check_date='NULL'; else $check_date='"'.$_POST['check_date'].'"';
if ($_POST['expire_date']=='') $expire_date='NULL'; else $expire_date='"'.$_POST['expire_date'].'"';
$query= 'UPDATE vessel_equipment SET '
        . 'nmnc_id="'.clean($_POST['nmnc_id']).'",'
        . 'check_date='.$check_date.', '
        . 'expire_date='.$expire_date.', '
        . 'note="'.clean($_POST['note']).'",'
        . 'vessel_id="'.clean($_POST['new_vessel']).'",'
        . 'equip_modified_by="'.$_SESSION['uid'].'",'
        . 'serial="'.($_POST['serial']).'" '
        . 'WHERE id="'.$id.'"';
$result=$db->query($query);
if ($result) {
    $db->close();
    echo 'true';
}
else{
    echo '<p>Error:</p>'; echo $db->error;
    $db->close();
}