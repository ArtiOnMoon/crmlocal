<?php
require_once 'functions/fns.php';
require_once 'functions/files_func.php';
require_once 'PATHS.php';
require_once 'functions/selector.php';
require_once 'functions/stock_fns.php';
require_once 'functions/invoice_fns.php';
require_once 'classes/Order_name_engine.php';

//$service_no=clean($_POST['service_no']);
//$service_our_comp=clean($_POST['service_our_comp']);

$order = clean($_POST['order']);
//
$service_id=clean($_POST['order_id']);
//
$comp_list = get_our_companies_list(1);
$stock_cat_list=get_stock_category_list();
$manuf_list=get_manufacturers_list(1);

?>
<link href="/css/calculation.css" rel="stylesheet">
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
            <?php
                $db =  db_connect();
                $on = new Order_name_engine();
                $on->init($db);
                try{
                    $on->resolve_order($order);
                } catch (Exception $ex) {
                    $ex->getMessage();
                    exit();
                }
                
                $query= 'SELECT service.*, users.full_name FROM service LEFT JOIN users ON users.uid= service.modified ';
                if (isset($_POST['order_id']))$query.='WHERE service_id = "'.$service_id.'"  AND service_deleted=0';
                else $query.= 'WHERE service_no = "'.$on->num.'" AND service_our_comp = "'.$on->comp_id.'" AND service_deleted=0';
                $result=$db->query($query);
                if ($result-> num_rows!==1)exit('Service order not found');
                $row=$result->fetch_assoc();
                $service_id=$row['service_id'];
                $service_no=$row['service_no'];
                
                if(check_access('acl_service', 1, $row['service_our_comp'])) exit('Access denied.');
                
                
                //USERS_LIST
                $users=[];
                $query= 'select su_uid from service_users where su_service_id = "'.$service_id.'"';
                $result=$db->query($query);
                while($elem = $result->fetch_assoc()){
                    $users[]=$elem['su_uid'];
                }

                //FAULT DESCRIPTIONS
                $query='SELECT * FROM service_fault_descr WHERE sfd_serv_id="'.$service_id.'"';
                $result=$db->query($query);
            ?>
            <div><h1><?php echo $on->get_order(); ?> <a onclick="copy_to_clipboard(this, '<?php echo $on->order;?>')"><img src="./icons_/copy.svg"></a></h1></div>
            <button class="nd_close_button" onclick="sales_close(this)">&#10006;</button>
        </div>
        <div class="nd_subheader nd_tabs">
            <div class="nd_tab nd_tab_active" onclick="switchTab(this)" data-tab="service_info_tab">Service info</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="calculation_tab">Invoice out</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="invoices_related">Related Invoices</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="stock_related">Related stock items</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="uploaded_files">Uploaded files</div>
        </div>
        <form id="service_info_tab" class="nd_body service_grid_conteiner nd_tabdiv nd_tabdiv_active">
            <input type="hidden" name="vessel_id" value="<?php echo $row['vessel_id'];?>">
            <input type="hidden" name="service_id" value="<?php echo $service_id;?>">
            <input type="hidden" name="return-path" value="window">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="return-path" value="window">
<!--            <div class="nd_block srv_id">
                <label class="nd_label">Case id</label>
                <input class="nd_input" type="number" min="1" name="service_no" maxlength="8" value="<?php // echo $row['service_no']; ?>">
            </div>-->
            <div class="nd_block srv_status">
                <label class="nd_label">Status</label>
                <?php echo select_service_status($row['status'], 'name="new_service_status" class="nd_input"');?>
            </div>
            <div class="nd_block srv_date">
                <label class="nd_label">Date</label>
                <input type="text" class="nd_input datepicker" name="new_service_date" value="<?php echo $row['service_date'];?>">
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
            <div class="nd_block srv_vesssel vessel_conteiner">
                <label class="nd_label">Vessel</label>
                <?php echo selector('vessels','name="new_vessel"',$row['vessel_id']);?>
                <img title="View vessel" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="vessel_view_add(this)">
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
                <label class="nd_label">Invoicing instructions
                    <input type="checkbox" name="inv_instructions" <?php if($row['inv_instructions']==1)echo 'checked'; ?> onchange="disabled_control(this,'inv_instructions_div')">
                </label>
                <div <?php if($row['inv_instructions']=='0') echo 'class="disabledbutton"'; ?> id="inv_instructions_div">
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
            <div class="nd_block srv_links related_orders_wrapper nd_links">
                <label class="nd_label">Links</label>
                <input type="hidden" class="related_orders_number" value="<?php echo $on->order;?>">
                <div class="nd_links_container related_orders_conteiner"></div>
                <div>
                    <input type="text" maxlength="12" width="100%" class="related_orders_number2" placeholder="Number XX-XXX-XXXXX">
                    <br />
                    <a class="knopka3" href="#" onclick="related_orders_add(this)">Add link</a>
                </div>
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
                    <?php
                    while ($faults=$result->fetch_assoc()){
                        echo '<td>',select_service_type($faults['sfd_type'],'name="sfd_type[]" onchange="service_fault_selector(this)"'),'</td>'
                            , '<td>';
                        if ($faults['sfd_type']=='2') echo selector_equip_long($stock_cat_list, $manuf_list,'name="sfd_equip_id[]"',$faults['sfd_equip_id'],1); 
                        else echo selector_equip_long($stock_cat_list, $manuf_list,'name="sfd_equip_id[]"',$faults['sfd_equip_id']);                            
                        echo  '<a href="#" style="font-size:larger;font-weight: bold;line-height: 1.15;" onclick="service_add_equipment(this)">&#xFF0B;</a>'
                            , '<br /><input type="text" placeholder="Additional info \ Class society" name="sfd_equip_comment[]" size="60" value="',$faults['sfd_equip_comment'],'"></td>'
                            , '<td><textarea name="sfd_descr[]" rows="2" cols="40" maxlength="2000">',$faults['sfd_descr'],'</textarea></td>'
                            , '<td><a href="#" onclick="delete_equip_row(this)"class="redtext">Delete</a></td></tr>';
                    }
                    ?>
                </tbody></table>
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
        <!-- CALCULATION -->
        <form id="calculation_tab" class="nd_body nd_block nd_tabdiv nd_tabdiv_inactive">
            <?php if(!check_access('acl_service', 2)){ ?>
            <table class="fancy_table calc_table bank_details_container">
            <tr>
                <td class="fancy_td" colspan="1"><b>Invoice №:</b>
                     <input type="text" name="srv_inv_number" maxlength="50" value="<?php echo $row['srv_inv_number'];?>">
                </td>
                <td class="fancy_td" colspan="1"><b>Credit note №:</b> <input type="checkbox" name="srv_cn_flag" <?php if ($row['srv_cn_flag']=='1') echo 'checked'?> onchange="checked_field_reverse(this,'srv_cn_number')">
                     <input type="text" id="srv_cn_number" name="srv_cn_number" maxlength="50" value="<?php echo $row['srv_cn_number'];?>" <?php if ($row['srv_cn_flag']=='0') echo 'disabled'?>>
                </td>
                <td class="fancy_td" colspan="1"><b>Invoice date:</b>
                    <input type="text" name="srv_inv_date" size="9" class="datepicker" value="<?php echo $row['srv_inv_date'];?>">
                </td>
                <td class="fancy_td" colspan="1"><b>Invoice from:</b>
                    <?php select_our_company($row['srv_inv_from'], 'name="srv_inv_from" class="bank_det_company" onchange="get_our_bank_det(this)"')?>
                </td>
                <td class="fancy_td" colspan="1"><b>Currency:</b>
                     <?php echo select_currency2(get_currency_list(), $row['service_currency'], 'name="service_currency" class="bank_det_currency" onchange="get_our_bank_det(this)"')?>
                </td>
                <td class="fancy_td" colspan="1"><b>Bank details:</b>
                    <?php echo select_our_bank_det_ajax($row['srv_inv_from'],$row['srv_our_bank_details'],$row['service_currency'],'class="our_bank_det short_select" name="srv_our_bank_details"');?>
                </td>
                <td class="fancy_td" colspan="1"><b>Payment terms:</b>
                    <?php echo select_payment_terms($row['srv_pay_terms'],'srv_pay_terms');?>
                </td>
            </tr>
            </table>
        <table id="entries_table" width="100%">
        <thead>
            <th width="5%">№</th>
            <th width="60%">Description</th>
            <th width="7%">Q-ty</th>
            <th width="7%">Unit price</th>
            <th width="7%">Discount %</th>
            <th width="7%">Amount</th>
            <th width="7%"></th>
        </thead>
        <tbody>
            <tr class="header_row "><td colspan="6">Product service report <?php echo service_id_num($row['service_no'],$row['service_our_comp']);?></td>
                <td><input type="button" class="small_button" value="Reset form" onclick="reset_rates()"></td></tr>
    <?php
    //Проверка ENTRIES
    $i=1;
    //RATES
    $query='SELECT * FROM service_calc_entries WHERE (entry_type=1 or entry_type=3) AND entry_related_id='.$service_id.'';
    $result2=$db->query($query);
    if ($result2->num_rows>0){
        while ($entries=$result2->fetch_assoc()){
            switch ($entries['entry_type']){
                case 1:
                    ?>
                    <tr>
                    <td class="calc_fancy_td"></td>
                    <td class="calc_fancy_td"><?php echo comboselect_rates($entries['entry_text'],$entries['entry_base_id']);?></td>
                    <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $entries['entry_qty'];?>" maxlength="6" name='entry_qty[]'></td>
                    <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $entries['entry_price'];?>" maxlength="6" name='entry_price[]'></td>
                    <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="<?php echo $entries['entry_discount'];?>" maxlength="6" name='entry_discount[]'></td>
                    <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="<?php echo $entries['entry_price']*$entries['entry_qty']*(1-$entries['entry_discount']*0.01);?>">
                        <input type="hidden" name='entry_type[]' value='1'>
                    </td>
                    <td class="calc_fancy_td calc_action_td"><input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)"></td>
                    </tr>
                    <?php
                    break;
                case 3:
                    ?>
                    </tbody>
                    <tbody>
                    <tr>
                    <td class="number_td calc_fancy_td"><strong><?php echo $i;?></strong></td>
                    <td class="calc_fancy_td align_left">
                        <span class="table_input input_header"><?php echo $entries['entry_text'];?></span>
                        <input class='table_input input_header' type="hidden" maxlength="250" name='entry_text[]' value="<?php echo $entries['entry_text'];?>">
                    </td>
                    <td class="calc_fancy_td align_left" colspan="5" class="calc_action_td">
                        <input type="button" class='small_button' value="Add" onclick="entry_rate(this)">
                        <input type="button" class='small_button red_button' value="Delete" onclick="delete_rate_cat(this)">
                        <input type='hidden' value='0' class="qty_input" name='entry_qty[]'>
                        <input type='hidden' value='0' class="price_input" name='entry_price[]'>
                        <input type='hidden' value='0' class="discount_input" name='entry_discount[]'>
                        <input type='hidden' value='0' class="amount_input">
                        <input type="hidden" name='entry_type[]' value='3'>
                        <input type="hidden" class="entry_base_id" name='entry_base_id[]' value='<?php echo $entries['entry_base_id'];?>'>
                    </td>
                    </tr>
                    <?php
                    $i++;
                    break;
            }
        }  
    }
    //SPARE PARTS
    ?>  </tbody><tbody id="entries_spares_body">
        <tr><td class="number_td calc_fancy_td"><strong><?php echo $i++;?></strong></td>
            <td class="calc_fancy_td align_left"><span class='table_input input_header'>Spare parts and materials:</span></td>
            <td class="calc_fancy_td align_left" colspan="5" class="calc_action_td">
                <input type="button" class='small_button' value="Add spare" onclick="entry_spare(this)">
                <input type='hidden' value='0' class="qty_input">
                <input type='hidden' value='0' class="price_input" >
                <input type='hidden' value='0' class="discount_input">
                <input type='hidden' value='0' class="amount_input">
            </td></tr>
    <?php
    $query='SELECT * FROM service_calc_entries WHERE (entry_type=0 or entry_type=2) AND entry_related_id='.$service_id.'';
    $result2=$db->query($query);
    if ($result2->num_rows>0){
        while ($entries=$result2->fetch_assoc()){
            switch ($entries['entry_type']){
                case 0:
                    ?>
                    <tr>
                    <td class="calc_fancy_td"></td>
                    <td class="calc_fancy_td"><input class='table_input' type="text" maxlength="250" name='entry_text[]' value="<?php echo $entries['entry_text'];?>"></td>
                    <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $entries['entry_qty'];?>" maxlength="6" name='entry_qty[]'></td>
                    <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $entries['entry_price'];?>" maxlength="6" name='entry_price[]'></td>
                    <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="<?php echo $entries['entry_discount'];?>" maxlength="6" name='entry_discount[]'></td>
                    <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="<?php echo $entries['entry_price']*$entries['entry_qty']*(1-$entries['entry_discount']*0.01);?>"></td>
                    <td class="calc_fancy_td calc_action_td"><input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
                        <input type="hidden" name='entry_type[]' value='0'>
                        <input type="hidden" name='entry_base_id[]' value='0'>
                    </td>
                    </tr>
                    <?php
                    break;
                case 2:
                    ?>
                    <tr>
                    <td class="calc_fancy_td"></td>
                    <td class="calc_fancy_td"><?php echo calc_selector_nmnc($entries['entry_base_id'],$entries['entry_text']); ?></td>
                    <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $entries['entry_qty'];?>" maxlength="6" name='entry_qty[]'></td>
                    <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $entries['entry_price'];?>" maxlength="6" name='entry_price[]'></td>
                    <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="<?php echo $entries['entry_discount'];?>" maxlength="6" name='entry_discount[]'></td>
                    <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="<?php echo $entries['entry_price']*$entries['entry_qty']*(1-$entries['entry_discount']*0.01);?>"></td>
                    <td class="calc_fancy_td calc_action_td" ><input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
                        <input type="hidden" name='entry_type[]' value='2'>
                    </td></tr>
                    <?php
                    break;
            }
        }  
    }
    ?>
    </tbody><tbody>
    <?php
    //OTHER
    $query='SELECT * FROM service_calc_entries WHERE entry_type=4 AND entry_related_id='.$service_id.'';
    $result2=$db->query($query);
    if ($result2->num_rows>0){
        while ($entries=$result2->fetch_assoc()){
            ?>
            <tr>
            <td class="number_td calc_fancy_td"><strong><?php echo $i;?></strong></td>
            <td class="calc_fancy_td"><input class='table_input input_header' type="text" maxlength="250" name='entry_text[]' value="<?php echo $entries['entry_text'];?>"></td>
            <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="1" onchange="total_calc()" value="<?php echo $entries['entry_qty'];?>" maxlength="6" name='entry_qty[]'></td>
            <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" value="<?php echo $entries['entry_price'];?>" maxlength="6" name='entry_price[]'></td>
            <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="<?php echo $entries['entry_discount'];?>" maxlength="6" name='entry_discount[]'></td>
            <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6" value="<?php echo $entries['entry_price']*$entries['entry_qty']*(1-$entries['entry_discount']*0.01);?>">
                <input type="hidden" name='entry_type[]' value='4'>
                <input type="hidden" name='entry_base_id[]' value='0'>
            </td>
            <td class="calc_action_td calc_fancy_td">
                <input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
            </td>
            </tr>
            <?php
            $i++;
        }  
    }
    ?>
            <tr><td colspan="7"><input type="button" class='small_button' value="Add other expenses" onclick="entry_header_calc(this)"></tr>
            </tbody>
        </table>
        <div style="text-align: right; width:100%;">
            <span><strong>Total:</strong>
                <input id="total" type='number' step="0.01" style="display:inline-block;min-width:150px; border:1px solid black" name="service_total" value="<?php echo $row['service_total'];?>">
            </span>
        </div>
        <?php } ?>
    </form>
        <!-- INVOICE Related -->
        <div id="invoices_related" class="nd_body nd_tabdiv nd_block nd_tabdiv_inactive">
            <div class="block_div2"><a class="knopka" href="#" onclick="invoice_new(<?php echo '1,\'',$on->order; ?>')">Create new invoice</a>
            </div>
            <?php
            $our_order_type=1;
            $our_order_comp=$service_our_comp;
            $our_order_id=$service_no;
            ?>
            <div class="related_invoices_container">
                <div style="margin:5px 0px;"><strong>Outgoing invoices:</strong></div>
                <?php
                $incoming_amount = 0;
                $incoming_payment = 0;
                $outgoing_amount = 0;
                $outgoing_payment = 0;
                $inv_query='SELECT invoices.*,customers.cust_id,customers.cust_short_name,invoices_statuses.inv_stat_name, our_companies.our_name,curr_name, '
                    . '(SELECT SUM(pay_amount)FROM invoices_payments WHERE pay_inv_id=invoices.invoice_id) AS payment_received '
                    . 'FROM invoices '
                    . 'LEFT JOIN customers ON invoice_customer=cust_id '
                    . 'LEFT JOIN invoices_statuses ON inv_stat_id=invoice_status '
                    . 'LEFT JOIN our_companies ON our_companies.id=invoice_our_comp '
                    . 'LEFT JOIN currency ON curr_id=invoice_currency '
//                        . 'WHERE invoice_order_type='.$our_order_type.' AND invoice_type=2 AND invoice_order_comp='.$our_order_comp.' AND invoice_order_num="'.$our_order_id.'"';
                    . 'WHERE invoice_order_num="'.$on->order.'" AND invoice_type=2';
                $inv_result=$db->query($inv_query);
                ?>
                <table id="invoice_table" class="sort_table" width="100%" rules="columns"  border="1px" cellspacing = "0" cellpadding="2px">
                    <thead>
                    <th width="30px"></th>
                    <th width="100px">Invoice number</th>
                    <th width="80px">Status</th>
                    <th width="80px">Invoice date</th>
                    <th width="80px">Our company</th>
                    <th >Customer</th>
                    <th width="50px">Currency</th>
                    <th width="100px">Amount</th>
                    <th width="100px">Payments</th>
                    <th width="100px">Balance</th>
                    </thead><tbody>
                <?php
                while($invoice = $inv_result->fetch_assoc()){
                    if ($invoice['invoice_is_cn']==='0')$type='INV'; else $type='CN';
                    $incoming_amount+=$invoice['invoice_total'];
                    $incoming_payment+=$invoice['payment_received'];
                    echo '<tr>'
                    . '<td>',$type,'</td>'
                    , '<td class="num">',view_invoice_by_id($invoice['invoice_id'],$invoice['invoice_num']),'</td>'
                    , '<td class="',invoice_color_table($invoice['invoice_status']),'">',$invoice['inv_stat_name'],'</td>'
                    , '<td>',$invoice['invoice_date'],'</td>'
                    , '<td>',$invoice['our_name'],'</td>'
                    , '<td>',view_company_link($invoice['cust_short_name'],$invoice['cust_id']),'</td>'
                    , '<td>',$invoice['curr_name'],'</td>'
                    , '<td class="align_right">',number_format($invoice['invoice_total'],2,'.',' '),'</td>'
                    , '<td class="align_right">',number_format($invoice['payment_received'],2,'.',' '),'</td>'
                    , '<td class="align_right">',number_format(($invoice['invoice_total']-$invoice['payment_received']),2,'.',' '),'</td>'
                    , '</tr>';
                }
                ?>
                        <tr>
                            <td class="align_right" colspan="7"><strong>Total:</strong></td>
                            <td class="align_right"><strong><?php echo number_format($incoming_amount,2,'.',' ');?></strong></td>
                            <td class="align_right"><strong><?php echo number_format($incoming_payment,2,'.',' ');?></strong></td>
                            <td class="align_right"><strong><?php echo number_format($incoming_amount-$incoming_payment,2,'.',' ');?></strong></td>
                        </tr>
                </table>
                <div style="margin:5px 0px;"><strong>Incoming invoices:</strong></div>
                <?php
                $inv_query='SELECT invoices.*,customers.cust_id,customers.cust_short_name,invoices_statuses.inv_stat_name, our_companies.our_name,curr_name, '
                    . '(SELECT SUM(pay_amount)FROM invoices_payments WHERE pay_inv_id=invoices.invoice_id) AS payment_received '
                    . 'FROM invoices '
                    . 'LEFT JOIN customers ON invoice_customer=cust_id '
                    . 'LEFT JOIN invoices_statuses ON inv_stat_id=invoice_status '
                    . 'LEFT JOIN our_companies ON our_companies.id=invoice_our_comp '
                    . 'LEFT JOIN currency ON curr_id=invoice_currency '
                    . 'WHERE invoice_order_num="'.$on->order.'" AND invoice_type=1';
                $inv_result=$db->query($inv_query);
                ?>
                <table id="invoice_table" class="sort_table" width="100%" rules="columns"  border="1px" cellspacing = "0" cellpadding="2px">
                    <thead>
                    <th width="30px"></th>
                    <th width="100px">Invoice number</th>
                    <th width="80px">Status</th>
                    <th width="80px">Invoice date</th>
                    <th width="80px">Our company</th>
                    <th >Customer</th>
                    <th width="50px">Currency</th>
                    <th width="100px">Amount</th>
                    <th width="100px">Payments</th>
                    <th width="100px">Balance</th>
                    </thead><tbody>
                <?php
                while($invoice = $inv_result->fetch_assoc()){
                    $outgoing_amount+=$invoice['invoice_total'];
                    $outgoing_payment+=$invoice['payment_received'];
                    if ($invoice['invoice_is_cn']==='0')$type='INV'; else $type='CN';
                    echo '<tr>'
                    . '<td>',$type,'</td>'
                    , '<td class="num">',view_invoice_by_id($invoice['invoice_id'],$invoice['invoice_num']),'</td>'
                    , '<td class="',invoice_color_table($invoice['invoice_status']),'">',$invoice['inv_stat_name'],'</td>'
                    , '<td>',$invoice['invoice_date'],'</td>'
                    , '<td>',$invoice['our_name'],'</td>'
                    , '<td>',view_company_link($invoice['cust_short_name'],$invoice['cust_id']),'</td>'
                    , '<td>',$invoice['curr_name'],'</td>'
                    , '<td class="align_right">',number_format($invoice['invoice_total'],2,'.',' '),'</td>'
                    , '<td class="align_right">',number_format($invoice['payment_received'],2,'.',' '),'</td>'
                    , '<td class="align_right">',number_format(($invoice['invoice_total']-$invoice['payment_received']),2,'.',' '),'</td>'
                    , '</tr>';
                }
                ?>
                        <tr>
                            <td class="align_right" colspan="7"><strong>Total:</strong></td>
                            <td class="align_right"><strong><?php echo number_format($outgoing_amount,2,'.',' ');?></strong></td>
                            <td class="align_right"><strong><?php echo number_format($outgoing_payment,2,'.',' ');?></strong></td>
                            <td class="align_right"><strong><?php echo number_format($outgoing_amount-$outgoing_payment,2,'.',' ');?></strong></td>
                        </tr>
                </table>
                <?php
                $expected_balance=$incoming_amount-$outgoing_amount;
                if ($expected_balance>0){$exp_blance_color = "green";} else {$exp_blance_color = "red";}
                $fact_balance=$incoming_payment-$outgoing_payment;
                if ($fact_balance>0){$blance_color = "green";} else {$blance_color = "red";}
                ?>
                <div class="align_center">
                    <strong>
                        Expected balance: <span style="color: <?php echo $exp_blance_color;?>"><?php echo number_format($expected_balance,2,'.',' ');?></span>
                         | 
                        Fact balance: <span style="color: <?php echo $blance_color;?>"><?php echo number_format($fact_balance,2,'.',' ');?></span>
                    </strong>
                </div>
            </div>
        </div>
        <!-- STOCK Related -->
        <div id="stock_related" class="nd_body nd_block nd_tabdiv nd_tabdiv_inactive">
            <table class="sort_table" width="100%">
            <thead>
                <th colspan="2" width="50">ID</th>
                <th width="50">Status</th>
                <th width="75">Category</th>
                <th width="100">Maker</th>
                <th width="100">P/N or type</th>
                <th width="75">Type\model</th>
                <th>Description</th>
                <th width="100">Serial</th>
                <th width="75">Stock</th>
                <th width="25">Place</th>
                <th width="25">Condition</th>
                <th>Note</th>
            </thead>
            <tbody>
                <tr><td colspan="13"><strong>Materials supplied:</strong></td></tr>
            <?php
            $query2='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name,'
                . 'stockl_id, stockl_name, stock_cat_name, curr_symb, our_companies.id, our_companies.our_name '
                . 'FROM stock_new '
                . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
                . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
                . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
                . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
                . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
                . 'LEFT JOIN currency ON stock_currency=curr_id '
                . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
//                    . 'WHERE stock_new.stock_deleted=0 AND stock_so_type=2 AND stock_so_comp="'.$service_our_comp.'" AND CAST(stock_so AS INTEGER)="'.$service_no.'"';
                . 'WHERE stock_new.stock_deleted=0 AND stock_so="'.$on->order.'"';
            $result2=$db->query($query2);
            while($row2 = $result2->fetch_assoc()){
            echo '<tr class="',stock_tr_is_sold($row2['stock_status']),'">';
            echo stock_view_link($row2['stock_id'],$row2['stock_compl_id'],$row2['stock_is_compl'])
                    , '<td ',color_table($row2['stock_status'],$row['stock_condition']),'>',$row2['stock_stat_name'],'</td>'
                    , '<td>',$row2['stock_cat_name'],'</td>'
                    , '<td>',$row2['mnf_short_name'],'</td>'
                    , '<td>',$row2['stnmc_pn'],'</td>'
                    , '<td>',$row2['stnmc_type_model'],'</td>'
                    , long_td_string($row2['stnmc_descr'],60)
                    , '<td>',$row2['stock_serial'],'</td>'
                    , '<td>',view_stocklist_item($row2['stock_stock_id'], $row2['stockl_name']).'</td>'
                    , '<td>',$row2['stock_place'],'</td>'
                    ,condition_decode($row2['stock_condition'])
                    , long_td_string($row2['stock_note'],20)
                    , '</tr>';
            }
            ?>
                <tr><td colspan="13"><strong>Materials received:</strong></td></tr>
            <?php
            $query2='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name,'
                . 'stockl_id, stockl_name, stock_cat_name, curr_symb, our_companies.id, our_companies.our_name '
                . 'FROM stock_new '
                . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
                . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
                . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
                . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
                . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
                . 'LEFT JOIN currency ON stock_currency=curr_id '
                . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
//                    . 'WHERE stock_new.stock_deleted=0 AND stock_po_type=2 AND stock_po_comp="'.$service_our_comp.'" AND CAST(stock_po AS INTEGER)="'.$service_no.'"';
                . 'WHERE stock_new.stock_deleted=0 AND stock_po="'.$on->order.'"';
            $result3=$db->query($query2);
            while($row2 = $result3->fetch_assoc()){
            echo '<tr class="',stock_tr_is_sold($row2['stock_status']),'">';
            echo stock_view_link($row2['stock_id'],$row2['stock_compl_id'],$row2['stock_is_compl'])
                    , '<td ',color_table($row2['stock_status'],$row['stock_condition']),'>',$row2['stock_stat_name'],'</td>'
                    , '<td>',$row2['stock_cat_name'],'</td>'
                    , '<td>',$row2['mnf_short_name'],'</td>'
                    , '<td>',$row2['stnmc_pn'],'</td>'
                    , '<td>',$row2['stnmc_type_model'],'</td>'
                    , long_td_string($row2['stnmc_descr'],60)
                    , '<td>',$row2['stock_serial'],'</td>'
                    , '<td>',view_stocklist_item($row2['stock_stock_id'], $row2['stockl_name']).'</td>'
                    , '<td>',$row2['stock_place'],'</td>'
                    ,condition_decode($row2['stock_condition'])
                    , long_td_string($row2['stock_note'],20)
                    , '</tr>';
            }
            ?>
            </tbody></table>
        </div>
        <!-- FILES -->            
        <div id="uploaded_files" class="nd_body nd_block nd_tabdiv nd_tabdiv_inactive"></div>
        <div class="nd_block nd_footer">
            <table width="100%">
                <tr>
                    <td class="align_left">
                        <span style="font-style: italic">Last modified by: <?php echo $row['full_name']; ?> at <?php echo $row['modified_date']; ?></span>
                    </td>
                    <td class="align_center">
                        <input type="button" value="Save" class="nd_button_green" onclick="service_view_form_submit(this)">
                    </td>
                </tr>
                <tr>
                    <td class="align_left">
                        <div class="service_download_menu">
                            <div class="service_download_menu_header"><strong>Downloads</strong></div>
                            <div class="service_download_menu_body">
                                <a class="knopka" href="/scripts/service_order_doc.php?id=<?php echo $service_id;?>"><img class="line_image" src="./icons_/download-button-white.png"> Service task</a><br>
                                <a class="knopka" href="/scripts/service_report_doc.php?id=<?php echo $service_id;?>"><img class="line_image" src="./icons_/download-button-white.png"> Service report</a><br>
                                <a class="knopka" href="/scripts/service_report_doc_azmp.php?id=<?php echo $service_id;?>"><img class="line_image" src="./icons_/download-button-white.png"> Service report (AZMP)</a>
                                <a class="knopka" href="/scripts/print_service_stock_form.php?id=<?php echo $service_id;?>"><img class="line_image" src="./icons_/download-button-white.png"> Download Spare parts list</a>
                            </div>
                        </div>
                    </td>
                    <td class="align_center"><button class="nd_button" onclick="check_copy(<?php echo (int)$service_id;?>)">Copy this order</button></td>
                    <td class="align_right">
                        <a class="nd_button" href="/scripts/service_calc_excel.php?service_id=<?php echo $row['service_id'];?>">Download Invoice</a>
                        <a class="nd_button" href="/scripts/service_calc_cn_excel.php?service_id=<?php echo $row['service_id'];?>">Download Credit Note</a>
                    </td>
                </tr>
            </table>
        </div>
    <!-- ROWS_FOR_INSERT-->
    <table style='display: none'>
    <tr id='entry_freeline_tr'>
        <td class="calc_fancy_td"></td>
        <td class="calc_fancy_td"><input class='table_input' type="text" maxlength="250" name='entry_text[]'></td>
        <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="0.01" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
        <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" maxlength="6" name='entry_price[]'></td>
        <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="0" maxlength="6" name='entry_discount[]'></td>
        <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6"></td>
        <td class="calc_fancy_td calc_action_td">
            <input type="hidden" name='entry_type[]' value='0'>
            <input type="hidden" name='entry_base_id[]' value='0'>
            <input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
        </td>
    </tr>
    <tbody  id='entry_header_tr' class="rate_body"><tr>
        <td class="number_td"></td>
        <td><?php echo comboselect_rates_cat('');?></td>
        <td colspan="4"></td>
        <td class="calc_action_td">
            <input type='hidden' value='0' class="qty_input" name='entry_qty[]'>
            <input type='hidden' value='0' class="price_input" name='entry_price[]'>
            <input type='hidden' value='0' class="discount_input" name='entry_discount[]'>
            <input type='hidden' value='0' class="amount_input">
            <input type="hidden" name='entry_type[]' value='3'>
            <input type="button" class='small_button' value="Add rate category" onclick="entry_header(this)">
            <input type="button" class='small_button' value="Add" onclick="entry_rate(this)">
            <input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
        </td>
    </tr></tbody>
    <tr id='entry_header_calc_tr'>
        <td class="calc_fancy_td number_td"></td>
        <td class="calc_fancy_td"><input class='table_input input_header' type="text" maxlength="250" name='entry_text[]'></td>
        <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="0.01" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
        <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" maxlength="6" name='entry_price[]'></td>
        <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="0" maxlength="6" name='entry_discount[]'></td>
        <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6"></td>
        <td class="calc_fancy_td calc_action_td">
            <input type="hidden" name='entry_type[]' value='4'>
            <input type="hidden" name='entry_base_id[]' value='0'>
            <input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
        </td>
    </tr>
    <tr id='entry_rate_tr'>
        <td class="calc_fancy_td"></td>
        <td class="calc_fancy_td"><?php echo comboselect_rates('');?></td>
        <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="0.01" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
        <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" maxlength="6" name='entry_price[]'></td>
        <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="0" maxlength="6" name='entry_discount[]'></td>
        <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6"></td>
        <td class="calc_fancy_td calc_action_td">
            <input type="hidden" name='entry_type[]' value='1'>
            <input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
        </td>
    </tr>
    <tr id='entry_spare_tr'>
        <td class="calc_fancy_td"></td>
        <td class="calc_fancy_td"><?php echo calc_selector_nmnc(0,'No spares');?></td>
        <td class="calc_fancy_td"><input class='table_input qty_input' type='number' step="0.01" onchange="total_calc()" value="1" maxlength="6" name='entry_qty[]'></td>
        <td class="calc_fancy_td"><input class='table_input price_input' type='number' step="0.01" onchange="total_calc()" maxlength="6" name='entry_price[]'></td>
        <td class="calc_fancy_td"><input class='table_input discount_input' type='number' step="1" onchange="total_calc()" value="0" maxlength="6" name='entry_discount[]'></td>
        <td class="calc_fancy_td"><input class='table_input amount_input' type='number' step="0.01" readonly maxlength="6"></td>
        <td class="calc_fancy_td calc_action_td">
            <input type="hidden" name='entry_type[]' value='2'>
            <input type="button" class='small_button red_button' value="Delete" onclick="sevice_delete_row(this)">
        </td>
    </tr>
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