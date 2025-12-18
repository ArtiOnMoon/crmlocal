<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/selector.php';
$manuf_list=get_manufacturers_list(1);
$stock_cat_list=get_stock_category_list(1);
?>
<div class="window_internal" style="width:1280px;height:720px">
    <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
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
    <h1 align="center">Add vessel equipment</h1>
    <!-- hidden elem -->
    <table style="display:none;">
        <tr id="t_row">
            <td><?php echo selector_equip_long($stock_cat_list,$manuf_list);?><a href="#" style="font-size:larger;font-weight: bold;line-height: 1.15;" onclick="service_add_equipment(this)">&#xFF0B;</a></td>
            <td><input type="text" name="serial[]" maxlength="30" size="10"></td>
            <td><input type="text" name="date_check[]" size="9" class="datepicker" value="<?php echo date('Y-m-d');?>"></td>
            <td><input type="text" name="date_expire[]" size="9" class="datepicker"></td>
            <td><input type="text" name="note[]" size="30" maxlength="1000"></td>
            <td><a class="button red_button" href="#" onclick="delete_row(this)">Delete</a></td>
            <td><a class="button" href="#" onclick="table_copy_line(this)">Copy</a></td>
        </tr>       
    </table>
    <!-- END hidden elem -->
    <form onsubmit="return vessel_equipment_send_form(this)">
        
    <?php
    
    $vessel_id=clean($_POST['vessel_id']);
    $db =  db_connect();
    $query= 'SELECT * FROM vessels WHERE vessel_id = "'.$vessel_id.'"';
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        echo '<div style="padding:5px;background:#EEE;margin-bottom:5px;"><strong>Vessel:</strong>';
        echo selector('vessels','name="new_vessel"',$row['vessel_id']),'</div>';
    } else {
        $row=$result->fetch_assoc();
        $vessel_name=$row['vessel_name'];
        echo '<h3> m\v '.$row['vessel_name'].'</h3>';
        echo 'IMO: '.$row['imo'];
        echo '<input type="hidden" name="new_vessel" value="'.$vessel_id.'">';
    }
    ?>
    <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
        <thead>
            <th>Equipment</th><th width="100px">Serial</th><th width="100px">Date of check</th><th width="100px">Expire/replace</th><th width="200px">Note</th><th width="100px">Delete</th><th>Copy</th>
        </thead>
        <tbody id="equipment_content">
        <tr>
            <td><?php echo selector_equip_long($stock_cat_list,$manuf_list);?><a href="#" style="font-size:larger;font-weight: bold;line-height: 1.15;" onclick="service_add_equipment(this)">&#xFF0B;</a></td>
            <td><input type="text" size="10" name="serial[]" maxlength="30" size="20"></td>
            <td><input type="text" size="9" name="date_check[]" class="datepicker" value="<?php echo date('Y-m-d');?>"></td>
            <td><input type="text" size="9" name="date_expire[]" class="datepicker"></td>
            <td><input type="text" name="note[]" size="30" maxlength="1000"></td>
            <td><a class="button red_button" href="#" onclick="delete_row(this)">Delete</a></td>
            <td><a class="button" href="#" onclick="table_copy_line(this)">Copy</a></td>
        </tr> 
        </tbody>
    </table> 
    <div align="right" width="100%" style="padding: 10px">
        <input type="button" value="New line" onclick="equipment_new_line()"><p>
        <input type="submit" class="green_button" value="Save">
        <input type="button" value="Close" onclick="window_close(this)"> 
        <input type="hidden" name="content" id="content">
        <input type="hidden" name="return_path" value="window">
    </div>
    </form>
</div>