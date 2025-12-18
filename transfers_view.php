<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'functions/stock_fns.php';
require_once 'functions/transfers.php';
?>
<style>
    .simple_grid_container{
        width:1024px;
        height:90%;
        display:grid;
        grid-template-rows: auto 1fr auto;
        overflow:hidden;
    }
    .simple_grid_header{
        grid-row: 1;
        grid-column: 1;
        border-bottom: 2px solid black;
    }
    .simple_grid_body{
        grid-row: 2;
        grid-column: 1;
    }
    .simple_grid_footer{
        border-top:1px solid black;
        grid-row: 3;
        grid-column: 1;
        padding: 10px;
        text-align: center;
    }
    .selector_stock_menu{
        background: #EEE;
        padding:10px;
    }
</style>

<form method="POST" action="/scripts/transfers_change.php">
<div class="window_internal simple_grid_container">
    <div class="simple_grid_header">
        <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
        
<?php
$comp_list = get_our_companies_list(1);
$transfer_id=clean($_POST['transfer_id']);
$db =  db_connect();
$query='SELECT * FROM transfers '
        . 'WHERE transfer_id="'.$transfer_id.'"';
if(!$result=$db->query($query)){
    exit ("Nothing found.</div>");
}
$row=$result->fetch_assoc();
?>
        <h2 align="center">Transfer order #TR<?php echo $transfer_id;?></h2>
        <div class="block_div2">
            <table width="100%">
                <tr>
                    <td><b>Transfer ID</b></td><td><input type="text" name="sales_no" size="5" maxlength="10" value="<?php echo $row['transfer_id'];?>"></td>
                    <td><b>Status</b></td><td><?php echo select_transfer_status($row['transfers_status']);?></td>
                    <td><b>From stock</b></td><td><?php echo select_stock($row['transfers_from'],'name="transfers_from"');?></td>
                    <td><b>To stock</b></td><td><?php echo select_stock($row['transfers_to'],'name="transfers_to"');?></td>
                    <td><b>Note</b></td><td rowspan="2"><textarea><?php echo $row['transfers_note'];?></textarea></td>
                </tr>
                <tr>
                    <td><b>Date</b></td><td><input type="text" required="true" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="sales_date" value="<?php echo date('Y-m-d');?>"></td>
                    <td><b>AWB</b></td><td><input type="text" size="10"  name="transfers_awb" value=""></td>
                    <td><b>Ship date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="transfers_from_date" value=""></td>
                    <td><b>Delivery date</b></td><td><input type="text" placeholder="yyyy-mm-dd" size="10" class="datepicker" name="transfers_to_date" value=""></td>
                </tr>
            </table>
        </div>
        <div style="text-align: center; margin-bottom: 5px;"><a class="button" href="#" onclick="transfers_stock_select(this,<?php echo (int)$transfer_id;?>)">Select items from stock</a></div>
    </div>
    <div class="stock_selector_sub_form simple_grid_body">
        <?php
        $query='SELECT * FROM transfers_content '
                . 'LEFT JOIN stock_new ON tc_stock_id=stock_id '
                . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
                . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
                . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
                . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
                . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
                . 'WHERE tc_transfer_id="'.$transfer_id.'"';
        $result=$db->query($query);
        echo $db->error;
        if ($result->num_rows>0){
            ?>
            <table class="sort_table" width="100%">
                <thead class="stock_thead" >
                    <th colspan="2">ID</th>
                    <th>Status </th>
                    <th>Category</th>
                    <th>Maker</th>
                    <th>P/N</th>
                    <th>Type\model</th>
                    <th>Description</th>
                    <th>Serial</th>
                    <th>PO №</th>
                    <th>Delivered</th>
                    <th></th>
                </thead>
                <tbody>
            <?php
            while ($row=$result->fetch_assoc()){
                echo '<tr class="',stock_tr_is_sold($row['stock_status']),'"><input type="hidden" name="tc_stock_id[]" value="'.$row['stock_id'].'">';
                echo stock_view_link($row['stock_id'],$row['stock_compl_id'],$row['stock_is_compl'])//,'</td>'
                    , '<td ',color_table($row['stock_status'],$row['stock_condition']),'>',$row['stock_stat_name'],'</td>'
                    , '<td>',$row['stock_cat_name'],'</td>'
                    , '<td>',$row['mnf_short_name'],'</td>'
                    , '<td>',$row['stnmc_pn'],'</td>'
                    , '<td>',$row['stnmc_type_model'],'</td>'
                    , long_td_string($row['stnmc_descr'],60)
                    , '<td>',$row['stock_serial'],'</td>'
                    , '<td>',stock_view_po($row['stock_po_comp'],$row['stock_po'],$row['stock_po_type']),'</td>'
                    , '<td><input size="10" type="text" class="datepicker" name="tc_delivered_date[]" value="'.$row['tc_delivered_date'].'"></td>'
                    , '<td><button type="button" onclick="table_delete_row(this)"><img class="line_image" src="/icons_/del.png"></button></td>'
                    , '</tr>';
            }
            ?></tbody></table><?php
        }
        ?>
    </div>  
    <div class="simple_grid_footer">
        <a class="knopka green_button" href="#" onclick="return transfers_new_form_submit_outer(this)">Save</a>
        <a class="knopka" href="#" onclick="window_close(this);">Cancel</a> 
    </div>
</div>
</form>