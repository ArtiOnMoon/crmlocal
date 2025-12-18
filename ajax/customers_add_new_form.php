<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/selector.php';
startSession();
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
<div class="window_internal" style="width:1024px; height:720px">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<form name="new_company" action="add_customer.php" method="POST" onsubmit="return add_new_customer_ajax(this)">
    <input type="hidden" name="return-path" value="window">
        <h2 align="center">Add customer</h2> 
        <div class="block_div calc_fancy_container" style="width: 960px; height:300px; display: inline-block">
    <div class="calc_fancy_div">
        <b>Short name</b>
        <input type="text" maxlength="250" style="width:320px" name="new_comp_short_name" required>
    </div>
    <div class="calc_fancy_div">
        <b>Full name</b>
        <input required maxlength="250" style="width:320px" name="new_company_full_name">
    </div>
    <div class="client_of_div block_div">
        <b>Client of</b>
        <?php echo select_client_of('','name="client_of"') ?>
    </div>
    <div class="calc_fancy_div">
        <b>Status</b>
        <?php echo select_customer_status($row['customer_status']);?>
    </div>
    <br><!-- LINE 2 -->
    <div class="calc_fancy_div">
        <table class="calc_fancy_subtable">
            <tr><td><b>Address</b></td>
                <td><input maxlength="300" name="new_address2" style="width:300px" placeholder="Office, street, building"></td>
            </tr>
            <tr><td></td><td><input maxlength="300" name="new_address" style="width:300px" placeholder="City, post code"></td></tr>
        </table>
    </div>
    <div class="calc_fancy_div">
        <table class="calc_fancy_subtable">
            <tr><td><b>Invoicing Address</b></td>
                <td><input disabled maxlength="300" name="new_invoicing_address2" style="width:300px" id="inv_addr_1" placeholder="Office, street, building"></td>
            </tr>
            <tr><td><label><input type="checkbox" checked name="address_check" value="1" onchange="invoicing_address_check(this)"> Same as address</label></td>
                <td><input disabled maxlength="300" name="new_invoicing_address" style="width:300px" id="inv_addr_2" placeholder="City, post code"></td></tr>
        </table>
    </div>
    <br><!-- LINE 3 -->
    <div class="calc_fancy_div">
        <b>Country</b>
        <?php echo select_country();?>
    </div>
    <div class="calc_fancy_div">
        <b>VAT or Reg. number</b>
        <input type="text" maxlength="30" size="15" name="vat">
    </div>
    <br><!-- LINE 4 -->
    <div class="calc_fancy_div">
        <b>Phone</b>
        <input required type="text" maxlength="30" name="new_contact_phone">
        <input type="text" maxlength="30" name="new_add_phone"><br>
    </div>
    <div class="calc_fancy_div">
        <b>Fax</b>
        <input type="text" name="new_fax" maxlength="30">
    </div>
    <div class="calc_fancy_div">
        <b>Website</b>
        <input type="text" maxlength="100" name="new_website" > 
    </div>
    <br><!-- LINE 5 -->
    <div class="calc_fancy_div">
        <b>E-mail</b>
        <input type="text" style="width:200px" name="new_email"><img title="Send email" class="line_image" align="middle" src='/icons_/email2.png'></a>
        <input type="text" style="width:200px" name="email2"><img title="Send email" class="line_image" align="middle" src='/icons_/email2.png'></a>
        <input type="text" style="width:200px" name="email3"><img title="Send email" class="line_image" align="middle" src='/icons_/email2.png'></a>
    </div>
    <br><!-- LINE 6 -->
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
    <br><!-- LINE 7 -->
    <div class="calc_fancy_div">
        <b>Sales discount %</b>
        <input type="text" size="3" name="discount" maxlength="3" value="0">
    </div>
    <div class="calc_fancy_div">
        <b>Service discount %</b>
        <input type="text" size="3" maxlength="3" name="service_discount" value="0">
    </div>
    <div class="calc_fancy_div">
        <b>Payment terms</b>
        <?php echo select_payment_terms();?>
    </div>
    <div class="calc_fancy_div">
        <b>Credit limit</b>
        <input maxlength="10" type="text" size="10" name="credit_limit" value="0">
    </div>
    <br><!-- LINE 8 -->
    <div class="calc_fancy_div">
        <b>Note</b>
        <textarea maxlength="500" cols="120" rows="1" name="new_note" style="width:100%"></textarea>
    </div>
</div> 
    <div style="height: 200px; overflow: auto;">
        <p><h2>Contacts</h2>
        <table width="100%" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px">
            <thead>
                <th>Department</th>
                <th>Name</th>
                <th>Position</th>
                <th>E-mail</th>
                <th>Phone</th>
                <th>Mobile</th>
                <th>Note</th>
                <th>Delete</th>
            </thead>
            <tbody id="contacts_tbody">
            </tbody>
        </table>
        <a href="#" onclick="add_contact_row()">Add contact</a>
    <br>
    <div align="center" width="100%" style="padding: 10px">
        <input type="submit" value="Save" class="green_button"> 
        <input type="button" value="Close" onclick="window_close(this);">
    </div>
</div>
</form>
    <div style="display:none">
        <table>
            <tr id="contact_row">
                <td><input type="text" name="cont_department[]" size="12" maxlength="100"></td>
                <td><input type="text" name="cont_name[]"  size="12" required maxlength="100"></td>
                <td><input type="text" name="cont_position[]" size="12" maxlength="75"></td>
                <td><input type="text" name="cont_email[]" size="12" maxlength="100"></td>
                <td><input type="text" name="cont_phone[]" size="12" maxlength="30"></td>
                <td><input type="text" name="cont_mob[]" size="12" maxlength="30"></td>
                <td><input type="text" name="cont_note[]" size="12" maxlength="300"></td>
                <td><input type="button" value="Delete" onclick="delete_contact_row(this)"></td>
            </tr>
    </table>
    </div>
</div>
