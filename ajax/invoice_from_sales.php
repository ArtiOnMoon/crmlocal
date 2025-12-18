<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/invoice_fns.php';
require_once '../functions/selector.php';
require_once '../classes/Order_name_engine.php';

$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);
$order_list = [1 => 'Service', 2 => 'Sales', 3 => 'Purchase'];

$sale_id = $_POST['sale_id'];
$query = 'SELECT * FROM sales WHERE sales_id = "'.$sale_id.'"';
$db =  db_connect();
$result=$db->query($query);
if ($result-> num_rows!==1){
    echo 'Nothing found';
    exit();
}
$row=$result->fetch_assoc();

$on = new Order_name_engine();
$on -> init($db);
$on -> type = 'SL';
$on -> comp_id = $row['sales_our_comp'];
$on -> num = $row['sales_no'];

$invoice_order_type=2;
$invoice_order_comp=$row['sales_our_comp'];
$invoice_type = '2';

?>
<link href="/css/invoices.css" rel="stylesheet">
<div class="window_internal" style="width:1280px;height:90%;">
    <datalist id='pay_terms_list'>
        <option value='In advance'>
        <option value='Net 15 days'>
        <option value='Net 30 days'>
        <option value='Net 60 days'>
        <option value='Net 90 days'>
    </datalist>
    <h2>New invoice\Credit note</h2>
    <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
    <div class="tab_bar">
        <a class="tab_button selected_tab" tab="general" onclick="openTab(this)" href='#'>Main</a>
        <a class="tab_button" tab="shipment" onclick="openTab(this)" href='#'>Shipment info</a>
    </div>
    <form name="new_invoice_form" method="post" onsubmit="return invoice_new_form(this)">
        <div id="general" class="tab" style="display:block;">
        <div class="invoice_grid_conteiner">
            <div id="grid_invoice_info" class="block_div2 calc_fancy_div">
                <table>
                    <tr>
                        <td><b><?php echo select_invoice_cn();?></b></td><td><input type="text" required name="invoice_num"></td>
                        <td><b>Date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="invoice_date" value="<?php echo date('Y-m-d');?>"></td>
                        <td><b>Status</b></td><td><?php echo select_invoice_status($row['invoice_status']);?></td>
                    </tr>
                    <tr class="bank_details_container">
                        <td><b>Our company</b></td><td><?php echo select_our_company2($comp_list,$row['sales_our_comp'],'onchange="get_our_bank_det(this)" class="bank_det_company" name="invoice_our_comp"'); ?></td>
                        <td><b>Currency</b></td><td><?php echo select_currency2($cur_list,$row['sales_currency'],'onchange="get_our_bank_det(this)" class="bank_det_currency" name="invoice_currency" required');?></td>
                        <td><b>Bank:</b></td><td> <?php echo select_our_bank_det_ajax(0,0,0,'class="our_bank_det short_select" name="invoice_our_bank_det"');?></td>
                    </tr>
                    <tr>
                        <td><b>Type</b></td><td><?php echo select_invoice_type($invoice_type);?></td>
                        <td><b>Our order.</b></td><td colspan="3"><input type="text" name="invoice_order_num" value="<?php echo $on->get_order();?>"></td>
                    </tr>
                </table>
            </div>
            <div id="grid_invoice_details" class="block_div2 calc_fancy_div align_center">
                <h3>Payments</h3>
                <br>
                You must save invoice to be able to add payments.                
            </div>
            <div id="grid_invoice_cust_info" class="block_div2 calc_fancy_div">
                <table>
                    <tr>
                        <td><b>Customer</b></td><td><div class="customer_conteiner""><?php echo selector('customers','name="invoice_customer"',$row['sales_customer']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)"></div></td>
                        <td><b>Customer's ref.</b></td><td><input type="text" maxlength="50" size="15" name="invoice_cust_ref" value="<?php echo $row['sales_cust_po'];?>"></td>
                    </tr>
                    <tr>
                        <td><b>Payment terms</b></td><td><input type="text" maxlength="50" size="15" name="invoice_pay_terms" value="<?php echo $row['sales_pay_terms'];?>" list='pay_terms_list'></td>
                    </tr>
                    <tr>
                        <td class="fancy_td"><b>VAT remarks</b></td><td colspan="3"><?php echo select_vat_rem(0,'name="invoice_vat_remarks"');?></td>
                    </tr>
                </table>
            </div>
            <div id="grid_invoice_links" class="block_div2 align_center">
                <h3>Links</h3>
                <br>
                You must save invoice to be able to add links.
            </div>
        </div>
        </div>
        <div id="shipment" class="tab">
            <table class="table_round2" width="100%">
            <col width="25%"><col width="25%"><col width="25%"><col width="25%">
            <tr><td colspan="4"><label>Shipment required <input type="checkbox" name="invoice_ship_req" <?php if($row['invoice_ship_req']==='1') echo'checked';?>></label></td></tr>
            <tr>
            <td class="fancy_td" colspan="2"><b>Shipped to:</b> &nbsp;&nbsp;
                <label><input onchange="sales_shipped_to_func(this)" type="radio" value="2" <?php if($row['sales_shipped_to_flag']==='2') echo'checked';?> name="invoice_shipped_to_flag"> Enter manualy</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <label><input onchange="sales_shipped_to_func(this)" type="radio" value="0" <?php if($row['sales_shipped_to_flag']==='0') echo'checked';?> name="invoice_shipped_to_flag"> Same as "Invoice to"</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <input onchange="sales_shipped_to_func(this)" type="radio" value="1" <?php if($row['sales_shipped_to_flag']==='1') echo'checked';?> name="invoice_shipped_to_flag">
                <span class="shipped_to_conteiner1 customer_conteiner <?php if($row['sales_shipped_to_flag']!=='1') echo'disabled';?>">
                    <?php echo selector('customers','name="invoice_shipped_to"',$row['sales_shipped_to']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </span>
                <div class="shipped_to_conteiner2 <?php if($row['sales_shipped_to_flag']!=='2') echo 'disabled';?>">
                    <input type="text" style="width:100%" name="invoice_shipped_name" placeholder="Company name" value="<?php echo $row['sales_shipped_name'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_addr1" placeholder="Street, building, office" value="<?php echo $row['sales_shipped_addr1'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_addr2" placeholder="ZIP, City" value="<?php echo $row['sales_shipped_addr2'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_country" placeholder="Country" value="<?php echo $row['sales_shipped_country'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_vat" placeholder="VAT number" value="<?php echo $row['sales_shipped_vat'];?>">
                </div>
            </td>
            <td class="fancy_td" colspan="2"><b>Shipped from</b> &nbsp;&nbsp;
                <label><input onchange="sales_shipped_from_func(this)" type="radio" value="2" <?php if($row['sales_shipped_from_flag']==='2') echo'checked';?> name="invoice_shipped_from_flag"> Enter manualy</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <label><input onchange="sales_shipped_from_func(this)" type="radio" value="0" <?php if($row['sales_shipped_from_flag']==='0') echo'checked';?> name="invoice_shipped_from_flag"> Same as "Invoice from"</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <input onchange="sales_shipped_from_func(this)" type="radio" value="1" <?php if($row['sales_shipped_from_flag']==='1') echo'checked';?> name="invoice_shipped_from_flag">
                <span class="shipped_from_conteiner1 customer_conteiner <?php if($row['sales_shipped_from_flag']!=='1') echo'disabled';?>">
                    <?php echo selector('customers','name="invoice_shipped_from"',$row['sales_shipped_from']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </span>                
                <div class="shipped_from_conteiner2 <?php if($row['sales_shipped_from_flag']!=='2') echo 'disabled';?>">
                    <input type="text" style="width:100%" name="invoice_shipped_from_name" placeholder="Company name" value="<?php echo $row['sales_shipped_from_name'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_from_addr1" placeholder="Street, building, office" value="<?php echo $row['sales_shipped_from_addr1'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_from_addr2" placeholder="ZIP, City" value="<?php echo $row['sales_shipped_from_addr2'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_from_country" placeholder="Country" value="<?php echo $row['sales_shipped_from_country'];?>">
                    <input type="text" style="width:100%" name="invoice_shipped_from_vat" placeholder="VAT number" value="<?php echo $row['sales_shipped_from_vat'];?>">
                </div>
            </td>
            </tr>
            <tr>
                <td><b>Shipped on</b></td>
                <td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="invoice_ship_date" value="<?php echo $row['sales_ship_date'];?>"></td>
                <td><b>Shipped by</b></td>
                <td><input type="text" maxlength="150" placeholder="DHL, UPS, TNT etc." name="invoice_shipped_on" value="<?php echo $row['sales_shipped_on'];?>"></td>
            </tr>
            <tr>
                <td><b>Delivery terms</b></td>
                <td><input type="text" name="invocie_delevery_terms" maxlength="100" value="<?php echo $row['sales_delevery_terms'];?>"></td>
                <td><b>AWB</b></td>
                <td><input type="text" name="invoice_awb" maxlength="150" value="<?php echo $row['sales_awb'];?>"></td>
            </tr>
        </table>     
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
        <!-- Invoice content-->
        <?php
        $i=1;
        $total=0;
        $query= 'SELECT * FROM sales_content WHERE scont_sale_id = "'.$sale_id.'"';
        if(!$result=$db->query($query))exit($db->error);
        $db->close();
        if($result->num_rows > 0){
            while($sales_cont = $result->fetch_assoc()){
            ?>
                <tbody id="invoice_quotation_new_line" class="quotation_line">
                    <tr>
                        <td class="po_col_no"><?php echo $i++;?></td>
                        <td class="po_col_control">
                            <input type="button" value="&#9650;" onclick="invoice_row_up(this)">
                            <input type="button" value="&#9660;" onclick="invoice_row_down(this)">
                        </td>
                        <td><?php echo selector_nmnc_linear('name="inv_con_base_id[]" onchange="qte_total(this)"','name="inv_con_text[]"',$sales_cont['scont_text'],$sales_cont['scont_base_id']);?></td>
                        <td class="po_col_qty"><input type="number" step="1" name="inv_con_qty[]" class="inp_qty" style="width:100%;" onchange="qte_total(this)" value="<?php echo $sales_cont['scont_cfm_qty'];?>"></td>
                        <td class="po_col_price"><input type="number" step="0.01" name="inv_con_price[]" class="inp_price align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo $sales_cont['scont_price'];?>"></td>
                        <td class="po_col_discount"><input type="number" step="1" name="inv_con_discount[]" class="inp_discount align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo $sales_cont['scont_discount'];?>"></td>
                        <td class="po_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="<?php echo $sales_cont['scont_cfm_qty']*$sales_cont['scont_price']*(1-$sales_cont['scont_discount']/100);?>"></td>
                        <td class="po_col_note"><input type="text" name="inv_con_note[]" style="width:100%;"></td>
                        <td class="po_col_delete"><button type="button" onclick="invoice_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
                    </tr>
                </tbody>
            <?php
            $total += $sales_cont['scont_cfm_qty']*$sales_cont['scont_price']*(1-$sales_cont['scont_discount']/100);
            }
        }
        ?>
    </table>
    </div>
    <table width="100%">
        <tr class="table_round_last_tr">
            <td width="900px"><input type="button" value="Add content" onclick="invoice_add_content(this)"></td>
            <td style="text-align: right;"><b>Total:</b></td>
            <td><input type="number" id="total_field" step="0.01" class="align_right" name="invoice_total" style="width:65px;border:2px solid black;" value="<?php echo number_format($total,2,'.',''); ?>"></td>
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