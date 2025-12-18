<?php
require_once 'functions/fns.php';
require_once 'functions/selector.php';
if(check_access('acl_cust', 1)) exit('Access denied.');
(int)$id=clean($_POST['id']);
//вывод ИНФО
$db =  db_connect();
$query= 'select customers.*, users.full_name from customers LEFT JOIN users ON modified_by=users.uid where cust_id = "'.$id.'" and deleted=0';
$result=$db->query($query);
if ($result-> num_rows!==1)exit('Nothing found');
    $row=$result->fetch_assoc();
?>
<style>
    .client_of_div{
            position: absolute;
            top:-33px;
            height:20px;
            right:-5px;
            background: #ffcccc;
            padding: 3px;
        }
</style>
<div class="window_internal" style="width:90%; max-width: 1280px;height:720px">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<div class="header_div">
    <h1>Customer <?php echo $id;?></h1>
</div>
<div class="block_div calc_fancy_container" style="width: 950px; height:360px; display: inline-block">
<form id="customer_form" onsubmit="return change_customer(<?php echo $_SESSION['cust_page'];?>)">
    <div class="calc_fancy_div">
        <b>Short name</b>
        <input type="text" maxlength="250" style="width:320px" name="new_comp_short_name" required value="<?php echo $row['cust_short_name'];?>">
    </div>
    <div class="calc_fancy_div">
        <b>Full name</b>
        <input required maxlength="250" style="width:320px" name="new_company_full_name" value="<?php echo $row['cust_full_name'];?>">
    </div>
    <div class="client_of_div block_div">
        <b>Client of</b>
        <?php echo select_client_of($row['client_of'],'name="client_of"') ?>
    </div>
    <div class="calc_fancy_div">
        <b>Status</b>
        <?php echo select_customer_status($row['customer_status']);?>
    </div>
    <br><!-- LINE 2 -->
    <div class="calc_fancy_div">
        <table class="calc_fancy_subtable">
            <tr><td><b>Address</b></td>
                <td><input maxlength="300" name="new_address2" style="width:300px" value="<?php echo $row['address2'];?>" placeholder="Office, street, building"></td>
            </tr>
            <tr><td></td><td><input maxlength="300" name="new_address" style="width:300px" value="<?php echo $row['address'];?>" placeholder="City, post code"></td></tr>
        </table>
    </div>
    <div class="calc_fancy_div">
        <table class="calc_fancy_subtable">
            <tr><td><b>Invoicing Address</b></td>
                <td><input maxlength="300" name="new_invoicing_address2" style="width:300px" value="<?php echo $row['InvoicingAddress2'];?>" placeholder="Office, street, building"></td>
            </tr>
            <tr><td></td><td><input maxlength="300" name="new_invoicing_address" style="width:300px" value="<?php echo $row['InvoicingAddress'];?>" placeholder="City, post code"></td></tr>
        </table>
    </div>
    <br><!-- LINE 3 -->
    <div class="calc_fancy_div">
        <b>Country</b>
        <?php echo select_country($row['country']);?>
    </div>
    <div class="calc_fancy_div">
        <b>VAT or Reg. number</b>
        <input type="text" maxlength="30" size="15" name="vat" value="<?php echo $row['vat'];?>">
    </div>
    <br><!-- LINE 4 -->
    <div class="calc_fancy_div">
        <b>Phone</b>
        <input required type="text" maxlength="30" name="new_contact_phone" value="<?php echo $row['contact_phone'];?>">
        <input type="text" maxlength="30" name="new_add_phone" value="<?php echo $row['add_phone'];?>"><br>
    </div>
    <div class="calc_fancy_div">
        <b>Fax</b>
        <input type="text" name="new_fax" maxlength="30" value="<?php echo $row['fax'];?>">
    </div>
    <div class="calc_fancy_div">
        <b>Website</b>
        <input type="text" maxlength="100" name="new_website" value="<?php echo $row['website'];?>"> 
        <a target="_blank" href="<?php echo $row['website'];?>"><img title="View website" class="line_image" align="middle" src='/icons_/ex_link.png'></a>
    </div>
    <br><!-- LINE 5 -->
    <div class="calc_fancy_div">
        <b>E-mail</b>
        <input type="text" style="width:200px" name="new_email" value="<?php echo $row['email'];?>"> <a target="_blank" href="mailto:<?php echo $row['email'];?>"><img title="Send email" class="line_image" align="middle" src='/icons_/email2.png'></a>
        <input type="text" style="width:200px" name="email2" value="<?php echo $row['email2'];?>"> <a target="_blank" href="mailto:<?php echo $row['email2'];?>"><img title="Send email" class="line_image" align="middle" src='/icons_/email2.png'></a>
        <input type="text" style="width:200px" name="email3" value="<?php echo $row['email3'];?>"> <a target="_blank" href="mailto:<?php echo $row['email3'];?>"><img title="Send email" class="line_image" align="middle" src='/icons_/email2.png'></a>
    </div>
    <br>
    <div class="calc_fancy_div">
        <b>Category</b>
        <label><input type="checkbox" name="is_mnfr" value=1 <?php if($row['is_mnfr']=='1')echo'checked';?>>Manufacturer</label>
        <label><input type="checkbox" name="is_sppl" value=1 <?php if($row['is_sppl']=='1')echo'checked';?>>Supplier</label>
        <label><input type="checkbox" name="is_serv" value=1 <?php if($row['is_serv']=='1')echo'checked';?>>Service</label>
        <label><input type="checkbox" name="is_mngr" value=1 <?php if($row['is_mngr']=='1')echo'checked';?>>Manager</label>
        <label><input type="checkbox" name="is_ownr" value=1 <?php if($row['is_ownr']=='1')echo'checked';?>>Owner</label>
        <label><input type="checkbox" name="is_optr" value=1 <?php if($row['is_optr']=='1')echo'checked';?>>Operator</label>
        <label><input type="checkbox" name="is_agnt" value=1 <?php if($row['is_agnt']=='1')echo'checked';?>>Agent</label>
        <label><input type="checkbox" name="is_fchk" value=1 <?php if($row['is_fchk']=='1')echo'checked';?>>For checking</label>
    </div>
    <br><!-- LINE 6 -->
    <div class="calc_fancy_div">
        <b>Sales discount</b>
        <input type="text" size="3" name="discount" maxlength="3" value="<?php echo $row['discount'];?>">
    </div>
    <div class="calc_fancy_div">
        <b>Service discount</b>
        <input type="text" size="3" maxlength="3" name="service_discount" value="<?php echo $row['service_discount'];?>">
    </div>
    <div class="calc_fancy_div">
        <b>Payment terms</b>
        <?php echo select_payment_terms($row['payment_terms']);?>
    </div>
    <div class="calc_fancy_div">
        <b>Credit limit</b>
        <input maxlength="10" type="text" size="10" name="credit_limit" value="<?php echo $row['credit_limit'];?>">
    </div>
    <br><!-- LINE 7 -->
    <div class="calc_fancy_div">
        <b>Note</b>
        <textarea maxlength="500" cols="120" rows="1" name="new_note" style="width:100%"><?php echo $row['note'];?></textarea>
    </div>
    <br><!-- LINE 8 -->
    <em>Last modified <?php echo $row['modified'];?> by <?php echo $row['full_name'];?></em>
<p>
    <div align="center"><input type="submit" class="green_button" value="Save"> <input type="button" Value="Cancel" onclick="window_close(this)">
    </div>
    <input type="hidden" name="company_id" value="<?php echo $row['cust_id'];?>">
    <input type="hidden" name="return-path" value="window">
</form>
<form method="POST" action="/scripts/customers_delete.php?id=<?php echo $id;?>" onclick="return check_delete()">
    <div style="float:right"><input type="submit" class="red_button" value="Delete customer"></div>
</form>
</div>

<!-- RELATED VESSELS -->
<?php if($row['is_mngr']=='1' || $row['is_ownr']=='1' || $row['is_optr']=='1'){
?>
<div class="block_div" style="width:280px; display:inline-block;float:right;">
    <table><tr><td><h2>Vessels list</h2></td><td><a class="knopka" href="#" onclick="vessel_new()">New vessel</a></td></tr></table>
    <div style="position:relative;overflow:auto; height: 305px;"">
    <table width="100%" class="sort_table">
        <thead><th>Vessel name</th><th>IMO</th></thead>
    <?php
    $query='SELECT vessel_id, vessel_name, imo, company, ship_manager, ship_operator '
            . 'FROM vessels '
            . 'WHERE (ship_manager="'.$id.'" or company="'.$id.'" or ship_operator="'.$id.'") and vessel_deleted="0" '
            . 'ORDER BY vessel_name';
    $result=$db->query($query);
    while ($row=$result->fetch_assoc()){
        echo '<tr><td>', view_vessel_link($row['vessel_name'], $row['vessel_id']),'</td><td>',marine_traffic_link($row['imo'],$row['imo']),'</td></tr>';
    }
    ?>
    </table>
    </div>
</div>
<?php
}
?>
<!-- CONTACTS  -->
<div class="block_div">
    <table><tr><td><h2>Contacts</h2></td><td><a class="knopka" href="#" onclick="display('new_contacts')">Add new contact</a></td></tr></table>
    <div style="height: 200px; overflow: auto;">
<?php
$query= 'select * from customers_contacts where customer_id = "'.$id.'" and deleted=0';
$result=$db->query($query);
if ($result-> num_rows!=0){
    echo '<table width="100%" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px">'
. '<thead>'
. '<th>Department</th>'
. '<th>Name</th>'
. '<th>Position</th>' 
. '<th>E-mail</th>'
. '<th>Phone</th>'
. '<th>Mobile</th>'     
. '<th>Note</th>'
. '<th></th>'
    . '</thead><tbody>';
while($row = $result->fetch_assoc()){
    echo 
        '<td>'.$row['department']
        . '<td>'.$row['name'].'</td>'
        . '<td>'.$row['position'].'</td>'   
        . '<td>'.'<a href="mailto:'.$row['email'].'">'.$row['email'].'</a>'.'</td>'
        . '<td>'.$row['phone'].'</td>'
        . '<td>'.$row['mob'].'</td>'
        . '<td>'.$row['note'].'</td>'
        . '<td width="60 px" style="border:0"><input type="button" value="Edit" onclick="edit_contact_display('.$row['id'].')"></td>'
        . '</tr>';
}
echo '</tbody></table>';
} 
else {
    echo 'No contacts yet'.$db->error;
}
?>
</div>
</div>
<!-- EDIT CONTACT  --> 
<div id="edit_contact" class="hidden" style="height: 400px; width:500px"></div>
<!-- NEW CONTACT  --> 
<div id="new_contacts" class="hidden" style="height: 400px;width:500px;">
        <form name="new_contacts_form" method="POST" onsubmit="return new_contact('<?php echo $id;?>', this)">
        <h2 align="center">Add contacts</h2>
            <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
                <tr>
                    <td><b>Department</b></td><td>
                    <input type="text" maxlength="30" size="50" name="new_department"></td>
                </tr>
                <tr>
                    <td><b>Name</b></td>
                    <td><input type="text" required maxlength="100" size="50" name="new_name"></td>
                </tr>
                <tr>
                    <td><b>Position</b></td>
                    <td><input type="text" maxlength="75" size="50" name="new_position"></td>
                </tr>
                <tr>
                    <td><b>E-mail</b></td>
                    <td><input type="text" maxlength="50" size="50" name="new_email" maxlength="50"></td>
                </tr>
                <tr>
                    <td><b>Phone</b></td>
                    <td><input type="text" maxlength="50" size="50" name="new_phone" maxlength="30"></td>
                </tr>
                <tr>
                    <td><b>Mobile</b></td>
                    <td><input type="text" maxlength="50" size="50" name="new_mob" maxlength="30"></td>
                </tr>
                <tr>
                    <td><b>Note</b></td>
                    <td><input type="text" maxlength="150" size="50" name="new_note"></td>
                </tr>
            </table>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Save" class="green_button">
        <input type="button" value="Close" onclick="cancel('new_contacts')"> 
        <input type="hidden" name="customer_id" value="<?php echo $id;?>">
    </div>
    <div id="contacts_status" align="center" width="100%" style="padding: 10px"></div>
    </form>
</div>
<!-- NEW VESSEL -->
<div id="new_vessel" class="hidden" style="width:700px;height:80%">
    <form id="new_vessel_form" onsubmit="return add_vessel('<?php echo $id;?>')">
        <h2 align="center">Add vessel</h2>
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
                <tr>
                    <td><b>Vessel name</b></td><td>
                    <input required type="text" name="new_vessel_name"></td>
                </tr>
                <tr>
                    <td><b>IMO</b></td><td>
                    <input required type="number" name="IMO" maxlength="7"></td>
                </tr>
                <tr>
                    <td><b>MMSI</b></td><td>
                    <input type="number" name="mmsi" maxlength="9"></td>
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
                    <td><?php echo selector('customers','name="new_customer"',$id,'ownr');?>
                </tr>
                <tr>
                    <td><b>Manager company</b></td>
                    <td><?php echo selector('customers','name="ship_manager"',$id,'mngr');?>
                </tr>
                <tr>
                    <td><b>Operator</b></td>
                    <td><?php echo selector('customers','name="ship_operator"',$id,'optr');?>
                </tr>
                <tr>
                    <td><b>Flag</b></td>
                    <td><?php select_country('RU', 'name="new_flag"')?></td>
                </tr>
                <tr>
                    <td><b>Class societies</b></td><td>
                    <input type="text" name="class_societies" maxlength="100"></td>
                </tr>
                <tr>
                    <td><b>Captain</b></td>
                    <td><input type="text" name="captain" maxlength="100" value="<?php echo $row['captain'];?>"></td>
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
                    <td><textarea name="vessel_note" maxlength="500" cols="50" rows="3"></textarea></td>
                </tr>
            </table>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add vessel"> 
        <input type="button" value="Close" onclick="document.getElementById('new_vessel').style.display='none'">
        <input type="hidden" value="window" name="return-path">
    </div>
    </form>
</div>
</div>