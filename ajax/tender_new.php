<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/invoice_fns.php';
require_once '../functions/selector.php';
require_once '../functions/tenders.php';
$comp_list = get_our_companies_list(1);
?>
<link href="/css/tenders.css" rel="stylesheet">
<div class="window_internal" style="width:1280px;height:90%;">
    <p><strong style="font-size:large;">New tender</strong>
    <br>
    ID: -
    </p>
    <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
    <div class="tab_bar">
        <a class="tab_button selected_tab" tab="general" onclick="openTab(this)" href='#'>Main</a>
        <a class="tab_button" tab="files" onclick="openTab(this)" href='#'>Uploaded files</a>
    </div>
    <form name="new_tender_form" method="post" onsubmit="return tender_new_form(this)">
        <div id="general" class="tab" style="display:block;">
        <div class="tender_grid_conteiner">
            <div id="grid_tender_info" class="block_div2 calc_fancy_div">
                <table>
                    <tr>
                        <td><b>Tender no</b></td><td><input type="text" required name="tender_no" maxlength="20"></td>
                        <td><b>Published date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" required name="tender_date"></td>
                        <td><b>Status</b></td><td><?php echo select_tender_status();?></td>
                    </tr>
                    <tr>
                        <td><b>Customer</b></td><td><div class="customer_conteiner""><?php echo selector('customers','name="invoice_customer"',$row['invoice_customer']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)"></div></td>
                        <td><b>Start price</b></td><td><input type="text" required name="tender_price" size="10"></td>
                        <td><b>Status</b></td><td><?php echo select_tender_status();?></td>
                    </tr>
                    <tr>
                        <td><b>Tender platform</b></td><td><?php echo select_tender_paltform();?></td>
                        <td><b>Published date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" required name="tender_date"></td>
                        <td><b>Application until</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" required name="tender_date"></td>
                    </tr>
                </table>
            </div>
            <div id="grid_tender_info2" class="block_div2 calc_fancy_div">
                <table>
                    <tr><td><b>Our company</b></td><td><?php echo select_our_company2($comp_list,0,'name="stock_po_comp"');?></td></tr>
                    <tr><td><b>Стоимость участия</b></td><td><input type="text" required name="tender_price" size="10"></td></tr>
                    <tr><td><b>Обеспечение заявки</b></td><td><input type="text" required name="tender_price" size="10"></td></tr>
                    <tr><td><b>Обеспечение договора</b></td><td><input type="text" required name="tender_price" size="10"></td></tr>
                    <tr><td><b>Обеспечение заявки</b></td><td><input type="text" required name="tender_price" size="10"></td></tr>
                </table>
            </div>
            <div id="grid_invoice_links" class="block_div2 align_center">
                <h3>Links</h3>
                <br>
                You must save invoice to be able to add links.
            </div>
            <div id="grid_tender_info2" class="block_div2 calc_fancy_div">
                <table>
                    <tr>
                        <td><b>Customer</b></td><td><div class="customer_conteiner""><?php echo selector('customers','name="invoice_customer"',$row['invoice_customer']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)"></div></td>
                        <td><b>Start price</b></td><td><input type="text" required name="tender_price" size="10"></td>
                        <td><b>Обеспечение заявки</b></td><td><input type="text" required name="tender_price" size="10"></td>
                        <td><b>Обеспечение договора</b></td><td><input type="text" required name="tender_price" size="10"></td>
                        <td><b>Обеспечение заявки</b></td><td><input type="text" required name="tender_price" size="10"></td>
                    </tr>
                </table>
            </div>
        </div>
        </div>
        <h2>Content</h2>   
    <div class="invoice_body_conteiner">
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
    </div>
    <table width="100%">
        <tr class="table_round_last_tr">
            <td width="900px"><input type="button" value="Add content" onclick="invoice_add_content(this)"></td>
            <td style="text-align: right;"><b>Total:</b></td>
            <td><input type="number" id="total_field" step="0.01" class="align_right" name="invoice_total" style="width:65px;border:2px solid black;" value="<?php echo number_format($row['qte_total'],2,'.',''); ?>"></td>
            <td class="sales_col_delete"></td>
        </tr>
    </table>
    <div class="block_div2">
        <strong>Note:</strong><br>
        <textarea name="invoice_note" class="invoice_note"></textarea>
    </div>
    <div class="align_center" width="100%" style="padding: 10px">
        <input type="submit" class="green_button" value="Save"> 
        <input type="button" value="Close" onclick="window_close(this);">
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