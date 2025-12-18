<?php
require_once 'functions/fns.php';
require_once 'functions/doc_fns.php';
startSession();
security();
if(check_access('acl_service', 2)) exit('Access denied.');
$db =  db_connect();
$query='insert vessels set '
        . 'company="'.clean($_POST['new_customer']).'", '
        . 'vessel_name="'.clean($_POST['new_vessel_name']).'", '
        . 'ship_manager="'.clean($_POST['ship_manager']).'", '
        . 'ship_operator="'.clean($_POST['ship_operator']).'", '
        . 'flag="'.clean($_POST['new_flag']).'", '
        . 'imo="'.clean($_POST['IMO']).'", '
        . 'mmsi="'.clean($_POST['mmsi']).'", '
        . 'call_sign="'.clean($_POST['call_sign']).'", '
        . 'vessel_type="'.clean($_POST['vessel_type']).'", '
        . 'class_societies="'.clean($_POST['class_societies']).'", '
        . 'vessel_note="'.clean($_POST['vessel_note']).'", '
        . 'vessel_mail_1="'.clean($_POST['vessel_mail_1']).'", '
        . 'vessel_mail_2="'.clean($_POST['vessel_mail_2']).'", '
        . 'vessel_mob_1="'.clean($_POST['vessel_mob_1']).'", '
        . 'vessel_mob_2="'.clean($_POST['vessel_mob_2']).'", '
        . 'vessel_inmarsat_1="'.clean($_POST['vessel_inmarsat_1']).'", '
        . 'vessel_inmarsat_2="'.clean($_POST['vessel_inmarsat_2']).'", '
        . 'vessel_contacts="'.clean($_POST['new_vessel_contacts']).'"';
if ($db->query($query)){
    if ($_POST['return-path']==='window') exit('true');
    else header('Location: /vessels.php');
}
else {
    if ($_POST['return-path']==='window')exit($db->error);
    echo '<font color="red">Problem: </font>'.$db->error;
    echo '<meta http-equiv="Refresh" content="5; url=/customers.php">';
    echo '<p>You will be redirected to previous page in 5 seconds...';
}