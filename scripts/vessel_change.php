<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security();

//if(check_access('acl_service', 2)) exit('Access denied.');
$db =  db_connect();
if (isset($_POST['vessel_deleted'])){
    $query= 'UPDATE vessels SET vessel_deleted="1" WHERE vessel_id="'.clean($_POST['vessel_id']).'"';
    
}
else{
    $query= 'update vessels set '
        . 'vessel_name = "'.clean($_POST['new_vessel_name']).'", ' 
        . 'vessel_contacts="'.clean($_POST['new_vessel_contacts']).'", '
        . 'flag="'.clean($_POST['new_flag']).'", '
        . 'imo="'.clean($_POST['imo']).'", '
        . 'mmsi="'.clean($_POST['mmsi']).'", '
        . 'captain="'.clean($_POST['captain']).'", '
        . 'vessel_mail_1="'.clean($_POST['vessel_mail_1']).'", '
        . 'vessel_mail_2="'.clean($_POST['vessel_mail_2']).'", '
        . 'vessel_mob_1="'.clean($_POST['vessel_mob_1']).'", '
        . 'vessel_mob_2="'.clean($_POST['vessel_mob_2']).'", '
        . 'vessel_inmarsat_1="'.clean($_POST['vessel_inmarsat_1']).'", '
        . 'vessel_inmarsat_2="'.clean($_POST['vessel_inmarsat_2']).'", '
        . 'call_sign="'.clean($_POST['call_sign']).'", '
        . 'vessel_type="'.clean($_POST['vessel_type']).'", '
        . 'class_societies="'.clean($_POST['class_societies']).'", '
        . 'company="'.clean($_POST['new_customer']).'", '
        . 'vessel_note="'.clean($_POST['vessel_note']).'", '
        . 'ship_operator="'.clean($_POST['ship_operator']).'", '
        . 'ship_manager="'.clean($_POST['ship_manager']).'" '
        . 'where vessel_id="'.clean($_POST['vessel_id']).'"';
}
if ($db->query($query)) echo 'true';  
else {
    echo '<p><font color="red">FAILED:</fond></p>'.$db->error;
}