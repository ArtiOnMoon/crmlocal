<?php
require_once './functions/fns.php';
require_once 'functions/selector.php';
startSession();
security ();
if(check_access('acl_service', 1)) exit('Access denied.');

$id=$_POST['id'];
$manuf_list=get_manufacturers_list(1);
$stock_cat_list=get_stock_category_list(1);

?>
<div class="window_internal" style="width:900px;height:400px">
    <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<h1>Vessel Equipment #<?php echo $id;?></h1>
<?php
$db =  db_connect();
$query= 'SELECT vessel_equipment.*, full_name FROM vessel_equipment '
        . 'LEFT JOIN users ON equip_modified_by=uid '
        . 'WHERE id = "'.$id.'" and equip_deleted=0';
$result=$db->query($query);
if ($result-> num_rows!==1){ exit('Nothing found'); }
$row=$result->fetch_assoc();
?>
<div class="srv_add_eq_div" id="service_add_eq_div">
    <form id="service_add_eqipment_form" onsubmit="return service_add_equipment_save()">
    <table>
        <tr>
            <td>Category</td><td><?php echo select_array($stock_cat_list, 0, 'required name="srv_eq_cat" id="new_equipment_cat"');?></td>
            <td>Manufacturer</td><td><?php echo select_array($manuf_list, 0, 'required name="srv_eq_manuf" id="new_equipment_manuf"');?></td>
        </tr>
        <tr><td>Type\Model</td><td colspan="3"><input type="text" required name="srv_eq_name" style="width:100%;"></td></tr>
        <tr><td colspan="4" align="center"><input class="green_button" type="submit" value="Save">
            <input type="button" value="Close" onclick="this.closest('#service_add_eq_div').style.display='none'">
        </tr>
    </table>
    </form>
</div>
<form onsubmit="return vessel_equipment_change(this)">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div style="display:inline-block; width:50%">
    <table width="100%" border="1px">
        <tr>
            <td><b>Vessel</b></td>
            <td><?php echo select_vessel($row['vessel_id']);?></td>
        </tr>
        <tr>
            <td><b>Equipment</b></td>
            <td><?php echo selector_equip_long($stock_cat_list,$manuf_list,'name="nmnc_id"',$row['nmnc_id']);?><a href="#" style="font-size:larger;font-weight: bold;line-height: 1.15;" onclick="service_add_equipment(this)">&#xFF0B;</a></td>
        </tr>
         <tr>
            <td><b>Serial</b></td>
            <td><input type="text" maxlength="20" name="serial" value="<?php echo $row['serial'];?>"></td>
        </tr>
        <tr>
            <td><b>Check date</b></td>
            <td><input type="text" name="check_date" class="datepicker" value="<?php echo $row['check_date'];?>"></td>
        </tr>
        <tr>
            <td><b>Expire\replace</b></td>
            <td><input type="text" name="expire_date" class="datepicker" value="<?php echo $row['expire_date'];?>"></td>
        </tr>
        <tr>
            <td><b>Note</b></td>
            <td><textarea name="note" rows="5" cols="100"><?php echo $row['note'];?></textarea></td>
        </tr>
    </table>
    </div>
    <br><br>
    <input type="submit" class="green_button" value="Save changes">
</form><p>
<form action="/scripts/equipment_change.php" method="POST" onsubmit="return check_delete()">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" class="red_button" name="delete" value="Delete">
</form>
<br>
<span style="font-style: italic">Last modified by: <?php echo $row['full_name']; ?> at <?php echo $row['equip_modified']; ?></span>
</div>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>