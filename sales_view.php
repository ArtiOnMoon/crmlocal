<?php
require_once 'functions/fns.php';
require_once 'functions/sales_fns.php';
require_once 'PATHS.php';
require_once 'functions/selector.php';
require_once 'functions/stock_fns.php';
require_once 'functions/invoice_fns.php';
require_once 'classes/Order_name_engine.php';

$db =  db_connect();

$sales_no=clean($_POST['order']);
$on = new Order_name_engine();
$on->init($db);
try{
    $on->resolve_order($sales_no);
    echo $on->comp_id;
    $on->resolve_id($db);
    $on->get_order();
} catch (Error $ex){
    exit ($ex->getMessage());
}
$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);

$query= 'SELECT sales.*, our_companies.*,our1.id as our_id, curr_name, countries.name as our_country, c1.name AS invoice_country, full_name, '
        . 'customers.client_of, customers.cust_full_name, customers.vat, customers.InvoicingAddress as address,customers.InvoicingAddress2 as address2 '
        . 'FROM sales '
        . 'LEFT JOIN currency ON sales.sales_currency=curr_id '
        . 'LEFT JOIN our_companies ON sales_invoice_from=our_companies.id '
        . 'LEFT JOIN our_companies our1 ON sales_our_comp=our1.id '
        . 'LEFT JOIN customers ON sales_customer=cust_id '
        . 'LEFT JOIN countries ON countries.id = our_companies.our_country '
        . 'LEFT JOIN countries c1 ON c1.id = customers.country '
        . 'LEFT JOIN users ON sales.modified = uid '
        . 'WHERE sales_id = "'.$on->id.'"';
$result=$db->query($query);
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
        <div><h1><?php echo $on->order;?> <a onclick="copy_to_clipboard(this, '<?php echo $on->order;?>')"><img src="./icons_/copy.svg"></a></h1></div>
        <button class="nd_close_button" onclick="sales_close(this)">&#10006;</button>
    </div>
<?php

if ($result-> num_rows!==1){
    echo 'Nothing found</div></div></div>';
    exit();
}
$row=$result->fetch_assoc();

if(check_access('acl_sales', 1)) exit('Access denied.');
if(check_access('acl_sales', 1, $row['sales_our_comp'])) exit('Access denied.');

$sale_id=$row['sales_id'];

//Shipped TO
if($row['sales_shipped_to_flag']==='0'){
    $shipped_to_name=$invoice_to_name1;
    $shipped_to_addr1=$invoice_to_addr1;
    $shipped_to_addr2=$invoice_to_addr2;
    $shipped_to_country=$invoice_to_country;
    $shipped_to_vat=$invoice_to_vat;
} elseif($row['sales_shipped_to_flag']==='1'){
    // запрос Customer SHIPPED_TO
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$row['sales_shipped_to'];
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Customer not found. Code 1.');
    } else $shipped_to=$result->fetch_assoc();
    $shipped_to_name=$shipped_to['cust_full_name'];
    $shipped_to_addr1=$shipped_to['InvoicingAddress'];
    $shipped_to_addr2=$shipped_to['InvoicingAddress2'];
    $shipped_to_country=$shipped_to['name'];
    $shipped_to_vat=$shipped_to['vat']; 
} ELSE { //sales_shipped_to_flag===2
    $shipped_to_name=$row['sales_shipped_name'];
    $shipped_to_addr1=$row['sales_shipped_addr1'];
    $shipped_to_addr2=$row['sales_shipped_addr2'];
    $shipped_to_country=$row['sales_shipped_country'];
    $shipped_to_vat=$row['sales_shipped_vat'];
}
//Shipped FROM
if($row['sales_shipped_from_flag']==='0'){
    $shipped_from_name=$row['our_full_name'];
    $shipped_from_addr1=$row['our_inv_addr'];
    $shipped_from_addr2=$row['our_inv_addr2'];
    $shipped_from_country=$row['our_country'];
    $shipped_from_vat=$row['our_vat'];
}elseif ($row['sales_shipped_from_flag']==='1'){
    // запрос Customer SHIPPED_FROM
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$row['sales_shipped_from'];
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Customer not found. Code 1.');
    } else $shipped_from=$result->fetch_assoc();
    $shipped_from_name=$shipped_from['cust_full_name'];
    $shipped_from_addr1=$shipped_from['InvoicingAddress'];
    $shipped_from_addr2=$shipped_from['InvoicingAddress2'];
    $shipped_from_country=$shipped_from['name'];
    $shipped_from_vat=$shipped_from['vat']; 
} else{
    $shipped_from_name=$row['sales_shipped_from_name'];
    $shipped_from_addr1=$row['sales_shipped_from_addr1'];
    $shipped_from_addr2=$row['sales_shipped_from_addr2'];
    $shipped_from_country=$row['sales_shipped_from_country'];
    $shipped_from_vat=$row['sales_shipped_from_vat'];
}
$sale_no=$row['sales_no'];

?>
    <div class="nd_subheader nd_tabs">
        <div class="nd_tab nd_tab_active" onclick="switchTab(this)" data-tab="general">Quotation</div>
        <div class="nd_tab" onclick="switchTab(this)" data-tab="invoices_related">Related Invoices</div>
        <div class="nd_tab" onclick="switchTab(this)" data-tab="package">Shipment \ Package</div>
        <!--<a class="tab_button" tab="invoice" onclick="openTab(this)" href='#'>Invoice</a>-->
        <div class="nd_tab" onclick="switchTab(this)" data-tab="stock_related">Related stock items</div>
        <div class="nd_tab" onclick="switchTab(this)" data-tab="uploaded_files">Uploaded files</div>
    </div>
    <!--General TAB-->
    <form id="general" method="POST" class="sales_grid_conteiner nd_tabdiv sales_view_form" onsubmit="return change_sales(this)" >
        <input type='hidden' name="sale_id" value="<?php echo $sale_id;?>">
        <input type='hidden' class="sales_our_comp" name="sales_our_comp" value="<?php $row['sales_our_comp'];?>">
            <div class="nd_block sale_id">
                <label class="nd_label">Sale ID</label>
                <input class="nd_input"type="text" required name="sales_no" size="5" maxlength="40" value="<?php echo $row['sales_no'];?>">
            </div>
            <div class="nd_block sale_status">
                <label class="nd_label">Status</label>
                <?php echo select_sale_status($row['sales_status'],'class="nd_select" name="sale_status"');?>
            </div>
            <div class="nd_block sale_vessel vessel_conteiner">
                <label class="nd_label">Vessel</label>
                <?php echo selector('vessels','name="sales_vessel_id"',$row['sales_vessel_id']);?>
                <img title="View vessel" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="vessel_view_add(this)">
            </div>
            <div class="nd_block sale_date">
                <label class="nd_label">Date</label>
                <input class="nd_input datepicker" type="text" required="true" placeholder="yyyy-mm-dd" size="10" name="sales_date" value="<?php echo $row['sales_date'];?>">
            </div>

            <div class="nd_block sale_customer customer_conteiner">
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
                <input class="nd_input datepicker" type="text"  placeholder="yyyy-mm-dd" size="10" name="sale_shipment_dew" value="<?php echo $row['sale_shipment_dew'];?>">
            </div>

            <div class="nd_block sale_pay_terms">
                <label class="nd_label">Payment terms</label>
                <input class="nd_input" list="pay_terms_list" type="text" name="sales_pay_terms" value="<?php echo $row['sales_pay_terms'];?>">
            </div>
            <div class="nd_block sale_pay_date">
                <label class="nd_label">Payment date</label>
                <input class="nd_input datepicker" type="text" name="sales_pay_date" placeholder="yyyy-mm-dd" size="10" value="<?php echo $row['sales_pay_date'];?>">
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
                <input class="nd_input datepicker" type="text" name="sales_qte_date"  placeholder="yyyy-mm-dd" size="10" value="<?php echo $row['sales_qte_date'];?>">
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

            <div class="nd_block sale_links related_orders_wrapper nd_links">
                <label class="nd_label">Links</label>
                <input type="hidden" class="related_orders_number" value="<?php echo $on->order;?>">
                <div class="nd_links_container related_orders_conteiner"></div>
                <div>
                    <input type="text" maxlength="12" width="100%" class="related_orders_number2" placeholder="Number XX-XXX-XXXXX">
                    <br />
                    <a class="knopka3" href="#" onclick="related_orders_add(this)">Add link</a>
                </div>
            </div>
        <div class="sale_details nd_block">
            <div><h2>Details</h2></div>
            <div style="overflow:auto;">
            <table class="sales_quotation_conteiner nd_table">
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
                        <?php
            //SALE CONTENT
            $i=1;
            $query= 'SELECT * FROM sales_content WHERE scont_sale_id = "'.$sale_id.'"';
            if(!$result=$db->query($query))exit($db->error);
            if($result->num_rows>0){
                while($sales_cont = $result->fetch_assoc()){
                    ?>
                        <tbody class="sales_quotation_line">
                        <tr>
                            <td class="sales_quotation_div sales_col_n sales_quotation_index"><?php echo $i++;?></td>
                            <td class="sales_quotation_div sales_col_control">
                                <input type="button" value="&#9650;" onclick="sales_row_up(this)">
                                <input type="button" value="&#9660;" onclick="sales_row_down(this)">
                            </td>
                            <td class="sales_quotation_div">
                                <input type="search" class="inp_text sales_col_descr" name="scont_text[]" oninput="live_search(this,<?php echo $row['sales_our_comp'];?>)" onblur="sales_inp_blur(this)" value="<?php echo $sales_cont['scont_text'];?>" placeholder="Start typing to search in nomenclature database">
                                <img title="View nomenclature" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="stnmc_view_add(this)">
                                <input type="hidden" class="inp_base_id" name="scont_base_id[]" value="<?php echo $sales_cont['scont_base_id'];?>">
                                <div class="selector_search_div"></div>
                            </td>
                            <td class="sales_quotation_div sales_col_qty"><input type="number" step="1" name="scont_qty[]" class="inp_qty" style="width:100%;" onchange="sales_total(this)" value="<?php echo $sales_cont['scont_qty'];?>"></td>
                            <td class="sales_quotation_div sales_col_cfm_qty"><input type="number" class="cfm_field inp_cfm_qty" step="1" name="scont_cfm_qty[]" class="inp_qty" style="width:100%;" onchange="sales_total(this)" value="<?php echo $sales_cont['scont_cfm_qty'];?>"></td>
                            <td class="sales_quotation_div sales_col_price"><input type="number" step="0.01" name="scont_price[]" class="inp_price align_right" style="width:100%;" onchange="sales_total(this)" value="<?php echo number_format($sales_cont['scont_price'], 2,'.','');?>"></td>
                            <td class="sales_quotation_div sales_col_vat"><?php echo sales_vat_func($sales_cont['scont_vat']); ?></div>
                            <td class="sales_quotation_div sales_col_discount"><input type="number" step="1" name="scont_discount[]" class="inp_discount align_right" style="width:100%;" onchange="sales_total(this)" value="<?php echo $sales_cont['scont_discount'];?>"></td>
                            <td class="sales_quotation_div sales_col_currency"><?php echo select_currency2($cur_list,$sales_cont['scont_currency'], 'class="inp_currency" name="scont_currency[]" required ');?></div>
                            <td class="sales_quotation_div sales_col_exrate"><input type="number" step="0.0001" name="scont_currency_rate[]" style="width:100%;" onchange="sales_total(this)" class="inp_currency_rate align_right" value="<?php echo number_format($sales_cont['scont_currency_rate'],4,'.','');?>"></td>
                            <td class="sales_quotation_div sales_col_amount"><input type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="<?php echo number_format($sales_cont['scont_price']*$sales_cont['scont_currency_rate']*$sales_cont['scont_qty']*(1-$sales_cont['scont_discount']/100),2,'.','');?>"></td>
                            <td class="sales_quotation_div sales_col_amount"><input class="cfm_field inp_cfm_amount align_right" type="number" step="0.01" style="width:100%;" class="inp_amount align_right" value="<?php echo number_format($sales_cont['scont_price']*$sales_cont['scont_currency_rate']*$sales_cont['scont_cfm_qty']*(1-$sales_cont['scont_discount']/100),2,'.','');?>"></td>
                            <td class="sales_quotation_div sales_col_delete"><button type="button" onclick="sales_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>
                        </tr>
                        <tr>
                            <td colspan="13" class="sales_quotation_sublink<?php if ($sales_cont['scont_has_serial']==1) echo' disnone';?>">
                                <a class="sublink" href="#" onclick="sales_second_row_switch(this)">Add serials and package</a>
                                <input type="text" class="sales_quotation_switch" name="scont_has_serial[]" value="<?php if ($sales_cont['scont_has_serial']==1) echo 1;ELSE echo 0;?>" style="display:none;">
                            </td>
                            <td colspan="13" class="sales_quotation_second_line<?php if ($sales_cont['scont_has_serial']=='0') echo' disnone';?>">
                                Delivery: <input type="text" size="10" name="scont_delivery[]" placeholder="in stock" value="<?php echo $sales_cont['scont_delivery'];?>">
                                Box No.: <input type="text" size="5" name="scont_box_no[]" value="<?php echo $sales_cont['scont_box_no'];?>">
                                Serails: <input type="text" size="40" name="scont_serials[]" value="<?php echo $sales_cont['scont_serials'];?>" style="width:900px;">
                                <a class="sublink" onclick="sales_second_row_hide(this)">Hide</a>
                            </td>
                        </tr>
                        </tbody>
                    <?php
                        }
                    }
                    ?>
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
        </div>
        <div class="nd_block sale_note">
            <label class="nd_label">Note</label>
            <textarea class="nd_textarea" name="sales_descr" maxlength="1000"><?php echo $row['sales_descr'];?></textarea>
        </div>
    </form>
    <!--Package TAB-->
    <form id="package" class="nd_body nd_block nd_tabdiv nd_tabdiv_inactive" >
        <table class="table_round2" width="100%">
            <col width="25%"><col width="25%"><col width="25%"><col width="25%">
            <tr>
            <td class="fancy_td" colspan="2"><b>Shipped to:</b> &nbsp;&nbsp;
                <label><input onchange="sales_shipped_to_func(this)" type="radio" value="2" <?php if($row['sales_shipped_to_flag']==='2') echo'checked';?> name="sales_shipped_to_flag"> Enter manualy</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <label><input onchange="sales_shipped_to_func(this)" type="radio" value="0" <?php if($row['sales_shipped_to_flag']==='0') echo'checked';?> name="sales_shipped_to_flag"> Same as "Invoice to"</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <input onchange="sales_shipped_to_func(this)" type="radio" value="1" <?php if($row['sales_shipped_to_flag']==='1') echo'checked';?> name="sales_shipped_to_flag">
                <span class="shipped_to_conteiner1 customer_conteiner <?php if($row['sales_shipped_to_flag']!=='1') echo'disabled';?>">
                    <?php echo selector('customers','name="sales_shipped_to"',$row['sales_shipped_to']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </span>
                <div class="shipped_to_conteiner2 <?php if($row['sales_shipped_to_flag']!=='2') echo 'disabled';?>">
                    <input type="text" style="width:100%" name="sales_shipped_name" placeholder="Company name" value="<?php echo $shipped_to_name;?>">
                    <input type="text" style="width:100%" name="sales_shipped_addr1" placeholder="Street, building, office" value="<?php echo $shipped_to_addr1;?>">
                    <input type="text" style="width:100%" name="sales_shipped_addr2" placeholder="ZIP, City" value="<?php echo $shipped_to_addr2;?>">
                    <input type="text" style="width:100%" name="sales_shipped_country" placeholder="Country" value="<?php echo $shipped_to_country;?>">
                    <input type="text" style="width:100%" name="sales_shipped_vat" placeholder="VAT number" value="<?php echo  $shipped_to_vat;?>">
                </div>
            </td>
            <td class="fancy_td" colspan="2"><b>Shipped from</b> &nbsp;&nbsp;
                <label><input onchange="sales_shipped_from_func(this)" type="radio" value="2" <?php if($row['sales_shipped_from_flag']==='2') echo'checked';?> name="sales_shipped_from_flag"> Enter manualy</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <label><input onchange="sales_shipped_from_func(this)" type="radio" value="0" <?php if($row['sales_shipped_from_flag']==='0') echo'checked';?> name="sales_shipped_from_flag"> Same as "Invoice from"</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                <input onchange="sales_shipped_from_func(this)" type="radio" value="1" <?php if($row['sales_shipped_from_flag']==='1') echo'checked';?> name="sales_shipped_from_flag">
                <span class="shipped_from_conteiner1 customer_conteiner <?php if($row['sales_shipped_from_flag']!=='1') echo'disabled';?>">
                    <?php echo selector('customers','name="sales_shipped_from"',$row['sales_shipped_from']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </span>                
                <div class="shipped_from_conteiner2 <?php if($row['sales_shipped_from_flag']!=='2') echo 'disabled';?>">
                    <input type="text" style="width:100%" name="sales_shipped_from_name" placeholder="Company name" value="<?php echo $shipped_from_name;?>">
                    <input type="text" style="width:100%" name="sales_shipped_from_addr1" placeholder="Street, building, office" value="<?php echo $shipped_from_addr1;?>">
                    <input type="text" style="width:100%" name="sales_shipped_from_addr2" placeholder="ZIP, City" value="<?php echo $shipped_from_addr2;?>">
                    <input type="text" style="width:100%" name="sales_shipped_from_country" placeholder="Country" value="<?php echo $shipped_from_country;?>">
                    <input type="text" style="width:100%" name="sales_shipped_from_vat" placeholder="VAT number" value="<?php echo $shipped_from_vat;?>">
                </div>
            </td>
            </tr>
            <tr>
                <td><b>Shipped on</b></td>
                <td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="sales_ship_date" value="<?php echo $row['sales_ship_date'];?>"></td>
                <td><b>Shipped by</b></td>
                <td><input type="text" maxlength="150" placeholder="DHL, UPS, TNT etc." name="sales_shipped_on" value="<?php echo $row['sales_shipped_on'];?>"></td>
            </tr>
            <tr>
                <td><b>Delivery terms</b></td>
                <td><input type="text" name="sales_delevery_terms" maxlength="100" value="<?php echo $row['sales_delevery_terms'];?>"></td>
                <td><b>AWB</b></td>
                <td><input type="text" name="sales_awb" maxlength="150" value="<?php echo $row['sales_awb'];?>"></td>
            </tr>
        </table>
        <h2>Package</h2>
        <table width="100%" class="table_round2" id="sales_package_table">
            <thead>
                <th>Box No.</th>
                <th>Width</th>
                <th>Depth</th>
                <th>Height</th>
                <th>Weight</th>
                <th>Contents</th>
                <th></th>
            </thead>
            <tbody>
<?php
//EXISTING PACKAGE
$query= 'SELECT * FROM sales_package WHERE sales_pack_sale_id = "'.$sale_id.'"';
if(!$result=$db->query($query))exit($db->error);
if($result->num_rows>0){
    while($sales_pack = $result->fetch_assoc()){
        ?>   
        <tr>
            <td><input type="text" name="sales_pack_box_no[]" value="<?php echo $sales_pack['sales_pack_box_no'];?>"></td>
            <td><input type="text" name="sales_pack_width[]" value="<?php echo $sales_pack['sales_pack_width'];?>"></td>
            <td><input type="text" name="sales_pack_depth[]" value="<?php echo $sales_pack['sales_pack_depth'];?>"></td>
            <td><input type="text" name="sales_pack_height[]" value="<?php echo $sales_pack['sales_pack_height'];?>"></td>
            <td><input type="text" name="sales_pack_weight[]" value="<?php echo $sales_pack['sales_pack_weight'];?>"></td>
            <td><input type="text" name="sales_pack_content[]" value="<?php echo $sales_pack['sales_pack_content'];?>"></td>
            <td><input type="button" class="knopka" onclick="table_delete_row(this)" value="Delete"></td>
        </tr>
        <?php
    }
}
?>
            </tbody>
        </table>
        <br><a class="knopka" href="#" onclick="sales_add_package_line(this)">Add package</a> 
        <a href="/scripts/sales_pack_list.php?id=<?php echo $sale_id; ?>"><img height="15px" src="./icons_/print.png">Download packing list</a> | 
        <a href="/scripts/sales_delivery_note.php?id=<?php echo $sale_id; ?>"><img height="15px" src="./icons_/print.png">Download delivery note</a>       
    </form>
    <!--Related Invoices-->
    <div id="invoices_related" class="nd_body nd_block nd_tabdiv nd_tabdiv_inactive">
            <div class="block_div2"><a class="knopka" href="#" onclick="invoice_new(<?php echo '1,\'',$on->order; ?>')">Create new invoice</a></div>
            <?php
            $our_order_type=2;
            $our_order_comp=$sales_our_comp;
            $our_order_id=$sales_no;
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
    <div id="uploaded_files" class="nd_body nd_tabdiv nd_tabdiv_inactive nd_block"></div>
    <div class="nd_footer nd_block">
        <div class="nd_corner_div"><i>Last modified by <?php echo $row['full_name'];?> at <?php echo $row['modified_date'];?></i></div>
        <div align="center" width="100%">
            <button class="nd_button_green" onclick="sales_view_form_submit(this)" class="knopka green_button" href="#">Save changes</button>
            <button class="nd_button" onclick="sales_copy(<?php echo (int)$sale_id;?>,this)" href="#">Copy this order</button>
            <button class="nd_button" onclick="sales_convert_to_invoice('<?php echo $sale_id; ?>')" href="#">Convert to invoice</button>    
        </div>
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
                <input type="search" class="inp_text sales_col_descr" name="scont_text[]" oninput="live_search(this,<?php echo $row['sales_our_comp'];?>)" onblur="sales_inp_blur(this)" value="<?php echo $sales_cont['scont_text'];?>" placeholder="Start typing to search in nomenclature database">
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
                Delivery: <input type="text" size="10" name="scont_delivery[]" placeholder="in stock" value="<?php echo $sales_cont['scont_delevery'];?>">
                Box No.: <input type="text" size="5" name="scont_box_no[]" value="<?php echo $sales_cont['scont_box_no'];?>">
                Serails: <input type="text" size="40" name="scont_serials[]" value="<?php echo $sales_cont['scont_serials'];?>" style="width:900px;">
                <a class="sublink" onclick="sales_second_row_hide(this)">Hide</a>
            </td>
        </tr>
        </tbody>
        <tr id="package_new_row">
            <td><input type="text" name="sales_pack_box_no[]"></td>
            <td><input type="text" name="sales_pack_width[]"></td>
            <td><input type="text" name="sales_pack_depth[]"></td>
            <td><input type="text" name="sales_pack_height[]"></td>
            <td><input type="text" name="sales_pack_weight[]"></td>
            <td><input type="text" name="sales_pack_content[]"></td>
            <td><input type="button" class="knopka" onclick="table_delete_row(this)" value="Delete"></td>
        </tr>
    </table>
</div>
</div>
    