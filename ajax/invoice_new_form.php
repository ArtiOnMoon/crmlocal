<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/invoice_fns.php';
require_once '../functions/selector.php';

$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);
$order_list = [1 => 'Service', 2 => 'Sales', 3 => 'Purchase'];
$invoice_our_comp=clean($_POST['invoice_our_comp']);
$invoice_num=clean($_POST['invoice_num']);
$invoice_id=clean($_POST['invoice_id']);

$invoice_order_type=clean($_POST['invoice_order_type']);
//$invoice_order_comp=$_POST['invoice_order_comp'];
$invoice_order_num=clean($_POST['invoice_order_num']);

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
    <div class="nd_header">
        <div><h1>New invoice\Credit note</h1></div>
        <button class="nd_close_button" onclick="window_close(this)">&#10006;</button>
    </div>
    <div class="nd_subheader nd_tabs">
    </div>
    <form id="invoice_main_form" class="nd_body nd_tabdiv invoice_grid_conteiner bank_details_container" name="change_invoice_form" method="post">
        <input type="hidden" name="invoice_id" value="<?php echo $invoice_id;?>">
        <div class="nd_block inv_num">
            <label class="nd_label">Number</label>
            <?php echo select_invoice_cn(0,'class="nd_select2" name="invoice_is_cn"',0);?>
            <input class="nd_input_short" size="10" type="text" required name="invoice_num" value="<?php echo $invoice_num;?>">
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
    
   
    <div style="display:none;">
        <table>
            <tbody id="invoice_quotation_new_line" class="quotation_line">
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
        </table>
    </div>
</div>