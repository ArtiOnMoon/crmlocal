<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/purchase_fns.php';
$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);
?>
<link href="/css/purchase.css" rel="stylesheet">
<div class="window_internal" style="width:90%; max-width: 1280px; height:90%; min-height: 720px;">
    <div class="nd_main_grid">
        <div class="nd_header">
            <div><h1>New PO</h1></div>
            <button class="nd_close_button" onclick="window_close(this)">&#10006;</button>
        </div>
        <div class="nd_subheader"></div>
        <form id="po_main_form" class="nd_body nd_tabdiv po_grid_conteiner" onsubmit="return add_new_purchase(this)" name="new_purchase_form">
            <div class="nd_block po_status">
                <label class="nd_label">Status</label>
                <?php echo select_po_status($row['po_status'],'required class="nd_select" name="po_status"');?>
            </div>
            <div class="nd_block po_date">
                <label class="nd_label">Date</label>
                <input class="nd_input datepicker" type="text" placeholder="yyyy-mm-dd" class="datepicker" required name="po_date" value="<?php echo $row['po_date'];?>">
            </div>
            <div class="nd_block po_our_comp">
                <label class="nd_label">Our company</label>
                <?php echo select_our_company2($comp_list,$row['po_our_comp'],'class="nd_select" required name="po_our_comp"'); ?>
            </div>
            <div class="nd_block po_currency">
                <label class="nd_label">Currency</label>
                <?php echo select_currency2($cur_list,$row['po_currency'],'class="nd_select" name="currency"');?>
            </div>
            <div class="nd_block po_customer">
                <label class="nd_label">Supplier</label>
                <span class="customer_conteiner">
                    <?php echo selector_customer('id="po_supplier" name="po_supplier"',$row['po_supplier']);?>
                    <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </span>
            </div>
            <div class="nd_block po_ship_date">
                <label class="nd_label">Shipment date</label>
                <input class="nd_input datepicker" type="text" name="po_ship_date" value="<?php echo $row['po_ship_date'];?>">
            </div>
            <div class="nd_block po_awb">
                <label class="nd_label">AWB</label>
                <input class="nd_input" type="text" maxlength="50" name="po_awb" value="<?php echo $row['po_awb'];?>">
            </div>
            <div class="nd_block po_invoice">
                <label class="nd_label">Invoice to:</label>
                <label><input name="po_invoice_to_flag" type="radio" value="0" checked> Us</label>
                <label><input name="po_invoice_to_flag" type="radio" value="1" <?php if($row['po_invoice_to_flag']==='1') {echo 'checked';}?>>
                    <span class="customer_conteiner"><?php echo selector('customers','name="po_invoice_to"',$row['po_invoice_to']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)"></span>
                </label>
            </div>
            <div class="nd_block po_delivery">
                <label class="nd_label"><b>Delivery address</b> (leave empty for default value):<br></label>
                <div class="po_delivery_conatiner">
                    <input class="po_delivery1 nd_input2" type="text" name="po_delivery1" value="<?php echo $row['po_delivery1'];?>" placeholder="Company name">
                    <input class="po_delivery2 nd_input2" type="text" name="po_delivery2" value="<?php echo $row['po_delivery2'];?>" placeholder="Addres line 1">
                    <input class="po_delivery3 nd_input2" type="text" name="po_delivery3" value="<?php echo $row['po_delivery3'];?>" placeholder="Addres line 2">
                    <input class="po_delivery4 nd_input2" type="text" name="po_delivery4" value="<?php echo $row['po_delivery4'];?>" placeholder="Country">
                    <input class="po_delivery5 nd_input2" type="text" size="10" placeholder="PIC name" name="po_pic_name" value="<?php echo $row['po_pic_name'];?>">
                    <input class="po_delivery6 nd_input2" type="text" size="10" placeholder="PIC phone" name="po_pic_phone" value="<?php echo $row['po_pic_phone'];?>">
                </div>
            </div>
            <div class="nd_block po_links related_orders_wrapper nd_links">
                <label class="nd_label">Links</label>
                <input type="hidden" class="related_orders_number" value="<?php echo $on->order;?>">
                <div class="nd_links_container related_orders_conteiner"></div>
                <div>
                    <input type="text" maxlength="12" width="100%" class="related_orders_number2" placeholder="Number XX-XXX-XXXXX">
                    <br />
                    <a class="knopka3" href="#" onclick="related_orders_add(this)">Add link</a>
                </div>
            </div>
            <div class="nd_block po_print_note">
                <label class="nd_label">Print note</label>
                <textarea name="po_print_note" id="po_print_note" class="nd_textarea po_note"><?php echo $row['po_print_note'];?></textarea>
            </div>
            <div class="nd_block po_print">
                <label class="nd_label">Download</label>
                <a href="/scripts/purchase_excel_form.php?id=<?php echo $po_id; ?>">
                    <img class="line_image" src="icons_/xls.png"> Download PO 
                </a>
            </div>
            <div class="po_body_conteiner nd_block">
                <div><h2>Details</h2></div>
                <div class="po_table_container">
                <table class="po_quotation_table">
                    <thead class="po_quotation_thead">
                        <th class="po_col_no">№</th>
                        <th class="po_col_control"></th>
                        <th class="">Description</th>
                        <th class="po_col_qty">Q-ty</th>
                        <th class="po_col_price">Price</th>
                        <th class="po_col_discount">Discount</th>
                        <th class="po_col_amount">Amount</th>
                        <th class="po_col_note">Note</th>
                        <th class="po_col_delete"></th>
                    </thead>
                </table>
                </div>
                <table class="po_table_bottom" width="100%">
                    <tr class="table_round_last_tr">
                        <td width="900px"><input type="button" class="nd_button" value="Add content" onclick="po_add_content(this)"></td>
                        <td class="sales_col_exrate" style="text-align: right;"><b>Total:</b></td>
                        <td class="sales_col_amount">
                            <input type="number" id="total_field" class="align_right" name="total" readonly style="width:65px;border:2px solid black;" value="<?php echo number_format($row['po_total'],2,'.',''); ?>">
                        </td>
                        <td class="sales_col_delete"></td>
                    </tr>
                </table>
            </div>
            <div class="nd_block po_note">
                <label class="nd_label">Note:</label>
                <textarea name="po_note" id="po_note" class="nd_textarea po_note"><?php echo $row['po_note'];?></textarea>
            </div>
        </form>
        <div class="nd_footer nd_block">
            <div class="align_center" width="100%" style="padding: 10px">
                <button class="nd_button_green" onclick="add_new_purchase(this)">Save</button>
                <button class="nd_button" onclick="window_close(this)">Close</button>
            </div>
        </div>
    </div>    
    <div style="display:none;">
        <table>
            <tbody id="po_quotation_new_line" class="quotation_line po_quotation_line">
            <tr>
                <td class="po_col_no"></td>
                <td class="po_col_control">
                    <input type="button" value="&#9650;" onclick="po_row_up(this)">
                    <input type="button" value="&#9660;" onclick="po_row_down(this)">
                </td>
                <td><?php echo selector_nmnc_linear('name="po_con_base_id[]" onchange="qte_total(this)"','name="po_con_text[]"');?></td>
                <td class="po_col_qty"><input type="number" step="1" name="po_con_qty[]" class="inp_qty" style="width:100%;" onchange="qte_total(this)" value="1"></td>
                <td class="po_col_price"><input type="number" step="0.01" name="po_con_price[]" class="inp_price align_right" style="width:100%;" onchange="qte_total(this)" value="0.00"></td>
                <td class="po_col_discount"><input type="number" step="1" name="po_con_discount[]" class="inp_discount align_right" style="width:100%;" onchange="qte_total(this)" value="0"></td>
                <td class="po_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="0.00"></td>
                <td class="po_col_note"><input type="text" name="po_con_note[]" style="width:100%;"></td>
                <td class="po_col_delete"><button type="button" onclick="po_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>