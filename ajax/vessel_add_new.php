<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/selector.php';
startSession();
?>
<div class="window_internal" style="width:1024px;height:650px;">
    <h2 align="center">Add vessel</h2>
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<div class="block_div2">
    <form name="new_vessel_form" method="POST" onsubmit='return add_new_vessel_ajax(this);'>
        <input type="hidden" name="return-path" value="window">
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
                <tr>
                    <td><b>Vessel name</b></td><td>
                    <input required type="text" name="new_vessel_name"></td>
                </tr>
                <tr>
                    <td><b>IMO</b></td><td>
                    <input type="text" name="IMO" maxlength="7"></td>
                </tr>
                <tr>
                    <td><b>MMSI</b></td><td>
                    <input type="text" name="mmsi" maxlength="9"></td>
                </tr>
                <tr>
                    <td><b>Call Sign</b></td><td>
                    <input type="text" name="call_sign" maxlength="30"></td>
                </tr>
                <tr>
                    <td><b>Ship type</b></td><td>
                    <input type="text" name="vessel_type" maxlength="30"></td>
                </tr>
                <tr>
                    <td><b>Owner</b></td>
                    <td><?php echo selector('customers','name="new_customer"',1,'ownr');?>
                </tr>
                <tr>
                    <td><b>Manager company</b></td>
                    <td><?php echo selector('customers','name="ship_manager"',1,'mngr');?>
                </tr>
                <tr>
                    <td><b>Operator</b></td>
                    <td><?php echo selector('customers','name="ship_operator"',1,'optr');?>
                </tr>
                <tr>
                    <td><b>Flag</b></td>
                    <td><?php select_country(0, 'name="new_flag"',2)?></td>
                </tr>
                <tr>
                    <td><b>Class society</b></td><td>
                    <input type="text" name="class_societies" maxlength="100"></td>
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
                    <td><b>Inmarsat number</b></td>
                    <td>
                        <input type="text" name="vessel_inmarsat_1" maxlength="100" value="<?php echo $row['vessel_inmarsat_1'];?>"><br>
                        <input type="text" name="vessel_inmarsat_2" maxlength="100" value="<?php echo $row['vessel_inmarsat_2'];?>">
                    </td>
                </tr>
                <tr>
                    <td><b>Additional contacts</b></td>
                    <td><textarea name="new_vessel_contacts" maxlength="500" cols="50" rows="2"></textarea></td>
                </tr>
                <tr>
                    <td><b>Note</b></td>
                    <td><textarea name="vessel_note" maxlength="500" cols="50" rows="2"></textarea></td>
                </tr>
            </table>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" class="green_button" value="Save"> 
        <input type="button" value="Close" onclick="window_close(this);">
    </div>
    <div id="vessel_status" align="center" width="100%" style="padding: 10px"></div>
    </form>
</div>
</div>