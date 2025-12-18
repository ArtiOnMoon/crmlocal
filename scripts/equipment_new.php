<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security ();
if (!access_check([],[],1)) exit ('Access denied');
$vessel_id=clean($_POST['new_vessel']);

$db= db_connect();
$db->autocommit(false);
$length=count($_POST['sfd_equip_id']);
for ($i=0;$i<$length;$i++){
    $check_date = ($_POST['date_check'][$i]=='' ? date('Y-m-d') : clean($_POST['date_check'][$i]));
    $expire_date = ($_POST['date_expire'][$i]=='' ? 'NULL' : '"'.clean($_POST['date_expire'][$i]).'"');
    $query='INSERT INTO vessel_equipment SET '
            . 'vessel_id="'.$vessel_id.'",'
            . 'nmnc_id="'.clean($_POST['sfd_equip_id'][$i]).'",'
            . 'serial="'.clean($_POST['serial'][$i]).'",'
            . 'note="'.clean($_POST['note'][$i]).'",'
            . 'check_date="'.$check_date.'",'
            . 'equip_modified_by="'.$_SESSION['uid'].'",'
            . 'expire_date='.$expire_date.'';
    if(!$db->query($query)){
        echo $query;
        echo 'Unable to set new equipment.<br>Error: ',$db->error;
        $db->rollback();
        $db->close();
        exit(); 
    }
}
//Все прошло успешо
$db->commit();
$db->close();
exit('true');
