<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';

startSession();
security ();
if(check_access('acl_stock', 2)) exit('Access denied.');
//Check dates

if ($_POST['transfers_from_date']!=='') $transfers_from_date='"'.clean($_POST['transfers_from_date']).'"'; else $transfers_from_date='NULL';
if ($_POST['transfers_to_date']!=='') $transfers_to_date='"'.clean($_POST['transfers_to_date']).'"'; else $transfers_to_date='NULL';

$db = db_connect();
$db->autocommit(false);
$query= 'INSERT INTO transfers SET '
        . 'transfers_status="'.clean($_POST['transfers_status']).'", '
        . 'transfers_from="'.clean($_POST['transfers_from']).'", '
        . 'transfers_to="'.clean($_POST['transfers_to']).'", '
        . 'transfers_from_date='.$transfers_from_date.', '
        . 'transfers_to_date='.$transfers_to_date.', '
        . 'transfers_awb="'.clean($_POST['transfers_awb']).'", '
        . 'transfers_note="'.clean($_POST['transfers_note']).'", '
        . 'transfers_modified_by="'.$_SESSION['uid'].'"';
if (!$db->query($query)) {
    $arr = array('result' => 'false', 'id' =>'', 'error' => $db->error);
    echo json_encode($arr);
    $db->rollback();
    $db->close();
    exit();
}
$id=$db->insert_id;

//Transfers content
if(isset($_POST['stock_selected_items']))$length=count($_POST['stock_selected_items']);ELSE $length=0;
if ($length>=1){
    for ($i=0;$i<$length;$i++){
        $query='INSERT INTO transfers_content VALUES(DEFAULT,'.$id.','
        .clean($_POST['stock_selected_items'][$i]).','
        .'DEFAULT,DEFAULT)';
        if(!$db->query($query)){
            $arr = array('result' => 'false', 'id' =>$id, 'error' => $query);
            echo json_encode($arr);
            $db->rollback();
            $db->close();
            exit();
        }
    }
}

$db->commit();
$db->close();
$arr = array('result' => 'true', 'id' => $id, 'error' => '');
echo json_encode($arr);
exit();