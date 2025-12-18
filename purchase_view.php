<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';
require_once 'functions/purchase_fns.php';
require_once 'functions/invoice_fns.php';
require_once 'classes/Order_name_engine.php';

$cur_list=get_currency_list();
$comp_list =get_our_companies_list(1);

$order=clean($_POST['order']);
//$comp_id=clean($_POST['comp_id']);
//$po_no=numberFormat(clean($_POST['po_no']),4);

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);
$on->resolve_order($order);
$on->resolve_id($db);

$query= 'SELECT purchase.*, our_companies.*, curr_name, users.full_name '
    . 'FROM purchase '
    . 'LEFT JOIN currency ON purchase.po_currency=curr_id '
    . 'LEFT JOIN our_companies ON po_our_comp=our_companies.id '
    . 'LEFT JOIN users ON purchase.modified = uid '
    . 'WHERE po_id="'.$on->id.'"';
$result=$db->query($query);
?>
<link href="/css/purchase.css" rel="stylesheet">
<div class="window_internal" style="width:90%; max-width: 1280px; height:90%; min-height: 720px;">
<div class="nd_main_grid">
    <div class="nd_header">
        <div><h1><?php echo $on->order;?> <a onclick="copy_to_clipboard(this, '<?php echo $on->order;?>')"><img src="./icons_/copy.svg"></a></h1></div>
        <button class="nd_close_button" onclick="window_close(this)">&#10006;</button>
    </div>
    <?php
    if ($result-> num_rows!==1){
        echo '<h2>PO #',$comp_id,'.',$po_no,'</h2>';
        echo 'PO not found'.$db->error,'</div></div></div>';
        exit();
    }
    $row=$result->fetch_assoc();
    $po_id=$row['po_id'];

    $on->comp_id = $row['po_our_comp'];
    $on->num = $row['po_no'];
    $on->id = $row['po_id'];

    ?>
    <div class="nd_subheader nd_tabs">
            <div class="nd_tab nd_tab_active" onclick="switchTab(this)" data-tab="po_main_form">Main</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="stock_related">Related stock items</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="invoices_related">Related invoices</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="uploaded_files">Uploaded files</div>
    </div>
    <form id="po_main_form" class="nd_body nd_tabdiv po_grid_conteiner">
        <input type="hidden" name="po_id" value="<?php echo $po_id;?>">
        <input type="hidden" name="po_no" value="<?php echo $row['po_no'];?>">
        <div class="nd_block po_status">
            <label class="nd_label">Status</label>
            <?php echo select_po_status($row['po_status'],'required class="nd_select" name="po_status"');?>
        </div>
        <div class="nd_block po_date">
            <label class="nd_label">Date</label>
            <input class="nd_input datepicker" type="text" placeholder="yyyy-mm-dd" class="datepicker" requierd name="po_date" value="<?php echo $row['po_date'];?>">
        </div>
        <div class="nd_block po_our_comp">
            <label class="nd_label">Our company</label>
            <?php echo select_our_company2($comp_list,$row['po_our_comp'],'class="nd_select" name="po_our_comp"'); ?>
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
            <label><input name="po_invoice_to_flag" type="radio" value="0" <?php if($row['po_invoice_to_flag']==='0') {echo 'checked';}?>> Us</label>
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
                <?php
                $query= 'SELECT * FROM purchase_content WHERE po_con_po_id="'.$po_id.'"';
                if (!$result=$db->query($query)){
                    echo $db->error;
                }
                if ($result->num_rows > 0){
                    $i=1;
                    while($line=$result->fetch_assoc()){
                        ?>
                        <tbody class="quotation_line po_quotation_line">
                        <tr>
                            <td class="po_col_no"><?php echo $i++;?></td>
                            <td class="po_col_control">
                                <input type="button" value="&#9650;" onclick="po_row_up(this)">
                                <input type="button" value="&#9660;" onclick="po_row_down(this)">
                            </td>
                            <td><?php echo selector_nmnc_linear('name="po_con_base_id[]" onchange="qte_total(this)"','name="po_con_text[]"',$line['po_con_text'],$line['po_con_base_id']);?></td>
                            <td class="po_col_qty"><input type="number" step="1" name="po_con_qty[]" class="inp_qty" style="width:100%;" onchange="qte_total(this)" value="<?php echo $line['po_con_qty'];?>"></td>
                            <td class="po_col_price"><input type="number" step="0.01" name="po_con_price[]" class="inp_price align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo number_format($line['po_con_price'], 2,'.','');?>"></td>
                            <td class="po_col_discount"><input type="number" step="1" name="po_con_discount[]" class="inp_discount align_right" style="width:100%;" onchange="qte_total(this)" value="<?php echo $line['po_con_discount'];?>"></td>
                            <td class="po_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="<?php echo number_format($line['po_con_price']*$line['po_con_qty']*(1-$line['po_con_discount']/100),2,'.','');?>"></td>
                            <td class="po_col_note"><input type="text" name="po_con_note[]" style="width:100%;" value="<?php echo $line['po_con_note'];?>"></td>
                            <td class="po_col_delete"><button type="button" onclick="po_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
                        </tr>
                        </tbody>
                        <?php
                    }
                }
                ?>
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
    <!<!-- RELATED STOCK ITEMS -->
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
            . 'WHERE stock_new.stock_deleted=0 AND stock_po="'.$on->order.'"';
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
        </tbody></table>
    </div>
    <!-- FILES -->
    <div id="uploaded_files" class="nd_block nd_body nd_tabdiv nd_tabdiv_inactive"></div>
    <!--Related Invoices-->
    <div id="invoices_related" class="nd_block nd_body nd_tabdiv nd_tabdiv_inactive">
            <div class="block_div2"><a class="knopka" href="#" onclick="invoice_new(<?php echo '1,\'',$on->order; ?>')">Create new invoice</a></div>
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
        
    <div class="nd_footer nd_block">
        <span style="font-style: italic">Last modified by: <?php echo $row['full_name']; ?> at <?php echo $row['modified_date']; ?></span>
        <div class="align_center" width="100%" style="padding: 10px">
            <button class="nd_button_green" onclick="purchase_change(this)">Save</button>
            <button class="nd_button" onclick="window_close(this)">Close</button>
            <button class="nd_button" onclick="purchase_convert_to_invoice('<?php echo $on->order;?>')">Convert to invoice</button>
            <button class="nd_button" onclick="purchase_convert_to_stock('<?php echo $po_id;?>')">Add to stock</button>
        </div>
    </div>
</div>
    <!-- FOR INSERT -->
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