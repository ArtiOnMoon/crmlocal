<?php
require_once 'functions/fns.php';
if (!access_check([],[],2)) exit ('Access denied');
do_page_header('Change company','Справочники');
$id=clean($_GET['cust_id']);

if(check_access('acl_cust', 1)) exit('Access denied.');

//вывод ИНФО
$db =  db_connect();
$query= 'select * from customers where cust_id = "'.$id.'" and deleted=0';
$result=$db->query($query);
if ($result-> num_rows==1){
    $row=$result->fetch_assoc();
?>
<div id="wrap" onclick="cancel()"></div>
<!-- NEW VESSEL -->
<div id="new_vessel" class="hidden" style="width:600px;z-index: 99;">
    <form id="new_vessel_form" onsubmit="return add_vessel('<?php echo $id;?>')">
        <h2 align="center">Add vessel</h2>
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
                <tr>
                    <td><b>Vessel name</b></td><td>
                    <input required type="text" name="new_vessel_name"></td>
                </tr>
                <tr>
                    <td><b>IMO</b></td><td>
                    <input required type="text" name="IMO" maxlength="7"></td>
                </tr>
                <tr>
                    <td><b>Owner</b></td>
                    <td><?php echo select_customer($id,0,'name="new_customer" required','ownr'); ?></td>
                </tr>
                <tr>
                    <td><b>Manager company</b></td>
                    <td><?php echo select_customer($id,0,'name="ship_manager" required','mngr');?> </td>
                </tr>
                <tr>
                    <td><b>Operator</b></td>
                    <td><?php echo select_customer($id,0,'name="ship_operator" required','optr');?> </td>
                </tr>
                <tr>
                    <td><b>Captain</b></td>
                    <td><input  maxlength="30" type="text" name="new_captain"></td>
                </tr>
                <tr>
                    <td><b>Flag</b></td>
                    <td><input type="text" name="new_flag"></td>
                </tr>
                <tr>
                    <td><b>Class societies</b></td><td>
                    <input type="text" name="class_societies" maxlength="100"></td>
                </tr>
                <tr>
                    <td><b>Vessel contacts</b></td>
                    <td><textarea name="new_vessel_contacts" maxlength="500" cols="50" rows="3"></textarea></td>
                </tr>
                 <tr>
                    <td><b>Note</b></td>
                    <td><textarea name="vessel_note" maxlength="500" cols="50" rows="3"></textarea></td>
                </tr>
            </table>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add vessel"> 
        <input type="button" value="Close" onclick="cancel()">
        <input type="hidden" value="window" name="return-path">
    </div>
    </form>
</div>
<!-- NEW CONTACTS -->
<div id="new_contacts" class="hidden" style="height: 400px;width:300px;">
        <form name="new_contacts_form" method="POST" action="new_contact.php">
        <h2 align="center">Add contacts</h2>
            <table width="100%" border="1px" cellspacing = "0" cellpadding="2px"><tr>
                    <td><b>Department</b></td><td>
                        <input type="text" maxlength="30" name="new_department"></td>
                </tr><tr>
                    <td><b>Name</b></td>
                    <td><input type="text" required maxlength="100" name="new_name"></td>
                </tr>
                 </tr><tr>
                    <td><b>Position</b></td>
                    <td><input type="text" maxlength="75" name="new_position"></td>
                </tr>
                <tr>
                    <td><b>E-mail</b></td>
                        <td><input type="text" maxlength="50" name="new_email" maxlength="50"></td>
                    </tr>
                    <tr>
                        <td><b>Phone</b></td>
                            <td><input type="text" maxlength="30" name="new_phone" maxlength="30"></td>
                    </tr>
                    <tr>
                        <td><b>Mobile</b></td>
                            <td><input type="text" maxlength="30" name="new_mob" maxlength="30"></td>
                    </tr>
                    <tr>
                        <td><b>Note</b></td>
                            <td><input type="text" maxlength="150" name="new_note"></td>
                    </tr>
            </table>
    <br>
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add contact">
        <input type="button" value="Close" onclick="cancel()"> 
        <input type="hidden" name="customer_id" value="<?php echo $id;?>">
    </div>
    <div id="contacts_status" align="center" width="100%" style="padding: 10px"></div>
    </form>
</div>
<div id="edit_contact" class="hidden" style="height: 350px"></div>
<div id="main_div_menu" >
<div style="width: 70%; align-content: left; text-align:left; display: inline-block">
<h1>Customer <?php echo$id;?></h1>
<form id="customer_form" onsubmit="return change_customer()">
<table width="100%" rules="none" cellspacing = "0" cellpadding="2px">
<tr>
    <td><b>Company short name</b></td>
    <td><input type="text" maxlength="50" style="width:100%" name="new_comp_short_name" required value="<?php echo $row['cust_short_name'];?>"></td>
    <td><b>Customer status</b></td>
    <td><?php echo select_customer_status($row['customer_status']);?></td>
</tr>
<tr>
    <td><b>Company full name</b></td>
    <td><input required maxlength="250" name="new_company_full_name" style="width:100%;" value="<?php echo $row['cust_full_name'];?>"></td>
    <td rowspan="2"><b>Category</b></td>
    <td rowspan="2">
        <label><input type="checkbox" name="is_mnfr" value=1 <?php if($row['is_mnfr']=='1')echo'checked';?>>Manufacturer</label>
        <label><input type="checkbox" name="is_sppl" value=1 <?php if($row['is_sppl']=='1')echo'checked';?>>Supplier</label>
        <label><input type="checkbox" name="is_serv" value=1 <?php if($row['is_serv']=='1')echo'checked';?>>Service</label>
        <label><input type="checkbox" name="is_mngr" value=1 <?php if($row['is_mngr']=='1')echo'checked';?>>Manager</label><br>
        <label><input type="checkbox" name="is_ownr" value=1 <?php if($row['is_ownr']=='1')echo'checked';?>>Owner</label>
        <label><input type="checkbox" name="is_optr" value=1 <?php if($row['is_optr']=='1')echo'checked';?>>Operator</label>
        <label><input type="checkbox" name="is_agnt" value=1 <?php if($row['is_agnt']=='1')echo'checked';?>>Agent</label>
        <label><input type="checkbox" name="is_fchk" value=1 <?php if($row['is_fchk']=='1')echo'checked';?>>For checking</label>
     </td>
</tr>
<tr>
    <td><b>VAT</b></td>
    <td><input type="text" maxlength="30" style="width:100%" name="vat" value="<?php echo $row['vat'];?>"></td>
</tr>
<tr>
    <td><b>Country</b></td>
    <td><?php echo select_country($row['country']);?></td>
</tr>
<tr>
    <td><b>Address</b></td>
    <td><textarea required maxlength="500" rows="2" cols="50" name="new_address" style="resize: none; width:100%;"><?php echo $row['address'];?></textarea></td>
    <td><b>Invoicing Address</b></td>
    <td><textarea maxlength="500" rows="2" cols="50" name="new_invoicing_address" style="resize: none;width:100%"><?php echo $row['InvoicingAddress'];?></textarea></td>
</tr>
<tr>
    <td><b>Bank details</b></td>
    <td><textarea maxlength="500" rows="2" cols="50" name="bank_details" style="resize: none;width:100%"><?php echo $row['bank_details'];?></textarea></td>
    <td><b>Note</b></td>
    <td><textarea maxlength="500" cols="50" rows="2" name="new_note" style="resize: none;width:100%"><?php echo $row['note'];?></textarea></td>
</tr>
<tr>
    <td><b>Website</b></td>
    <td><input type="text" maxlength="30" name="new_website" style="width:50%" value="<?php echo $row['website'];?>"> <a href="<?php echo $row['website'];?>">website</a></td>
</tr>
<tr>
    <td><b>E-mail</b></td>
    <td><input required type="text" maxlength="50" style="width:50%" name="new_email" value="<?php echo $row['email'];?>"><a href="mailto:<?php echo $row['email'];?>"> Send e-mail</a></td>
</tr>
<tr>
    <td><b>Contact phone</b></td>
    <td><input required type="text" maxlength="30" style="width:100%" name="new_contact_phone" value="<?php echo $row['contact_phone'];?>"></td>
    <td><b>Payment terms</b></td>
    <td> <?php echo select_payment_terms($row['payment_terms']);?></td>
</tr>
<tr>
    <td><b>Additional phone</b></td>
    <td><input type="text" maxlength="30" name="new_add_phone" style="width:100%" value="<?php echo $row['add_phone'];?>"></td>
    <td><b>Credit limit</b></td>
    <td><input required maxlength="10" type="text" style="width:100%" name="credit_limit" value="<?php echo $row['credit_limit'];?>"></td>    
</tr>
<tr>
    <td><b>Fax</b></td>
    <td><input type="text" name="new_fax" maxlength="30" style="width:100%" value="<?php echo $row['fax'];?>"></td>
    <td><b>Discount (%)</b></td>
    <td><input required type="text" name="discount" maxlength="3" style="width:100%" value="<?php echo $row['discount'];?>"></td>
</tr>
</table>
    <p>
    <div align="center"><input type="submit" class="button" value="Apply changes"><p>
        <button onclick="cancel()" class="button">Cancel</button>
    </div>
    <input type="hidden" name="company_id" value="<?php echo $row['cust_id'];?>">
    <input type="hidden" name="return-path" value="window">
</form>
<form method="POST" action="/scripts/delete_customer.php?id=<?php echo $_GET['cust_id'];?>" onclick="return check_delete()">
<div style="float:right"><input type="submit" class="red_button" value="Delete customer"></div>
</form>
</div>
    <!-- RELATED VESSELS -->
<?php if($row['is_mngr']=='1' || $row['is_ownr']=='1' || $row['is_optr']=='1'){
?>
<div style="width:25%; display:inline-block; float:right;overflow:auto; max-height: 500px;">
    <h2>Vessels list</h2>
    <table width="100%">
        <thead><th>Vessel name</th><th>IMO</th><th>Relation type</th></thead>
    <?php
    $query='select vessel_id, vessel_name, imo, company, ship_manager, ship_operator from vessels where ship_manager="'.$id.'" or company="'.$id.'" or ship_operator="'.$id.'"';
    $result=$db->query($query);
    while ($row=$result->fetch_assoc()){
        echo '<tr><td>', view_vessel_link($row['vessel_name'], $row['vessel_id']),'</td><td>',marine_traffic_link($row['imo'],$row['imo']),'</td><td>';
        if ($row['company']==$id) echo 'Owner';
        elseif ($row['ship_manager']==$id) echo 'Manager';
        else echo 'Operator';
        echo'</td></tr>';
    }
    ?>
    </table>
    <button onclick="display('new_vessel')">New vessel</button>
</div>
<?php
}

} 
else {
    echo 'Nothing found';    exit();
}
?>
<!-- CONTACTS -->
<div style="width:100%;">
<?php
echo '<h2>Contacts</h2>';
$query= 'select * from customers_contacts where customer_id = "'.$id.'" and deleted=0';
$result=$db->query($query);
if ($result-> num_rows!=0){
    echo '<table class="sortable" width="100%"  border="1px" cellspacing = "0" cellpadding="2px">'
. '<thead>'
. '<th width="100">Department</th>'
. '<th width="200">Name</th>'
. '<th width="100">Position</th>' 
. '<th width="100">E-mail</th>'
. '<th width="100">Phone</th>'
. '<th width="100">Mobile</th>'     
. '<th width="100">Note</th>'
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
        .'<td width="60 px" style="border:0"><input type="button" value="Edit" onclick="edit('.$row['id'].')"></td>'
        . '</tr>';
}
echo '</tbody></table>';
} 
else {
    echo 'No contacts yet'.$db->error;
}
?>

<p><a class="knopka" href="#" onclick="display('new_contacts')">Add new contact</a>   
</div>
</div>

<script src="java/java_func.js"></script>
<script src="java/java_customers.js"></script>
