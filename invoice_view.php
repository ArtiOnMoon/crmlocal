<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'functions/invoice_fns.php';

$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);
$order_list = [1 => 'Service', 2 => 'Sales', 3 => 'Purchase'];
$invoice_our_comp=clean($_POST['invoice_our_comp']);
$invoice_num=clean($_POST['invoice_num']);
$invoice_id=clean($_POST['invoice_id']);
?>
<link href="/css/invoices.css" rel="stylesheet">
<div class="window_internal" style="width:90%; max-width: 1280px; height:90%; min-height: 720px;">
    <datalist id='pay_terms_list'>
        <option value='In advance'>
        <option value='Net 15 days'>
        <option value='Net 30 days'>
        <option value='Net 60 days'>
        <option value='Net 90 days'>
    </datalist>
    <div class="nd_main_grid">
        
<?php
$db =  db_connect();
$query= 'SELECT invoices.*, our_companies.*, curr_name,users.full_name '
        . 'FROM invoices '
        . 'LEFT JOIN currency ON invoices.invoice_currency=curr_id '
        . 'LEFT JOIN our_companies ON invoice_our_comp=our_companies.id '
        . 'LEFT JOIN users ON invoice_modified = uid ';
if (isset($_POST['invoice_id']))$query.='WHERE invoice_id="'.$invoice_id.'"';
else $query.='WHERE invoice_num="'.$invoice_num.'" AND invoice_our_comp="'.$invoice_our_comp.'"';
$result=$db->query($query);
if ($result-> num_rows!==1){
    echo 'Invoice not found'.$db->error;
    echo '<button class="nd_close_button" onclick="window_close(this)">&#10006;</button>';
    exit();
}
$row=$result->fetch_assoc();
$invoice_id=(int)$row['invoice_id'];
$invoice_num=$row['invoice_num'];
$invoice_our_comp=$row['invoice_our_comp'];
$invoice_type=invoice_cn_decode($row['invoice_is_cn']);
$type=$row['invoice_type'];

if(check_access('acl_invoices', 1, $invoice_our_comp)) {exit('Access denied.</div>');}
?>
        <div class="nd_header">
            <div><h1><?php echo $invoice_type;?> # <?php echo $invoice_num;?></h1></div>
            <button class="nd_close_button" onclick="window_close(this)">&#10006;</button>
        </div>
        <div class="nd_subheader nd_tabs">
            <div class="nd_tab nd_tab_active" onclick="switchTab(this)" data-tab="invoice_main_form">Main</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="shipment">Shipment info</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="uploaded_files">Uploaded files</div>
        </div>
       <form id="invoice_main_form" class="nd_body nd_tabdiv invoice_grid_conteiner bank_details_container" name="change_invoice_form" method="post">
            <input type="hidden" name="invoice_id" value="<?php echo $invoice_id;?>">
            <div class="nd_block inv_num">
                <label class="nd_label"><?php echo $invoice_type;?> №</label>
                <input class="nd_input inv_limited_input" type="text" required name="invoice_num" value="<?php echo $invoice_num;?>">
            </div>
            <div class="nd_block inv_date">
                <label class="nd_label">Date</label>
                <input class="nd_input datepicker" type="text" placeholder="yyyy-mm-dd" name="invoice_date" value="<?php echo $row['invoice_date'];?>">
            </div>
            <div class="nd_block inv_status">
                <label class="nd_label">Status</label>
                <?php echo select_invoice_status($row['invoice_status'],'name="invoice_status" class="nd_select"');?>
            </div>
            <div class="nd_block inv_order">
                <label class="nd_label">Our order</label>
                <input class="nd_input inv_limited_input" type="text" name="invoice_order_num" value="<?php echo $row['invoice_order_num'];?>"> <a href="#" onclick="view_link('<?php echo $row['invoice_order_num'];?>')"><img class="line_image" align="middle" src="/icons_/ex_link.png"></a>
            </div>
            <div class="nd_block inv_type">
                <label class="nd_label">Type</label>
                <?php echo select_invoice_type($row['invoice_type'],'name="invoice_type" class="nd_select"');?>
            </div>
            <div class="nd_block inv_comp">
                <label class="nd_label">Our company</label>
                <?php echo select_our_company2($comp_list,$invoice_our_comp,'onchange="get_our_bank_det(this)" reqiured class="bank_det_company nd_select" name="invoice_our_comp"'); ?>
            </div>
            <div class="nd_block inv_currency">
                <label class="nd_label">Currency</label>
                <?php echo select_currency2($cur_list,$row['invoice_currency'],'onchange="get_our_bank_det(this)" class="bank_det_currency nd_select" name="invoice_currency" required');?>
            </div><div class="nd_block inv_bank">
                <label class="nd_label">Bank</label>
                <?php echo select_our_bank_det_ajax($invoice_our_comp,$row['invoice_our_bank_det'],$row['invoice_currency'],'class="our_bank_det nd_select" name="invoice_our_bank_det"');?>
            </div>
            <div id="grid_invoice_details" class="nd_block inv_payment">
                <div class="align_center"><h3>Payments</h3></div>
                <table width="100%">
                    <thead><th>Date</th><th>Amount</th><th>Edit</th></thead>                
                <?php
                $query='SELECT * FROM invoices_payments WHERE pay_inv_id="'.$invoice_id.'"';
                $result=$db->query($query);
                $total_payments=0;
                if (!$result)echo 'Nothing found'.$db->error;
                while ($payment=$result->fetch_assoc()){
                    $total_payments+=$payment['pay_amount'];
                    echo'<tr>'
                    . '<td>',$payment['pay_date'],'</td>'
                    . '<td>',$payment['pay_amount'],'</td>'
                    . '<td><a href="#" onclick="invoice_payment_edit(this,\'',$payment['pay_id'],'\')">Edit</a></td></tr>';
                }
                ?>
                <tr style="border-top:2px solid black;">
                    <td><strong>Total:</strong></td><td><?php echo $total_payments;?></td>
                    <td><a href="#" class="knopka3" onclick="invoice_add_payment(this,<?php echo $invoice_id;?>)">ADD</a></td></tr>
                </table>
            </div>
            <div class="nd_block inv_cust">
                <label class="nd_label">Customer</label>
                <span class="customer_conteiner">
                    <?php echo selector('customers','name="invoice_customer"',$row['invoice_customer']);?>
                    <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </span>
            </div>
            <div class="nd_block inv_cust_ref">
                <label class="nd_label">Customer's ref.</label>
                <input class="nd_input" type="text" maxlength="50" name="invoice_cust_ref" value="<?php echo $row['invoice_cust_ref'];?>">
            </div>
            <div class="nd_block inv_pay_terms">
                <label class="nd_label">Payment terms</label>
                <input class="nd_input" type="text" name="invoice_pay_terms" value="<?php echo $row['invoice_pay_terms'];?>" list='pay_terms_list'>
            </div>
            <div class="nd_block inv_pay_terms">
                <label class="nd_label">Payment terms</label>
                <input class="nd_input" type="text" name="invoice_pay_terms" value="<?php echo $row['invoice_pay_terms'];?>" list='pay_terms_list'>
            </div>
            <div class="nd_block inv_vat">
                <label class="nd_label">VAT remarks</label>
                <?php echo select_vat_rem($row['invoice_vat_remarks'],'name="invoice_vat_remarks" class="nd_select"');?>
            </div>
            <div class="nd_block inv_instr">
                <label class="nd_label">Invoicing instructions
                    <input type="checkbox" value="1" name="inv_instructions" <?php if($row['inv_instructions']==1)echo 'checked'; ?> onchange="disabled_control(this,'inv_instructions_div')">
                </label>
                <div <?php if($row['inv_instructions']=='0') echo 'class="disabledbutton"'; ?> id="inv_instructions_div">
                    <table class="nd_subtable">
                        <tr>
                            <td><b>Invoice to:</b></td>
                            <td><input type="text" name="inv_inst_comp_name" placeholder="Company name" maxlength="100" size="40" value="<?php echo $row['inv_inst_comp_name'];?>"></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="text" name="inv_inst_comp_name2" placeholder="Company name line 2" maxlength="100" size="40" value="<?php echo $row['inv_inst_comp_name2'];?>"></td>
                        </tr>
                        <tr>
                            <td><b>Address:</b></td><td>
                                <input type="text" name="inv_inst_comp_addr1" maxlength="200" size="40" placeholder="Office, street, building" value="<?php echo $row['inv_inst_comp_addr1'];?>">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="text" name="inv_inst_comp_addr2" maxlength="200" size="40" placeholder="City, post code, country" value="<?php echo $row['inv_inst_comp_addr2'];?>"></td>
                        </tr>
                        <tr>
                            <td><b>Country:</b></td>
                            <td><?php echo select_country($row['inv_inst_comp_country'], 'name="inv_inst_comp_country"');?>
                        </tr>
                        <tr>
                            <td><b>VAT:</b></td>
                            <td><input type="text" name="inv_inst_comp_vat" maxlength="15" size="40" value="<?php echo $row['inv_inst_comp_vat'];?>"></td>
                        </tr>
                        <tr>
                            <td><b>E-mail:</b></td>
                            <td><input type="text" name="inv_inst_comp_email" maxlength="100" size="40" value="<?php echo $row['inv_inst_comp_email'];?>"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="invoice_body_conteiner nd_block invoice_content">
                <h2>Content</h2> 
                <table class="invoice_body_table">
                    <thead class="invoice_quotation_thead">
                        <th class="invoice_col_no">№</th>
                        <th class="invoice_col_control"></th>
                        <th class="invoice_col_descr">Description</th>
                        <th class="invoice_col_qty">Q-ty</th>
                        <th class="invoice_col_price">Price</th>
                        <th class="invoice_col_discount">Discount</th>
                        <th class="invoice_col_amount">Amount</th>
                        <th class="invoice_col_note">Note</th>
                        <th class="invoice_col_delete"></th>
                    </thead>
                    <?php
                    $query= 'SELECT * FROM invoices_content WHERE inv_con_inv_id="'.$invoice_id.'"';
                    if (!$result=$db->query($query)){
                        echo $db->error;
                    }
                    if ($result->num_rows > 0){
                        $i=1;
                        while($line=$result->fetch_assoc()){
                            if ($line['inv_con_type'] == 0){
                            ?>
                            <tbody class="quotation_line">
                            <tr>
                                <td class="po_col_no"><?php echo $i++;?></td>
                                <td class="po_col_control">
                                    <input type="button" value="&#9650;" onclick="invoice_row_up(this)">
                                    <input type="button" value="&#9660;" onclick="invoice_row_down(this)">
                                </td>
                                <td><?php echo selector_nmnc_linear('name="inv_con_base_id[]" onchange="qte_total(this)"','name="inv_con_text[]"',$line['inv_con_text'],$line['inv_con_base_id']);?></td>
                                <td class="po_col_qty"><input type="number" step="1" name="inv_con_qty[]" class="inp_qty" style="width:100%;" onchange="qte_total(this)" value="<?php echo $line['inv_con_qty'];?>"></td>
                                <td class="po_col_price"><input type="number" step="0.01" name="inv_con_price[]" class="inp_price align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo number_format($line['inv_con_price'], 2,'.','');?>"></td>
                                <td class="po_col_discount"><input type="number" step="1" name="inv_con_discount[]" class="inp_discount align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo $line['inv_con_discount'];?>"></td>
                                <td class="po_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="<?php echo number_format($line['inv_con_price']*$line['inv_con_qty']*(1-$line['inv_con_discount']/100),2,'.','');?>"></td>
                                <td class="po_col_note"><input type="text" name="inv_con_note[]" style="width:100%;" value="<?php echo $line['inv_con_note'];?>"></td>
                                <td class="po_col_delete"><button type="button" onclick="invoice_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
                            </tr>
                            </tbody>
                            <?php
                            }
                            else{
                            ?>
                                <tbody class="quotation_line">
                                    <tr>
                                        <td></td>
                                        <td class="po_col_control">
                                            <input type="button" value="&#9650;" onclick="invoice_row_up(this)">
                                            <input type="button" value="&#9660;" onclick="invoice_row_down(this)">
                                        </td>
                                        <td colspan="6"><input type="text" name="inv_con_text[]" class="inv_con_header" style="width:100%;" value="<?php echo $line['inv_con_text'];?>"></td>
                                        <td style="display:none;">
                                            <input type="hidden" name="inv_con_type[]" class="inp_qty" value="1">
                                            <input type="hidden" step="1" name="inv_con_qty[]" class="inp_qty" value="1">
                                            <input type="hidden" step="0.01" name="inv_con_price[]" class="inp_price align_right" value="0.00">
                                            <input type="hidden" step="1" name="inv_con_discount[]" class="inp_discount align_right" value="0">
                                            <input type="hidden" step="0.01" style="width:100%;" class="inp_amount align_right" value="0.00">
                                            <input type="text" name="inv_con_note[]" style="width:100%;">
                                        </td>
                                        <td class="po_col_delete"><button type="button" onclick="invoice_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
                                    </tr>
                                </tbody>
                            <?php
                            }
                        }
                    }
                    ?>
                </table>
                <table width="100%">
                    <tr class="table_round_last_tr">
                        <td width="900px"><input type="button" value="Add content" onclick="invoice_add_content(this)"></td>
                        <!-- <td width="900px"><input type="button" value="Add text line" onclick="invoice_add_text_line(this)"></td> -->
                        <td style="text-align: right;"><b>Total:</b></td>
                        <td><input type="number" id="total_field" step="0.01" class="align_right" name="invoice_total" style="width:65px;border:2px solid black;" value="<?php echo number_format($row['invoice_total'],2,'.',''); ?>"></td>
                        <td class="sales_col_delete"></td>
                    </tr>
                </table>
            </div>
            <div class="nd_block invoice_note">
                <label class="nd_label">Note:</label>
                <textarea name="invoice_note" class="invoice_note nd_textarea"><?php echo $row['invoice_note'];?></textarea>
            </div>
        </form>
        <!-- Shipment -->
        <form id="shipment" class="nd_block nd_tabdiv nd_tabdiv_inactive">
            <table class="table_round2" width="100%">
                <col width="25%"><col width="25%"><col width="25%"><col width="25%">
                <tr><td colspan="4"><label>Shipment required <input type="checkbox" name="invoice_ship_req" <?php if($row['invoice_ship_req']==='1') echo'checked';?>></label></td></tr>
                <tr>
                <td class="fancy_td" colspan="2"><b>Shipped to:</b> &nbsp;&nbsp;
                    <label><input onchange="sales_shipped_to_func(this)" type="radio" value="2" <?php if($row['invoice_shipped_to_flag']==='2') echo'checked';?> name="invoice_shipped_to_flag"> Enter manualy</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <label><input onchange="sales_shipped_to_func(this)" type="radio" value="0" <?php if($row['invoice_shipped_to_flag']==='0') echo'checked';?> name="invoice_shipped_to_flag"> Same as "Invoice to"</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <input onchange="sales_shipped_to_func(this)" type="radio" value="1" <?php if($row['invoice_shipped_to_flag']==='1') echo'checked';?> name="invoice_shipped_to_flag">
                    <span class="shipped_to_conteiner1 customer_conteiner <?php if($row['invoice_shipped_to_flag']!=='1') echo'disabled';?>">
                        <?php echo selector('customers','name="invoice_shipped_to"',$row['invoice_shipped_to']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                    </span>
                    <div class="shipped_to_conteiner2 <?php if($row['invoice_shipped_to_flag']!=='2') echo 'disabled';?>">
                        <input type="text" style="width:100%" name="invoice_shipped_name" placeholder="Company name" value="<?php echo $row['invoice_shipped_name'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_addr1" placeholder="Street, building, office" value="<?php echo $row['invoice_shipped_addr1'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_addr2" placeholder="ZIP, City" value="<?php echo $row['invoice_shipped_addr2'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_country" placeholder="Country" value="<?php echo $row['invoice_shipped_country'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_vat" placeholder="VAT number" value="<?php echo  $row['invoice_shipped_vat'];?>">
                    </div>
                </td>
                <td class="fancy_td" colspan="2"><b>Shipped from</b> &nbsp;&nbsp;
                    <label><input onchange="sales_shipped_from_func(this)" type="radio" value="2" <?php if($row['invoice_shipped_from_flag']==='2') echo'checked';?> name="invoice_shipped_from_flag"> Enter manualy</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <label><input onchange="sales_shipped_from_func(this)" type="radio" value="0" <?php if($row['invoice_shipped_from_flag']==='0') echo'checked';?> name="invoice_shipped_from_flag"> Same as "Invoice from"</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <input onchange="sales_shipped_from_func(this)" type="radio" value="1" <?php if($row['invoice_shipped_from_flag']==='1') echo'checked';?> name="invoice_shipped_from_flag">
                    <span class="shipped_from_conteiner1 customer_conteiner <?php if($row['invoice_shipped_from_flag']!=='1') echo'disabled';?>">
                        <?php echo selector('customers','name="invoice_shipped_from"',$row['invoice_shipped_from']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                    </span>                
                    <div class="shipped_from_conteiner2 <?php if($row['invoice_shipped_from_flag']!=='2') echo 'disabled';?>">
                        <input type="text" style="width:100%" name="invoice_shipped_from_name" placeholder="Company name" value="<?php echo $row['invoice_shipped_from_name'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_from_addr1" placeholder="Street, building, office" value="<?php echo $row['invoice_shipped_from_addr1'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_from_addr2" placeholder="ZIP, City" value="<?php echo $row['invoice_shipped_from_addr2'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_from_country" placeholder="Country" value="<?php echo $row['invoice_shipped_from_country'];?>">
                        <input type="text" style="width:100%" name="invoice_shipped_from_vat" placeholder="VAT number" value="<?php echo $row['invoice_shipped_from_vat'];?>">
                    </div>
                </td>
                </tr>
                <tr>
                    <td><b>Shipped on</b></td>
                    <td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="invoice_ship_date" value="<?php echo $row['invoice_ship_date'];?>"></td>
                    <td><b>Shipped by</b></td>
                    <td><input type="text" maxlength="150" placeholder="DHL, UPS, TNT etc." name="invoice_shipped_on" value="<?php echo $row['invoice_shipped_on'];?>"></td>
                </tr>
                <tr>
                    <td><b>Delivery terms</b></td>
                    <td><input type="text" name="invocie_delevery_terms" maxlength="100" value="<?php echo $row['invocie_delevery_terms'];?>"></td>
                    <td><b>AWB</b></td>
                    <td><input type="text" name="invoice_awb" maxlength="150" value="<?php echo $row['invoice_awb'];?>"></td>
                </tr>
            </table>    
        </form>
        <!-- FILES -->
        <div id="uploaded_files" class="nd_body nd_tabdiv nd_tabdiv_inactive"></div>
        <div class="nd_footer nd_block invoice_footer">
            <div style="text-align:left;"><span style="font-style: italic">Last modified by: <?php echo $row['full_name']; ?> at <?php echo $row['invoice_modified_date']; ?></span></div>
            <input type="button" class="nd_button_green" value="Save" onclick="invoice_change_form(this,<?php  echo $type;?>)"> 
            <input type="button" class="nd_button" value="Close" onclick="window_close(this);"><br><br>
            <a class="knopka" href="/scripts/invoice_excel.php?id=<?php echo $invoice_id; ?>">Download invoice</a>
        </div>
    
    <!-- FOR INSERT -->
    <div style="display:none;">
        <table>
            <tbody id="invoice_quotation_new_line">
            <tr>
                <td class="po_col_no"></td>
                <td class="po_col_control">
                    <input type="button" value="&#9650;" onclick="invoice_row_up(this)">
                    <input type="button" value="&#9660;" onclick="invoice_row_down(this)">
                </td>
                <td><?php echo selector_nmnc_linear('name="inv_con_base_id[]" onchange="qte_total(this)"','name="inv_con_text[]"');?></td>
                <td class="po_col_qty"><input type="number" step="1" name="inv_con_qty[]" class="inp_qty" style="width:100%;" onchange="qte_total(this)" value="1"></td>
                <td class="po_col_price"><input type="number" step="0.01" name="inv_con_price[]" class="inp_price align_right" style="width:100%;" onchange="qte_total(this)" value="0.00"></td>
                <td class="po_col_discount"><input type="number" step="1" name="inv_con_discount[]" class="inp_discount align_right" style="width:100%;" onchange="qte_total(this)" value="0"></td>
                <td class="po_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="0.00"></td>
                <td class="po_col_note"><input type="text" name="inv_con_note[]" style="width:100%;"></td>
                <td class="po_col_delete"><button type="button" onclick="invoice_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
            </tr>
            </tbody>
            <tbody id="invoice_quotation_text_line">
            <tr>
                <td></td>
                <td class="po_col_control">
                    <input type="button" value="&#9650;" onclick="invoice_row_up(this)">
                    <input type="button" value="&#9660;" onclick="invoice_row_down(this)">
                </td>
                <td colspan="6"><input type="text" name="inv_con_text[]" class="inv_con_header" style="width:100%;"></td>
                <td style="display:none;">
                    <input type="hidden" name="inv_con_type[]" class="inp_qty" style="width:100%;" value="1">
                    <input type="hidden" step="1" name="inv_con_qty[]" class="inp_qty" style="width:100%;" value="1">
                    <input type="hidden" step="0.01" name="inv_con_price[]" class="inp_price align_right" style="width:100%;" value="0.00">
                    <input type="hidden" step="1" name="inv_con_discount[]" class="inp_discount align_right" style="width:100%;" value="0">
                    <input type="hidden" step="0.01" style="width:100%;" class="inp_amount align_right" value="0.00">
                    <input type="text" name="inv_con_note[]" style="width:100%;">
                </td>
                <td class="po_col_delete"><button type="button" onclick="invoice_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</div>