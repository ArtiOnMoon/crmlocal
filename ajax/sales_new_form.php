<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/sales_fns.php';
require_once '../functions/selector.php';
$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);
?>
<link rel="stylesheet" type="text/css" href="css/sales.css">
<div class="window_internal" style="width:90%; max-width: 1280px;height:90%;max-height:100%;">
    <datalist id="pay_terms_list">
        <option value="In advance"></option>
        <option value="NET 15 days"></option>
        <option value="Net 30 days"></option>
    </datalist>
<div class="nd_main_grid">
    <div class="nd_header">
        <div><h2>New sale</h2></div>
        <button class="nd_close_button" onclick="sales_close(this)">&#10006;</button>
    </div>
    <div class="nd_subheader"></div>
    <div class="nd_body">
        <form class="sales_grid_conteiner nd_tabdiv sales_view_form" name="new_sale_form" id="new_sale_form" method="post" action="/scripts/sales_new.php" onsubmit="return sales_new_form(this)">
            <input type='hidden' name="sale_id" value="<?php echo $sale_id;?>">
            <input type='hidden' class="sales_our_comp" name="sales_our_comp" value="<?php echo clean($_POST['sales_our_comp']);?>">
                <div class="nd_block sale_id">
                    <label class="nd_label">Sale ID</label>
                    <input class="nd_input"type="text" disabled name="sales_no" size="5" maxlength="40" value="Auto">
                </div>
                <div class="nd_block sale_status">
                    <label class="nd_label">Status</label>
                    <?php echo select_sale_status($row['sales_status'],'class="nd_select" name="sale_status"');?>
                </div>
                <div class="nd_block sale_vessel">
                    <label class="nd_label">Vessel</label>
                    <?php echo selector('vessels','name="sales_vessel_id"',1);?>
                    <img title="View vessel" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="vessel_view_add(this)">
                </div>
                <div class="nd_block sale_date">
                    <label class="nd_label">Date</label>
                    <input class="nd_input datepicker" type="text" required="true" placeholder="yyyy-mm-dd" size="10" name="sales_date" value="<?php echo date('Y-m-d');?>">
                </div>
                
                <div class="nd_block sale_customer">
                    <label class="nd_label">Customer</label>
                    <?php echo selector_customer('name="sales_customer"',$row['sales_customer']);?> 
                    <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </div>
                <div class="nd_block sale_request">
                    <label class="nd_label">Customer's Request</label>
                    <input class="nd_input" type="text" maxlength="50" size="15" name="sales_request" value="<?php echo $row['sales_request'];?>">
                </div>
                <div class="nd_block sale_po">
                    <label class="nd_label">Customer's PO</label>
                    <input class="nd_input" type="text" maxlength="50" size="15" name="sales_cust_po" value="<?php echo $row['sales_cust_po'];?>">
                </div>
                <div class="nd_block sale_ship_date">
                    <label class="nd_label">Shipment before</label>
                    <input class="nd_input" type="text"  placeholder="yyyy-mm-dd" size="10" class="datepicker" name="sale_shipment_dew" value="<?php echo $row['sale_shipment_dew'];?>">
                </div>
                
                <div class="nd_block sale_pay_terms">
                    <label class="nd_label">Payment terms</label>
                    <input class="nd_input" list="pay_terms_list" type="text" name="sales_pay_terms" value="<?php echo $row['sales_pay_terms'];?>">
                </div>
                <div class="nd_block sale_pay_date">
                    <label class="nd_label">Payment date</label>
                    <input class="nd_input datepicker" type="text" name="sales_pay_date" placeholder="yyyy-mm-dd" size="10" class="datepicker" value="<?php echo $row['sales_pay_date'];?>">
                </div>
                <div class="nd_block sale_pay_amount">
                    <label class="nd_label">Payment amount</label>
                    <input class="nd_input" type="number" step="0.01" name="sales_payment" value="<?php echo $row['sales_payment'];?>">
                </div>
                <div class="nd_block sale_invoice">
                    <label class="nd_label">Invoice\account</label>
                    <input class="nd_input" type="text" name="sales_invoice"  size="10" value="<?php echo $row['sales_invoice'];?>">
                </div>
                <div class="nd_block sale_qte">
                    <label class="nd_label">QTE valid untill</label>
                    <input class="nd_input" type="text" class="datepicker" name="sales_qte_date"  placeholder="yyyy-mm-dd" size="10" value="<?php echo $row['sales_qte_date'];?>">
                </div>
                <div class="nd_block sale_print">
                    <label class="nd_label">Print\download</label>
                    <a href="/scripts/sales_oa_excel.php?id=<?php echo $sale_id;?>"><img height="15px" src="./icons_/print.png">Confirmation of order</a> <br />
                    <a target="blank" href="sales_view_print.php?id=<?php echo $sale_id;?>"><img height="15px" src="./icons_/print.png">Quotation</a> <br /> 
                    <a target="blank" href="sales_view_print_ru.php?id=<?php echo $sale_id;?>"><img height="15px" src="./icons_/print.png">Quotation(RU)</a>
                </div>
                <div class="nd_block sale_qte_note">
                    <label class="nd_label">Quotation note</label>
                    <input class="nd_input" name="sales_qte_note" placeholder="This note will appear in quotation." value="<?php echo $row['sales_qte_note'];?>">
                </div>

                <div class="nd_block sale_links align_center">
                    <label class="nd_label">Links</label>
                    <div class="related_orders_wrapper">
                        You must save the order to be able to add links.
                    </div>
                </div>
        <div class="sale_details">
            <div><h2>Details</h2></div>
            <div class="nd_block" style="overflow:auto;">
            <table class="sales_quotation_conteiner">
                <thead class="sales_quotation_header_conteiner">
                    <th class="sales_header sales_col_n">№</th>
                    <th class="sales_header sales_col_control"></th>
                    <th class="sales_header">Description</th>
                    <th class="sales_header sales_col_qty">Q-ty quoted</th>
                    <th class="sales_header sales_col_qty">Q-ty confirmed</th>
                    <th class="sales_header sales_col_price">Price</th>
                    <th class="sales_header sales_col_vat">VAT</th>
                    <th class="sales_header sales_col_discount">Discount</th>
                    <th class="sales_header sales_col_currency">Curr.</th>
                    <th class="sales_header sales_col_exrate">Ex. rate</th>
                    <th class="sales_header sales_col_amount">Amount quoted</th>
                    <th class="sales_header sales_col_amount">Amount confirmed</th>
                    <th class="sales_header sales_col_delete"></th>
                </thead>                       
            </table>
            </div>
            <div>
                <table width="100%">
                    <tr class="table_round_last_tr">
                        <td width="900px"><input type="button" class="nd_button" onclick="sales_quotation_add_content(this)" value="Add line"></td>
                        <td class="sales_col_discount" style="text-align: right;"><b>Currency</b></td>
                        <td class="sales_col_currency"><?php echo select_currency2($cur_list,$row['sales_currency'], 'class="inp_currency bank_det_currency" onchange="get_our_bank_det(this)" name="sales_currency" required');?></td>
                        <td class="sales_col_exrate" style="text-align: right;"><b>Total:</b></td>
                        <td class="sales_col_amount"><input type="number" id="total_field" class="align_right" name="total" readonly style="width:65px;border:2px solid black;" value="<?php echo number_format($row['sales_total'],2,'.',''); ?>"></td>
                        <td class="sales_col_amount"><input type="number" id="total_cfm_field" class="align_right cfm_field" name="sales_total_cfm" readonly style="width:65px;border:2px solid black;" value="<?php echo number_format($row['sales_total_cfm'],2,'.',''); ?>"></td>
                        <td class="sales_col_delete"></td>
                    </tr>
                    <tr class="table_round_last_tr2">
                        <td colspan="3"></td>
                        <td style="text-align: right;"><b>Incl. VAT:</b></td>
                        <td><input type="number" id="total_vat_field" class="align_right" name="sales_total_vat" readonly style="width:65px;" value="<?php echo number_format($row['sales_total_vat'],2,'.',''); ?>"></td>
                        <td colspan="2"><input type="number" id="total_cfm_vat" class="align_right" name="sales_vat_cfm" readonly style="width:65px;" value="<?php echo number_format($row['sales_vat_cfm'],2,'.',''); ?>"></td>
                    </tr>
                </table>
            </div>
            <div>
                <div class="nd_block sale_note">
                    <label class="nd_label">Note</label>
                    <textarea class="nd_textarea" name="sales_descr" maxlength="1000"><?php echo $row['sales_descr'];?></textarea>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div class="nd_footer nd_block">
        <div align="center">
            <input class="nd_button_green" type="submit" form="new_sale_form" value="Save"> 
        </div>
    </div> 
    <!-- FOR INSERT -->
    <div style="display:none;">
        <table>
        <tbody id="sales_quotation_new_line" class="sales_quotation_line">
            <tr>
                <td class="sales_quotation_div sales_col_n sales_quotation_index"></td>
                <td class="sales_quotation_div sales_col_control">
                    <input type="button" value="&#9650;" onclick="sales_row_up(this)">
                    <input type="button" value="&#9660;" onclick="sales_row_down(this)">
                </td>
                <td class="sales_quotation_div">
                    <input type="search" class="inp_text sales_col_descr" name="scont_text[]" oninput="live_search(this,<?php echo clean($_POST['sales_our_comp']);?>)" onblur="sales_inp_blur(this)" value="<?php echo $sales_cont['scont_text'];?>" placeholder="Start typing to search in nomenclature database">
                    <img title="View nomenclature" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="stnmc_view_add(this)">
                    <input type="hidden" class="inp_base_id" name="scont_base_id[]" value="<?php echo $sales_cont['scont_base_id'];?>">
                    <div class="selector_search_div"></div>
                </td>
                <td class="sales_quotation_div sales_col_qty"><input type="number" step="1" name="scont_qty[]" class="inp_qty" style="width:100%;" onchange="sales_total(this)" value="1"></td>
                <td class="sales_quotation_div sales_col_cfm_qty"><input class="cfm_field inp_cfm_qty" type="number" step="1" name="scont_cfm_qty[]" class="inp_qty" style="width:100%;" onchange="sales_total(this)" value="0"></td>
                <td class="sales_quotation_div sales_col_price"><input type="number" step="0.01" name="scont_price[]" class="inp_price align_right" style="width:100%;" onchange="sales_total(this)" value="0.00"></td>
                <td class="sales_quotation_div sales_col_vat"><?php echo sales_vat_func($sales_cont['scont_vat']); ?></div>
                <td class="sales_quotation_div sales_col_discount"><input type="number" step="1" name="scont_discount[]" class="inp_discount align_right" style="width:100%;" onchange="sales_total(this)" value="0"></td>
                <td class="sales_quotation_div sales_col_currency"><?php echo select_currency2($cur_list,$sales_cont['scont_currency'], 'class="inp_currency" name="scont_currency[]" required ');?></div>
                <td class="sales_quotation_div sales_col_exrate"><input type="number" step="0.0001" name="scont_currency_rate[]" style="width:100%;" onchange="sales_total(this)" class="inp_currency_rate align_right" value="1.0000"></td>
                <td class="sales_quotation_div sales_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="0.00"></td>
                <td class="sales_quotation_div sales_col_amount"><input class="cfm_field inp_cfm_amount align_right"type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="0.00"></td>
                <td class="sales_quotation_div sales_col_delete"><button type="button" onclick="sales_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
            </tr>
            <tr>
                <td colspan="13" class="sales_quotation_sublink">
                    <a class="sublink" href="#" onclick="sales_second_row_switch(this)">Add serials and package</a>
                    <input type="text" class="sales_quotation_switch" name="scont_has_serial[]" value="<?php if ($sales_cont['scont_has_serial']==1) echo 1;ELSE echo 0;?>" style="display:none;">
                </td>
                <td colspan="13" class="sales_quotation_second_line disnone">
                    Delivery: <input type="text" size="10" name="scont_delevery[]" placeholder="in stock" value="<?php echo $sales_cont['scont_delevery'];?>">
                    Box No.: <input type="text" size="5" name="scont_box_no[]" value="<?php echo $sales_cont['scont_box_no'];?>">
                    Serails: <input type="text" size="40" name="scont_serials[]" value="<?php echo $sales_cont['scont_serials'];?>" style="width:900px;">
                    <a class="sublink" onclick="sales_second_row_hide(this)">Hide</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</div>