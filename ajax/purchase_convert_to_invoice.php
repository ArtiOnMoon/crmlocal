<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/invoice_fns.php';
require_once '../functions/selector.php';
require_once '../classes/Order_name_engine.php';

$db =  db_connect();

$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);
$po_no=clean($_POST['po_no']);

$on = new Order_name_engine();
$on ->init($db);
try {
    $on ->resolve_order($po_no);
} catch (Exception $ex) {
    exit($ex->getMessage());
}

$query= 'SELECT purchase.*, our_companies.*, curr_name,users.full_name '
        . 'FROM purchase '
        . 'LEFT JOIN currency ON purchase.po_currency=curr_id '
        . 'LEFT JOIN our_companies ON po_our_comp=our_companies.id '
        . 'LEFT JOIN users ON purchase.modified = uid '
        . 'WHERE po_no="'.$on->num.'" AND po_our_comp="'.$on->comp_id.'"';
$result=$db->query($query);

if ($result-> num_rows!==1){
    echo 'Nothing found'.$db->error;
    exit();
}
$purchase=$result->fetch_assoc();
$po_id=$purchase['po_id'];
?>
<link href="/css/invoices.css" rel="stylesheet">
<div class="window_internal" style="width:1280px;height:80%;">
    <datalist id='pay_terms_list'>
        <option value='In advance'>
        <option value='Net 15 days'>
        <option value='Net 30 days'>
        <option value='Net 60 days'>
        <option value='Net 90 days'>
    </datalist>
    <h2>New invoice based on PO <?php echo $on->order;?></h2>
    <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
    <form name="new_invoice_form" method="post" onsubmit="return invoice_new_form(this)">
        <div class="invoice_grid_conteiner">
            <div id="grid_invoice_info" class="block_div2 calc_fancy_div">
                <table>
                    <tr>
                        <td><b>Invoice No</b></td><td><input type="text" required name="invoice_num"></td>
                        <td><b>Date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="invoice_date"></td>
                        <td><b>Type</b></td><td><?php echo select_invoice_type(0);?></td>
                    </tr>
                    <tr>
                        <td><b>Our company</b></td><td><?php echo select_our_company2($comp_list,$purchase['po_our_comp'],'onchange="get_our_bank_det(this)" class="bank_det_company" name="invoice_our_comp"'); ?></td>
                        <td><b>Currency</b></td><td><?php echo select_currency2($cur_list,$purchase['po_currency'],'onchange="get_our_bank_det(this)" class="bank_det_currency" name="invoice_currency" required');?></td>
                        <td><b>Bank:</b></td><td> <?php echo select_our_bank_det_ajax($purchase['po_our_comp'],0,$purchase['po_currency'],'onchange="get_our_bank_det(this)" class="our_bank_det short_select" name="invoice_our_bank_det"');?></td>
                    </tr>
                    <tr>
                        <td><b>Status</b></td><td><?php echo select_invoice_status($row['invoice_status']);?></td>
                        <td><b>Our order.</b></td><td colspan="3"><input type="text" name="invoice_order_num" value="<?php echo $on->order;?>"></td>
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
                        <td><b>Customer</b></td><td><div class="customer_conteiner""><?php echo selector('customers','name="invoice_customer"',$purchase['po_supplier']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)"></div></td>
                    </tr>
                    <tr>
                        <td><b>Customer's ref.</b></td><td><input type="text" maxlength="50" size="15" name="invoice_cust_ref"></td>
                    </tr>
                    <tr>
                        <td><b>Payment terms</b></td><td><input type="text" maxlength="50" size="15" name="invoice_pay_terms" list="pay_terms_list"></td> 
                    </tr>
                </table>
            </div>
            <div id="grid_invoice_links" class="block_div2 align_center">
                <h3>Links</h3>
                <br>
                You must save invoice to be able to add links.
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
        <?php
        $query= 'SELECT * FROM purchase_content WHERE po_con_po_id="'.$po_id.'"';
        if (!$result=$db->query($query)){
            echo $db->error;
        }
        if ($result->num_rows > 0){
            $i=1;
            while($line=$result->fetch_assoc()){
                ?>
                <tbody class="quotation_line">
                <tr>
                    <td class="po_col_no"><?php echo $i++;?></td>
                    <td class="po_col_control">
                        <input type="button" value="&#9650;" onclick="invoice_row_up(this)">
                        <input type="button" value="&#9660;" onclick="invoice_row_down(this)">
                    </td>
                    <td><?php echo selector_nmnc_linear('name="inv_con_base_id[]" onchange="qte_total(this)"','name="inv_con_text[]"',$line['po_con_text'],$line['po_con_base_id']);?></td>
                    <td class="po_col_qty"><input type="number" step="1" name="inv_con_qty[]" class="inp_qty" style="width:100%;" onchange="qte_total(this)" value="<?php echo $line['po_con_qty'];?>"></td>
                    <td class="po_col_price"><input type="number" step="0.01" name="inv_con_price[]" class="inp_price align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo number_format($line['po_con_price'], 2,'.','');?>"></td>
                    <td class="po_col_discount"><input type="number" step="1" name="inv_con_discount[]" class="inp_discount align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo $line['po_con_discount'];?>"></td>
                    <td class="po_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="<?php echo number_format($line['po_con_price']*$line['po_con_qty']*(1-$line['po_con_discount']/100),2,'.','');?>"></td>
                    <td class="po_col_note"><input type="text" name="inv_con_note[]" style="width:100%;"></td>
                    <td class="po_col_delete"><button type="button" onclick="invoice_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
                    
                </tr>
                </tbody>
                <?php
            }
        }
        ?>
    </table>
    </div>
    <table width="100%">
        <tr class="table_round_last_tr">
            <td width="900px"><input type="button" value="Add content" onclick="invoice_add_content(this)"></td>
            <td style="text-align: right;"><b>Total:</b></td>
            <td><input type="number" id="total_field" step="0.01" class="align_right" name="invoice_total" style="width:65px;border:2px solid black;" value="<?php echo number_format($purchase['po_total'],2,'.',''); ?>"></td>
            <td><b>Currency</b></td><td><?php echo select_currency2($cur_list,0,'name="invoice_currency" required');?></td>
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
        <input type="hidden" name="create_link" value="1">
        <input type="hidden" name="link_type" value="3">
        <input type="hidden" name="link_comp" value="<?php echo $comp_id;?>">
        <input type="hidden" name="link_num" value="<?php echo $po_no;?>">
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