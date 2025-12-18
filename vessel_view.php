<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';
require_once 'PATHS.php';
require_once 'classes/Order_name_engine.php';

startSession();
security ();
if (!access_check([],[],1)) exit ('Access denied');

$id=$_POST['id'];
$db =  db_connect();
$query= 'select * from vessels where vessel_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1) exit('<font color="red">Nothing found </font>');
$row=$result->fetch_assoc();
$vessel_name=$row['vessel_name'];
?>
<div class="window_internal" style="width:1280px;height:720px">
<h1>Vessel <?php echo $id;?></h1>
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<div class="block_div">
    <div class="tab_bar">
        <button class="tab_button selected_tab" tab="vessel" onclick="openTab(this)">Vessel Info</button>
        <button class="tab_button" tab="equipment" onclick="openTab(this)">Vessel equipment</button>
        <a class="tab_button" tab="uploaded_files" onclick="openTab(this)" href='#'>Uploaded files</a>
    </div>
<div id="vessel" class="tab" style="display:block">
    <div class="block_div2 calc_fancy_container" style="display:inline-block; width:900px; height: 580px;">
        <form method="POST" onsubmit="return vessel_change(this)">
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Vessel name</b></td>
            <td><input type="text" name="new_vessel_name" value="<?php echo $row['vessel_name'];?>"></td>
        </tr>
        <tr>
            <td><b>IMO</b></td>
            <td><input type="text" name="imo" maxlength="7" value="<?php echo $row['imo'];?>">
                <?php echo marine_traffic_link($row['imo']); ?>
            </td>
        </tr>
        <tr>
            <td><b>MMSI</b></td>
            <td><input type="text" name="mmsi" maxlength="9" value="<?php echo $row['mmsi'];?>"></td>
        </tr>
        <tr>
            <td><b>Class society</b></td>
            <td><input type="text" name="class_societies" maxlength="100" value="<?php echo $row['class_societies'];?>"></td>
        </tr>
        <tr>
            <td><b>Call Sign</b></td>
            <td><input type="text" name="call_sign" maxlength="30" value="<?php echo $row['call_sign'];?>"></td>
        </tr>
        <tr>
            <td><b>Vessel type</b></td>
            <td><input type="text" name="vessel_type" maxlength="30" value="<?php echo $row['vessel_type'];?>"></td>
        </tr>
        <tr>
            <td><b>Owner</b></td>
            <td><?php echo selector('customers','name="new_customer"',$row['company'],'ownr');?>
        </tr>
        <tr>
            <td><b>Manager company</b></td>
            <td><?php echo selector('customers','name="ship_manager"',$row['ship_manager'],'mngr');?>
        </tr>
        <tr>
            <td><b>Operator</b></td>
            <td><?php echo selector('customers','name="ship_operator"',$row['ship_operator'],'optr');?>
        </tr>
        <tr>
            <td><b>Flag</b></td>
            <td><?php echo select_country($row['flag'],'name="new_flag"',2);?></td>
        </tr>
        <tr>
            <td><b>Vessel e-mail</b></td>
            <td>
                <input type="text" name="vessel_mail_1" maxlength="100" value="<?php echo $row['vessel_mail_1'];?>"><br>
                <input type="text" name="vessel_mail_2" maxlength="100" value="<?php echo $row['vessel_mail_2'];?>">
            </td>
        </tr>
        <tr>
            <td><b>Vessel mobile</b></td>
            <td>
                <input type="text" name="vessel_mob_1" maxlength="100" value="<?php echo $row['vessel_mob_1'];?>"><br>
                <input type="text" name="vessel_mob_2" maxlength="100" value="<?php echo $row['vessel_mob_2'];?>">
            </td>
        </tr>
        <tr>
            <td><b>Inmarsat-C number</b></td>
            <td>
                <input type="text" name="vessel_inmarsat_1" maxlength="100" value="<?php echo $row['vessel_inmarsat_1'];?>"><br>
                <input type="text" name="vessel_inmarsat_2" maxlength="100" value="<?php echo $row['vessel_inmarsat_2'];?>">
            </td>
        </tr>
        <tr>
            <td><b>Additional contacts</b></td>
            <td><textarea name="new_vessel_contacts" maxlength="500" cols="60" rows="2"><?php echo $row['vessel_contacts'];?></textarea></td>
        </tr>
        <tr>
            <td><b>Note</b></td>
            <td><textarea maxlength="500" cols="60" rows="2" name="vessel_note"><?php echo $row['vessel_note'];?></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><strong>DELETE vessel</strong> <input type="checkbox" name="vessel_deleted"></td>
        </tr>
        </table>
            <div align="center">
                <input type="submit" class="green_button" value="Save"><p>
                <input type="hidden" name="vessel_id" value="<?php echo $id;?>">
            </div>
        </form>
    </div>
<!-- SERVICES LIST --> 
    <div  class="block_div" style="display:inline-block;vertical-align: top; width:280px; height:580px;overflow:auto;">
        <h2>Services list</h2>
        <table width="100%">
            <thead>
                <th>Service id</th>
                <th>Date</th>
            </thead>
        <?php 
        $on = new Order_name_engine();
        $on ->init($db);
        $query= 'SELECT service_no, service_id, service_date, service_our_comp FROM service WHERE vessel_id = "'.$id.'"';
        $result = $db->query($query);
        while($row=$result->fetch_assoc()){
            $on -> comp_id = $row['service_our_comp'];
            $on -> num = $row['service_no'];
            $on -> type = 'SR';
            try {
                $on->get_order();
            } catch (Exception $ex) {
                break;
            }
            ?>
            <tr>
                <td><a href="#" onclick="view_link('<?php echo $on->order; ?>')"><?php echo $on->order; ?></a></td>
                <td><?php echo $row['service_date'];?></td>
            </tr>
        <?php }?>
        </table>
    </div>
</div>
<!-- Vessel Equipment-->
<div id="equipment" class="tab">
<!-- EQUIPMENT LIST -->     
<div >
    <h2 style="display: inline;">Equipment list</h2>
    <span class="space_span"></span><a href="#" class="knopka" onclick="add_new_equipment('<?php echo $id;?>')">Add new</a>
    <br><br>
</div>
<div style="overflow: auto;height:560px;">
<table width="100%">
    <thead>
        <th>Type</th>
        <th>Manufacturer</th>
        <th>Description</th>
        <th>Serial</th>
        <th>Check date</th>
        <th>Expire/replace</th>
        <th>Note</th>
    </thead>
    <tbody>
<?php 
$db =  db_connect();
$query='SELECT * FROM stock_cats WHERE for_vessel=1';
$result=$db->query($query);
while($row=$result->fetch_assoc()){
    $cats[ $row['id']] = $row['stock_cat_name'];
}
foreach ($cats as $key => $value){
    echo'<tr style="border-bottom: 1px solid #EEE;"><td colspan="7"><strong>',$value,'</strong></td></tr>';
    $query='SELECT vessel_equipment.*, mnf_short_name, srv_eq_name '
            . 'FROM vessel_equipment '
            . 'LEFT JOIN service_equipment ON nmnc_id=srv_eq_id '
            . 'LEFT JOIN manufacturers ON srv_eq_manuf=mnf_id '
            . 'WHERE vessel_id="'.$id.'" AND srv_eq_cat = "'.$key.'" AND equip_deleted=0;';
    $result=$db->query($query);
    if ($result->num_rows===0){
        echo'<tr><td colspan="6"><span class="greytext">Nothing added</span></td></tr>';
    }
    else {
    while($row=$result->fetch_assoc()){
        ?>
        <tr>
            <td><?php echo view_equipment_link('Edit',$row['id']);?></td>
            <td><?php echo $row['mnf_short_name'];?></td>
            <td><?php echo $row['srv_eq_name'];?></td>
            <td><?php echo $row['serial'];?></td>
            <td><?php echo $row['check_date'];?></td>
            <td><?php echo $row['expire_date'];?></td>
            <td><?php echo $row['note'];?></td>
        </tr>
        <?php
        }
    }
    echo '<tr><td colspan="6"> </td></tr>';
}
?>
    </tbody>
</table>
</div>
</div>
<!-- FILES -->
<div id="uploaded_files" class="tab"></div>
<!--<div id="uploaded_files" style="position:absolute; right:0px; top:30px; overflow:auto; max-height: 400px; width:30%;"></div>-->
<!-- END FILES -->
    <!-- NEW EQUIPMENT BLOCK -->
<div id="new_equipment" class="hidden" style="width:1280px;height:500px;"></div>
</div>
</div>