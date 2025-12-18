<?php
require_once 'functions/stock_fns.php';
require_once 'functions/fns.php';
require_once 'functions/selector.php';
startSession();
security();
if(check_access('acl_stock', 1)) exit('Access denied.');
$id=$_GET['id'];
if (isset($_POST['id'])) $id=$_POST['id'];
?>
<style>
    .stock_view_grid_conteiner{
        display:grid;
    }
</style>
<div class="window_internal" style="width:1024px;min-height:430px;max-height:100%;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this)">&#10006;</a></div>
<h1> Stock Item № <?php echo $id;?></h1>
<?php
$comp_list = get_our_companies_list(1);
$global_array=array();
$db =  db_connect();
$query= 'SELECT stock_new.*, stock_nmnc.*,0 as level, users.full_name, manufacturers.mnf_short_name, stock_cats.stock_cat_name, sales_id FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN users on users.uid= stock_mod_by '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN sales ON sales_no=stock_so AND sales_our_comp=stock_so_comp '
        . 'WHERE stock_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1) exit('Nothing found.');
$row=$result->fetch_assoc();
if ($row['stock_deleted']) $delete='checked'; else $delete='';
//STOCK transfer
$query2='SELECT stock_transfer.*, sl1.stockl_name as from_stock, sl2.stockl_name as to_stock FROM stock_transfer '
        . 'LEFT JOIN stock_list sl1 ON sl1.stockl_id=from_stock '
        . 'LEFT JOIN stock_list sl2 ON sl2.stockl_id=to_stock '
        . 'WHERE stock_id = "'.$id.'" AND stock_transfer.is_deleted=0';

$result2=$db->query($query2);
$transfers_count=$result2->num_rows;
?>
<div class="tab_bar">
    <a class="tab_button selected_tab" tab="general" onclick="openTab(this)" href='#'>Main</a>
    <a class="tab_button" tab="transfer" onclick="openTab(this)" href='#'>Transfers (<?php echo $transfers_count;?>)</a>
</div>
<form id="change_stock_form" onsubmit="return change_stock_item(<?php echo $_SESSION['stock_page'];?>,this)">
    <div class="tab" id="general" style="display:block;">
    <div class="stock_view_grid_conteiner">
        <div class="block_div2 calc_fancy_div">
            <table width="100%">
                <tr>
                    <td><a href="#" onclick="nmnc_view(<?php echo $row['stnmc_id'];?>)">View nomenclature</a></td>
                    <td><b>Category:</b> <?php echo $row['stock_cat_name'];?></td>
                    <td><b>Manufacturer:</b> <?php echo $row['mnf_short_name'];?></td>
                </tr>
                <tr>
                    <td><b>Part number:</b> <?php echo $row['stnmc_pn'];?></td>
                    <td><b>Type\model:</b> <?php echo $row['stnmc_type_model'];?></td>
                    <td><b>Description:</b> <?php echo $row['stnmc_descr'];?></td>
                </tr>
                <tr>
                    <td colspan="3"><b>Note:</b> <?php echo $row['stnmc_note'];?></td>
                </tr>
            </table>
        </div>
        <div class="block_div2 calc_fancy_div">
            <div class="calc_fancy_div"><b>Serial</b> <input type="text" maxlength="50" name="stock_serial" value="<?php echo $row['stock_serial'];?>"></div>
            <div class="calc_fancy_div"><b>Status</b> <?php echo select_stock_stat($row['stock_status']);?></div>
            <div class="calc_fancy_div"><b>Condition</b> <?php echo select_condition($row['stock_condition'],'0','name="stock_condition"');?></div>
            <div class="calc_fancy_div"><b>Owner</b> <?php echo select_our_company2($comp_list,$row['stock_our_company'],'name="stock_our_company"'); ?></div>
            <div class="calc_fancy_div"><label><b>Officialy sold</b> <input 
                        type="checkbox" name="stock_officialy_sold" value="1" 
                            <?php if ($row['stock_officialy_sold']==1)echo 'checked'; ?>>
                </label>
            </div>
        </div>
        <div class="block_div2 calc_fancy_div">
            <div class="calc_fancy_div"><b>Supplier</b> <?php echo selector('customers','name="stock_supplier"',$row['stock_supplier']);?></div>
            <div class="calc_fancy_div">
                <b>Received by</b> <input type="text" size="10"  name="stock_po" value="<?php echo $row['stock_po'];?>">
                <a href="#" onclick="view_link('<?php echo $row['stock_po'];?>')"><img class="line_image" align="middle" src="/icons_/ex_link.png"></a>
                    <?php // echo select_stock_po_link_type($row['stock_po_type']),select_our_company2($comp_list,$row['stock_po_comp'],'name="stock_po_comp"');?>
                    <?php // echo stock_view_po_short($row['stock_po_comp'],$row['stock_po'],$row['stock_po_type']);?>
            </div>
            <div class="calc_fancy_div">
                <b>Sold by</b> <input type="text" size="12"  name="stock_so" value="<?php echo $row['stock_so'];?>">
                <a href="#" onclick="view_link('<?php echo $row['stock_so'];?>')"><img class="line_image" align="middle" src="/icons_/ex_link.png"></a>
                    <?php // echo select_stock_link_type($row['stock_so_type']),select_our_company2($comp_list,$row['stock_so_comp'],'name="stock_so_comp"');?>
                    <?php // echo stock_view_sales_short($row['stock_so_comp'],$row['stock_so'],$row['stock_so_type']);?>
            </div>
        </div>
        <div class="block_div2 calc_fancy_div">
            <div class="calc_fancy_div"><b>Stock</b> <?php echo select_stock($row['stock_stock_id']);?></div>
            <div class="calc_fancy_div"><b>Place</b> <input type="text" size="8" maxlength="20" name="stock_place" value="<?php echo $row['stock_place'];?>"></div>
            <div class="calc_fancy_div"><b>Date of receipt</b> <input type="text" size="10" class="datepicker" name="stock_date_receipt" value="<?php echo $row['stock_date_receipt'];?>"></div>
            <div class="calc_fancy_div"><b>Date of sale</b> <input type="text" size="10" class="datepicker" name="stock_sale_date" value="<?php echo $row['stock_sale_date'];?>"></div>
            <div class="calc_fancy_div"><b>CCD</b> <input type="text" size="20" name="stock_ccd" value="<?php echo $row['stock_ccd'];?>"></div>
        </div>
        <div class="block_div2 calc_fancy_div">
            <!-- <div class="calc_fancy_div"><b>Net price</b> <?php //echo $row['stnmc_price'];?></div> -->
            <div class="calc_fancy_div"><b>Price</b> <input type="number" step="0.01" style="width:65px" name="stock_price" value="<?php echo $row['stock_price'];?>"></div>
            <div class="calc_fancy_div"><b>Freight</b> <input type="number" step="0.01" style="width:65px" name="stock_freight" value="<?php echo $row['stock_freight'];?>"></div>
            <div class="calc_fancy_div"><b>Currency</b> <?php echo select_currency2(get_currency_list(), $row['stock_currency'], 'name="stock_currency"');?></div>
            <div class="calc_fancy_div"><b>Warranty up to</b> <input type="text" size="10" name="stock_warranty_to" class="datepicker" value="<?php echo $row['stock_warranty_to'];?>"> Days left: <?php echo days_left($row['stock_warranty_to']);?></div>
            <div class="calc_fancy_div"><b>Is complect</b> <input type="checkbox" name="stock_is_compl" value="1" <?php if($row['stock_is_compl']==='1') echo 'checked';?>></div>
            <div class="calc_fancy_div complect_conteiner"><b>Complect</b> <input class="complect_field" type="text" size="5" name="stock_compl_id" value="<?php echo $row['stock_compl_id'];?>"><img title="View complect" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="complect_view(this)"></div>
        </div>
        <table class="fancy_table">
            <tr>
                <td class="fancy_td block_div2">
                    <b>Note</b>
                    <textarea name="stock_note" maxlength="500" rows="2" style="width:990px;"><?php echo $row['stock_note']; ?></textarea>
                </td>
            </tr>
        </table>
    </div>
    </div>
    <div class="tab" id="transfer">
        <table class="sort_table" width="100%">
            <thead>
                <th>ID</th>
                <th>From stock</th>
                <th>To stock</th>
                <th>Ship date</th>
                <th>Receipt date</th>
                <th>AWB</th>
                <th>Shipped on</th>
                <th>Note</th>
            </thead>
        <?php
        if ($transfers_count>0){
            while ($row2=$result2->fetch_assoc()){
                echo'<tr><td><a href="#" onclick="stock_transfer_edit(\'',$row2['transfer_id'],'\')">',$row2['transfer_id'],'</a></td>'
                    . '<td>',$row2['from_stock'],'</td>'
                    . '<td>',$row2['to_stock'],'</td>'
                    . '<td>',$row2['ship_date'],'</td>'
                    . '<td>',$row2['receipt_date'],'</td>'
                    . '<td>',$row2['awb'],'</td>'
                    . '<td>',$row2['shipped_on'],'</td>'
                    . '<td>',$row2['note'],'</td></tr>';
            }
        }
        ?>
        </table>
        <br>
        <a class="knopka" href="#" onclick="stock_show_add_transfer(this,<?php echo $id;?>)">Add transfer info</a>
    </div>
<?php
if($row['stock_is_compl']==="1"){
?>
<p><h2>Complect parts</h2>
<!-- COMPLECT CONTENT-->
    <div>
    <table class="sort_table" width="100%">
    <thead>
        <th width="50" keyword="stock_id" colspan="6" <?php sort_class('stock_id',$sort_field,$sort_type);?>>ID</th>
        <th width="50" keyword="stock_stat_name" <?php sort_class('stock_stat_name',$sort_field,$sort_type);?>>Status</th>
        <th width="75" keyword="stock_cat_name" <?php sort_class('stock_cat_name',$sort_field,$sort_type);?>>Category</th>
        <th width="100" keyword="mnf_short_name" <?php sort_class('mnf_short_name',$sort_field,$sort_type);?>>Maker</th>
        <th width="100" keyword="stnmc_pn" <?php sort_class('stnmc_pn',$sort_field,$sort_type);?>>P/N or type</th>
        <th width="75" keyword="stnmc_type_model" <?php sort_class('stnmc_type_model',$sort_field,$sort_type);?>>Type\model</th>
        <th keyword="stnmc_descr" <?php sort_class('stnmc_descr',$sort_field,$sort_type);?>>Description</th>
        <th width="100" keyword="stock_serial" <?php sort_class('stock_serial',$sort_field,$sort_type);?>>Serial</th>
        <th width="75" keyword="stockl_name" <?php sort_class('stockl_name',$sort_field,$sort_type);?>>Stock</th>
        <th width="25" keyword="stock_place" <?php sort_class('stock_place',$sort_field,$sort_type);?>>Place</th>
        <th width="25" keyword="stock_condition" <?php sort_class('stock_condition',$sort_field,$sort_type);?>>Condition</th>
        <!--<th width="50" keyword="our_name" <?php sort_class('our_name',$sort_field,$sort_type);?>>Owner</th>-->
        <!--<th keyword="stock_note" <?php sort_class('stock_note',$sort_field,$sort_type);?>>Note</th>-->
    </thead>
    <tbody>
<?php
    //Вывод содержимого комплекта

    $subquery='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name,0 as level,'
        . 'stockl_name, stock_cat_name, curr_name, our_companies.id, our_companies.our_name,sales_id '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN currency ON stock_currency=curr_id '
        . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
        . 'LEFT JOIN sales ON sales_no=stock_so AND sales_our_comp=stock_so_comp '
        . 'WHERE stock_new.stock_deleted=0 AND stock_compl_id=';
    $key=0;
    $result=$db->query($subquery.$row['stock_id']);
    while($row2=$result->fetch_assoc()){
       $global_array[]=$row2; 
    }
    if (count($global_array)==0)$stop=1;
    while($stop!=1){
        $temp_array=array();
        $result=$db->query($subquery.$global_array[$key]['stock_id']);
        if (!$result->num_rows===0 || !$result) continue;
        while($subrow=$result->fetch_assoc()){
            $subrow['level']=$global_array[$key]['level']+1;
            $temp_array[]=$subrow;
        }
        array_splice($global_array, $key+1, 0, $temp_array);
        $key++;
        if ($key>=count($global_array))$stop=1;
    }
    foreach ($global_array as $compl_part){
        echo '<tr class="',stock_tr_is_sold($compl_part['stock_status']),'">';
        echo stock_view_complect_link($compl_part['stock_id'],$compl_part['level'])
            , '<td ',color_table($compl_part['stock_status'],$compl_part['stock_condition']),'>',$compl_part['stock_stat_name'],'</td>'
            , '<td>',$compl_part['stock_cat_name'],'</td>'
            , '<td>',$compl_part['mnf_short_name'],'</td>'
            , '<td>',$compl_part['stnmc_pn'],'</td>'
            , '<td>',$compl_part['stnmc_type_model'],'</td>'
            , '<td>',$compl_part['stnmc_descr'],'</td>'
            , '<td>',$compl_part['stock_serial'],'</td>'
            , '<td>',view_stocklist_item($compl_part['stock_stock_id'], $compl_part['stockl_name']).'</td>'
            , '<td>',$compl_part['stock_place'],'</td>'
            , condition_decode($compl_part['stock_condition'])
            //, '<td>', view_our_company_link($compl_part['id'], $compl_part['our_name']),'</td>'
            //, long_td_string($compl_part['stock_note'],20)
            , '</tr>';
    }
?>
    </tbody></table>   
    </div>

<?php }?>
    <br><b>DELETE position</b> <input type="checkbox" name="stock_deleted" value="1" <?php echo $delete ?>>
    <br><span style="font-style: italic">Last modified by: <?php echo $row['full_name']; ?><br> at <?php echo $row['stock_mod_date']; ?></span>
    <div align="center"><input type="submit" class="green_button" value="Save"> <input type="button" value="Close" onclick="window_close(this)"></div>
    <input type="hidden" name="stock_id" value="<?php echo $row['stock_id'];?>">
</form>
</div>
