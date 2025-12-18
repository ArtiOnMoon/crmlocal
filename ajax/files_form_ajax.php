<?php
require_once '../PATHS.php';
require_once '../functions/main.php';
require_once '../functions/files_func.php';
require_once '../functions/db.php';
require_once '../classes/Order_name_engine.php';

//$comp_id= clean($_POST['comp_id']);
$number= clean($_POST['number']);
//$type=clean($_POST['type']);

$db =  db_connect();

$on = new Order_name_engine();
$on -> init($db);
$on -> resolve_order($number);
$on -> resolve_id($db);

$files_type = ['SR'=>'service', 'SL'=>'sales', 'PO' =>'purchase', 'CT' => 'contracts','AD'=>'adm'];

//Получение ID
//if ($type=='sales'){
//    $db =  db_connect();
//    $query='SELECT sales_id FROM sales WHERE sales_our_comp="'.$comp_id.'" AND sales_no="'.$number.'"';
//    if (!$result=$db->query($query)){
//        exit($db->error);
//    }
//    $row=$result->fetch_assoc();
//    $id=$row['sales_id'];
//    //echo 'ID:'.$row['sales_id'];
//}
//elseif($type=='service'){
//    $db =  db_connect();
//    $query='SELECT service_id FROM service WHERE service_our_comp="'.$comp_id.'" AND service_no="'.$number.'"';
//    if (!$result=$db->query($query)){
//        exit($db->error);
//    }
//    $row=$result->fetch_assoc();
//    $id=$row['service_id'];
//    //echo 'ID:'.$row['service_id'];
//}
//elseif($type=='purchase'){
//    $db =  db_connect();
//    $query='SELECT po_id FROM purchase WHERE po_our_comp="'.$comp_id.'" AND po_no="'.$number.'"';
//    if (!$result=$db->query($query)){
//        exit($db->error);
//    }
//    $row=$result->fetch_assoc();
//    $id=$row['po_id'];
//    //echo 'ID:'.$row['po_id'];
//}
//elseif($type=='contracts'){
//    $id=$number;
//    //echo 'ID:'.$row['po_id'];
//}
//else $id=$number;

$id = $on -> id;
$type = $files_type[$on->type];

$dir=get_file_folder($id, $type, $uploads_folder);
//echo 'DIR:'.$dir;
$list=array_slice(scandir($dir),2);
?>
<form method="POST" multipart="" id="files_upload_form" enctype="multipart/form-data">
    <input type="hidden" id="files_upload_form_comp_id" value="<?php echo $comp_id;?>">
    <input type="hidden" id="files_upload_form_number" value="<?php echo $number;?>">
    <input type="hidden" id="files_upload_form_type" value="<?php echo $type;?>">
<table width="100%" border="0" cellspacing = "0" cellpadding="2px">
<?php
foreach ($list as $file){
?>
    <tr>
        <td><?php echo $file; ?></td>
        <td><a class="knopka" href="#" onclick="download_file('<?php echo addslashes($file); ?>',this)">Download file</a></td>
        <td><a class="knopka" href="#" onclick="delete_file('<?php echo addslashes($file); ?>',this)">Delete file</a></td>
    </tr>
<?php
}
?>
</table>
<br>
    <input type="file" name="service_files[]" multiple>
    <input type="hidden" name="file_type" value="<?php echo $type;?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" value="Add files">
</form>
<form id="blankform" action="/scripts/files_manage.php" method="POST">
    <input type="hidden" name="file_name" id="file_name">
    <input type="hidden" name="file_id" value="<?php echo $id; ?>">
    <input type="hidden" name="file_action" id="file_action">
    <input type="hidden" name="file_type" value="<?php echo $type;?>">
</form>