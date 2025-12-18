<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/service.php';
require_once '../functions/selector.php';

$stock_cat_list=get_stock_category_list();
$manuf_list=get_manufacturers_list(1);
//USERS_LIST
$users=[];
?>
<!-- New Service -->
<link href="/css/service.css" rel="stylesheet">
<div class="window_internal" style="width:90%; max-width: 1280px;height:90%;">
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
    <div class="nd_main_grid">
        <div class="nd_header">
            <div><h1>New service</h1></div>
            <button class="nd_close_button" onclick="sales_close(this)">&#10006;</button>
        </div>
        <div class="nd_subheader"></div>
        <form id="service_new_form" class="nd_body service_grid_conteiner nd_tabdiv nd_tabdiv_active">
            <input type="hidden" name="service_our_comp" value="<?php echo clean($_POST['service_our_comp'])?>">
             <div class="nd_block srv_status">
                <label class="nd_label">Status</label>
                <?php echo select_service_status($row['status'], 'name="new_service_status" class="nd_input"');?>
            </div>
            <div class="nd_block srv_date">
                <label class="nd_label">Date</label>
                <input type="text" class="nd_input datepicker" name="new_service_date" value="<?php echo date('Y-m-d');?>">
            </div>
            <div class="nd_block srv_srform">
                <label class="nd_label">SR form</label>
                 <?php echo select_sr_form($row['sr_form'],'name="sr_form" class="nd_select"');?>
            </div>
            <div class="nd_block srv_payment">
                <label class="nd_label">Form of payment</label>
                <?php echo select_pay_type($row['srv_pay_type'], 'name="srv_pay_type" class="nd_select"');?>
            </div>
            <div class="nd_block srv_inv">
                <label class="nd_label">Invoice</label>
                <input class="nd_input" type="text" name="invoice" value="<?php echo $row['invoice'];?>">
            </div>
            <div class="nd_block srv_agent">
                <label class="nd_label">Agent</label>
                <?php echo select_agent($row['agent'],$row['agent_contact_id'],'name="service_agent" onchange="select_agent_contact(this)" class="selector_select"','name="agent_contact_id" class="selector_select nd_select2" id="contact_id"'); ?>
                <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
            </div>
            <div class="nd_block srv_pass">
                <label class="nd_label">Pass status</label>
                <?php echo select_pass_status($row['passes_status'],'class="nd_select" name="passes_status"');?>
            </div>
            <div class="nd_block srv_customer customer_conteiner">
                <label class="nd_label">Customer</label>
                <?php echo selector('customers', 'name="new_customer"', $row['comp_id']);?>
                <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
            </div>
            <div class="nd_block srv_request">
                <label class="nd_label">Request №</label>
                <input class="nd_input" type="text" name="request" maxlength="100" value="<?php echo $row['request']?>">
            </div>
            <div class="nd_block srv_po">
                <label class="nd_label">Customer's PO</label>
                <input class="nd_input" type="text" name="new_PO" maxlength="100" size="20" value="<?php echo $row['PO']?>">
            </div>
            <div class="nd_block srv_po2">
                <label class="nd_label">Additioanl PO</label>
                <input class="nd_input" type="text" name="new_PO2" maxlength="100" size="20" value="<?php echo $row['PO2']?>">
            </div>
            <div class="nd_block srv_vesssel">
                <label class="nd_label">Vessel</label>
                <?php echo selector('vessels','name="new_vessel"',1);?>
            </div>
            <div class="nd_block srv_eta">
                <label class="nd_label">ETA</label>
                <input class="nd_input datepicker" type="text" name="new_ETA" value="<?php echo $row['ETA'];?>">
            </div>
            <div class="nd_block srv_etd">
                <label class="nd_label">ETD</label>
                <input class="nd_input datepicker" type="text" name="new_ETD" value="<?php echo $row['ETD'];?>">
            </div>
            <div class="nd_block srv_port">
                <label class="nd_label">Port</label>
                <input class="nd_input" type="text" name="new_location" size="10" value="<?php echo $row['location'];?>">
            </div>
            <div id="srv_inv_instr" class="nd_block srv_insructions">
                <div class="calc_fancy_div">
                    <label class="nd_label">Invoicing instructions
                        <input type="checkbox" name="inv_instructions" <?php if($row['inv_instructions']==1)echo 'checked'; ?> onchange="disabled_control(this,'inv_instructions_div')">
                    </label>
                    <div class="disabledbutton" id="inv_instructions_div">
                            <table class="nd_subtable">
                                <tr>
                                    <td><b>Invoice to:</b></td>
                                    <td><input type="text" name="srv_inv_comp_name" placeholder="Company name" maxlength="100" size="40" value="<?php echo $row['srv_inv_comp_name'];?>"></td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input type="text" name="srv_inv_comp_name2" placeholder="Company name line 2" maxlength="100" size="40" value="<?php echo $row['srv_inv_comp_name2'];?>"></td>
                                </tr>
                                <tr>
                                    <td><b>Address:</b></td><td>
                                        <input type="text" name="srv_inv_addr1" maxlength="200" size="40" placeholder="Office, street, building" value="<?php echo $row['srv_inv_addr1'];?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input type="text" name="srv_inv_addr2" maxlength="200" size="40" placeholder="City, post code, country" value="<?php echo $row['srv_inv_addr2'];?>"></td>
                                </tr>
                                <tr>
                                    <td><b>Country:</b></td>
                                    <td><?php echo select_country($row['srv_inv_country'], 'name="srv_inv_country"');?>
                                </tr>
                                <tr>
                                    <td><b>VAT:</b></td>
                                    <td><input type="text" name="srv_inv_vat" maxlength="15" size="40" value="<?php echo $row['srv_inv_vat'];?>"></td>
                                </tr>
                                <tr>
                                    <td><b>E-mail:</b></td>
                                    <td><input type="text" name="srv_inv_email" maxlength="100" size="40" value="<?php echo $row['srv_inv_email'];?>"></td>
                                </tr>
                            </table>
                        </div>
                </div>
            </div>
            <div class="nd_block srv_links related_orders_wrapper nd_links">
                <label class="nd_label">Links</label>
                <input type="hidden" class="related_orders_number" value="<?php echo $on->order;?>">
                    You must save the order to be able to add links.
            </div>  
            <div class="nd_block srv_table">
                <div style="text-align: center"><h3>Service required</h3></div>
                    <table class="nd_table">
                        <thead>
                            <tr>
                                <th><strong>Service type</strong></th>
                                <th><strong>Equipment</strong></th>
                                <th><strong>Fault description</strong></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="service_required_table">
                            <tr>
                                <td>
                                    <?php echo select_service_type($faults['sfd_type'],'name="sfd_type[]" onchange="service_fault_selector(this)"');?>
                                </td>
                                <td>
                                    <?php echo selector_equip_long(get_stock_category_list(), get_manufacturers_list());?><a href="#" style="font-size:larger;font-weight: bold;line-height: 1.15;" onclick="service_add_equipment(this)">&#xFF0B;</a>
                                    <br /><input type="text" name="sfd_equip_comment[]" size="60" placeholder="Additional info \ Class society">
                                </td>
                                <td>
                                    <textarea name="sfd_descr[]" rows="2" cols="40" maxlength="2000"></textarea>
                                </td>
                                <td>
                                    <a href="#" onclick="delete_equip_row(this)"class="redtext">Delete</a>
                                </td>   
                            </tr>
                        </tbody>
                    </table>
                    <div class="align_center"><a class="nd_button" href="#" onclick="sfd_insert_row(this)">Add line</a></div>
                </div>
            <div class="srv_bottom_block">
                <div class="nd_block nd_block_for_inputs">
                    <label class="nd_label">Engineer</label>
                    <div class="calc_fancy_div" onmouseleave="user_select_close(this)">
                        <div style="display:inline-block"><?php user_multiselect3($users);?></div>
                    </div>
                    <label class="nd_label">Service agent</label>
                    <div class="calc_fancy_div">
                        <div style='display: inline-block;'><?php echo selector('customers', 'name="service_executor_id"', $row['service_executor_id'],'serv');?></div>
                    </div>
                </div>
                <div class="nd_block">
                    <label class="nd_label">Note</label>
                    <textarea class="nd_textarea" name="service_note" ><?php echo $row['service_note'];?></textarea>
                </div>
            </div>
        </form>
        <div class="nd_block nd_footer">
             <input type="button" value="Save" class="nd_button_green" onclick="service_new_form(this)">
        </div>
<!-- ROW for insert -->
    <table style="display:none;">
        <tr id="sfd_row_for_insert">
            <td>
            <?php echo select_service_type($faults['sfd_type'],'name="sfd_type[]" onchange="service_fault_selector(this)"');?>
            </td>
            <td>
                <?php echo selector_equip_long(get_stock_category_list(), get_manufacturers_list());?><a href="#" style="font-size:larger;font-weight: bold;line-height: 1.15;" onclick="service_add_equipment(this)">&#xFF0B;</a>
                <br /><input type="text" name="sfd_equip_comment[]" size="60" placeholder="Additional info \ Class society">
            </td>
            <td>
                <textarea name="sfd_descr[]" rows="2" cols="40" maxlength="2000"></textarea>
            </td>
            <td>
                <a href="#" onclick="delete_equip_row(this)"class="redtext">Delete</a>
            </td>  
        </tr>
    </table>
    </div>
</div>