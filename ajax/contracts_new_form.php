<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/contracts_fns.php';
require_once '../functions/selector.php';

$cur_list=get_currency_list();
$comp_list = get_our_companies_list(1);

?>
<style>
    .grid_contract_conteiner{
        display: grid;
        grid-template-columns: 1fr 1fr 230px;
        grid-template-areas:
        "grid_contract_info grid_customer_info grid_related_orders"
        "grid_contract_descr grid_payment grid_related_orders";
    }
    #grid_contract_info {
        grid-area: grid_contract_info;
    }
    #grid_customer_info {
        grid-area: grid_customer_info;
    }
    #grid_related_orders {
        grid-area: grid_related_orders;
    }
    #grid_inv_instructions {
        grid-area: grid_inv_instructions;
    }
    #grid_payment {
        grid-area: grid_payment;
    }
    .sales_quotation_conteiner{
        width:100%;
        position: relative;
    }
</style>
<div class="window_internal" style="width:90%; max-width: 1280px;height:500px;max-height:100%;">
<form class="sales_view_form" style="height:100%;">
<div class="window_container">
    <div class="grid_window_header">
        <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this)">&#10006;</a></div>
        <link rel="stylesheet" type="text/css" href="css/sales.css">
        <h2>New contract<?php echo $contract_display_name;?></h2>
    </div>
    <div class="grid_window_body1">
        <!--General TAB-->
        <div id="general" class="tab" style="display:block;">
            <div class="grid_contract_conteiner">
            <div id="grid_contract_info" class="block_div2 calc_fancy_div">
                <table>
                    <tr>
                        <td><b>Contract #</b></td><td><input type="text" name="contract_our_num" size="5" maxlength="40" value=""></td>
                        <td><b>Use External #</b><input type="checkbox" name="contract_num_flag" value="1"></td><td><input type="text" name="contract_num" size="5" maxlength="40""></td>
                    </tr>
                    <tr>
                        <td><b>Date</b></td><td><input type="text" required="true" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="contract_date" value="<?php echo $row['contract_date'];?>"></td>
                        <td><b>Expire</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="contract_expire" value="<?php echo $row['contract_expire'];?>"></td>
                        
                    </tr>
                    <tr>
                        <td><b>Status</b></td><td><?php echo select_contract_status($row['contract_status']);?></td>
                        <td><b>Type</b></td><td><?php echo select_contract_type($row['contract_type']);?></td>
                    </tr>
                </table>
            </div>
            <div id="grid_customer_info" class="block_div2 calc_fancy_div">
                <table>
                    <tr>
                        <td><b>Our company</b></td><td><?php echo select_our_company2($comp_list,0,'required name="contract_our_comp"');?></td>
                        <td><b>Customer</b></td><td><?php echo selector_customer('name="contract_customer" required',$row['contract_customer']);?> <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)"></td>
                    </tr>
                    <tr>
                        <td><b>Currency</b></td><td><?php echo select_currency2($cur_list,$row['contract_currency'], 'name="contract_currency" required');?></td>
                        <td><b>Contract amount</b></td><td><input type="number" step="0.01" name="contract_amount" value="<?php echo $row['contract_amount'];?>"></td>
                    </tr>
                </table>
            </div>
            <div id="grid_payment" class="block_div2 calc_fancy_div">
                <strong>Note</strong><br>
                <textarea name="contract_note" rows="3" maxlength="500" style="width:100%;margin:0;padding:0;resize: none;"><?php echo $row['contract_note'];?></textarea>
            </div>
            <div id="grid_related_orders" class="block_div2 align_center">
                    <h3>Links</h3>
                <br>
                You must save order to be able to add links.
            </div>
            <div id="grid_contract_descr" class="block_div2 calc_fancy_div">
                <strong>Contract description</strong><br>
                <textarea name="contract_descr" rows="3" style="width:100%;margin:0;padding:0;resize: none;"><?php echo $row['contract_descr'];?></textarea>
            </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $sale_id;?>">
        </div>
    </div>
    <div class="grid_window_body2">
    </div>
    <div class="grid_window_footer">
        <i>Last modified by <?php echo $row['full_name'];?> at <?php echo $row['modified_date'];?></i>
        <div align="center" width="100%">
            <input type="button" onclick="contracts_new_submit(this)" class="green_button" value="Save changes">
        </div>
    </div>
</div>
</form>    