<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/selector.php';
startSession();
?>
<div class="window_internal" style="width:1280px; height:720px">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
<form name="new_company" action="add_customer.php" method="POST" onsubmit="return add_new_customer_ajax(this)">
    <input type="hidden" name="return-path" value="window">
        <h2 align="center">Add customer</h2> 
            <table class="fancy_table" width="100%">
                <col width="25%">
                <col width="25%">
                <col width="25%">
                <col width="25%">
                <tr><td colspan="3"></td><td class="fancy_td" style="background: #ffcccc;"><strong>Client of </strong> <?php echo select_our_company('','name="client_of"',0) ?></td></tr>
                <tr>
                    <td class="fancy_td" colspan="2"><b>Company short name</b> 
                        <input required ype="text" maxlength="250" name="new_comp_short_name" style="width:100%"></td>
                    <td class="fancy_td" colspan="2"><b>Company full name</b>
                        <input typr="text" required maxlength="250" name="new_company_full_name" style="width:100%;"></td>
                </tr>
                <tr>
                    <td class="fancy_td" colspan="2"><b>Address</b>
                            <input required maxlength="300" name="new_address2" style="width:100%;"  placeholder="Office, street, building">
                            <br>
                            <input required maxlength="300" name="new_address" style="width:100%;" placeholder="City, post code">
                        </td>
                    <td class="fancy_td" colspan="2"><b>Invoicing address</b>
                        <label><input type="checkbox" checked name="address_check" value="1" onchange="invoicing_address_check(this)"> Same as address</label>
                        <input disabled maxlength="300" name="new_invoicing_address2" style="width:100%" id="inv_addr_1" placeholder="Office, street, building"><br>
                        <input disabled maxlength="300" name="new_invoicing_address" style="width:100%" id="inv_addr_2" placeholder="City, post code">
                    </td>
                </tr>
                <tr>
                    <td class="fancy_td" colspan="2">
                        <b>Vat No</b> <input maxlength="30" type="text" name="vat" size="12">&nbsp;&nbsp;
                        <b>Country</b> <?php select_country();?>&nbsp;&nbsp;
                        <b>Status</b> <?php select_customer_status();?></td>
                    <td class="fancy_td" colspan="2"><b>Type</b>
                        <label><input type="checkbox" name="is_mnfr" value=1>Manufacturer</label>
                        <label><input type="checkbox" name="is_sppl" value=1>Supplier</label>
                        <label><input type="checkbox" name="is_serv" value=1>Service</label>
                        <label><input type="checkbox" name="is_mngr" value=1>Manager</label>
                        <label><input type="checkbox" name="is_ownr" value=1>Owner</label>
                        <label><input type="checkbox" name="is_agnt" value=1>Agent</label>
                        <label><input type="checkbox" name="is_optr" value=1>Operator</label>
                        <label><input type="checkbox" name="is_fchk" value=1>For checking</label>
                    </td>
                </tr> 
                <tr>
                    <td rowspan="2" class="fancy_td"><b>E-mail</b><br>
                        <input required maxlength="50" type="text" name="new_email" size="50"><br>
                        <input maxlength="50" type="text" name="email2" size="50"><br>
                        <input maxlength="50" type="text" name="email3" size="50">
                    </td>
                    <td class="fancy_td" rowspan="2"><b>Note</b>
                        <textarea name="new_note" maxlength="500" rows="5" style="width:100%; resize:none" ></textarea></td>
                    <td class="fancy_td"><b>Сontact phone</b>
                        <input required type="text" name="new_contact_phone" maxlength="30" style="width:100%"></td>
                    <td class="fancy_td"><b>Additional phone</b>
                        <input type="text" name="new_add_phone" maxlength="30" style="width:100%"></td>
                </tr>
                <tr>
                    <td class="fancy_td"><b>Web site</b>
                        <input type="text"maxlength="30" name="new_website" maxlength="100" style="width:100%"></td>
                    <td class="fancy_td"><b>Fax</b>
                        <input type="text" name="new_fax" maxlength="30" style="width:100%"></td>
                </tr>
                <tr>
                    <td class="fancy_td"><b>Sales discount %</b>
                        <input type="text" size="3" maxlength="3" value="0" name="discount"></td>
                    <td class="fancy_td"><b>Service discount %</b>
                        <input type="text" size="3" maxlength="3" value="0" name="service_discount"></td>
                    <td class="fancy_td"><b>Payment terms</b>
                            <?php select_payment_terms(); ?></td>
                    <td class="fancy_td"><b>Credit limit</b>
                        <input type="text" size="3" maxlength="10" name="credit_limit"></td>
                </tr>
            </table>
        <p><h2>Contacts</h2>
        <table width="100%"  border="1px" cellspacing = "0" cellpadding="2px">
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
    </form>
    <div style="display:none">
        <table>
            <tr id="contact_row">
                <td><input type="text" name="cont_department[]" maxlength="100"></td>
                <td><input type="text" name="cont_name[]" required maxlength="100"></td>
                <td><input type="text" name="cont_position[]" maxlength="75"></td>
                <td><input type="text" name="cont_email[]" maxlength="100"></td>
                <td><input type="text" name="cont_phone[]" maxlength="30"></td>
                <td><input type="text" name="cont_mob[]" maxlength="30"></td>
                <td><input type="text" name="cont_note[]" maxlength="300"></td>
                <td><input type="button" value="Delete" onclick="delete_contact_row(this)"></td>
            </tr>
    </table>
</div>
</div>
