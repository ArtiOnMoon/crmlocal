<?php
require_once 'functions/stock_fns.php';
require_once 'functions/fns.php';
require_once 'functions/selector.php';
startSession();
security();
if(check_access('acl_stock', 2)) exit('Access denied.');
if (isset($_POST['id'])) $id=$_POST['id'];
else exit('Nothing found');
?>
<style>
    .stock_view_grid_conteiner{
        display:grid;
    }
</style>
<div class="window_internal" style="width:1024px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this)">&#10006;</a></div>
<h1> Complect № <?php echo $id;?></h1>
<?php
$comp_list = get_our_companies_list(1);
$db =  db_connect();
$query='SELECT stock_complects.*,manufacturers.mnf_short_name, stock_cat_name, users.full_name '
        . 'FROM stock_complects '
        . 'LEFT JOIN manufacturers ON complect_maker=mnf_id '
        . 'LEFT JOIN stock_cats ON complect_cat=stock_cats.id '
        . 'LEFT JOIN users on users.uid=complect_mod_by '
        . 'WHERE complect_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1) exit('Nothing found.');
$row=$result->fetch_assoc();
?>
<form onsubmit="return change_stock_complect(this)">
    <input type="hidden" name="complect_id" value="<?php echo $id;?>">
    <div class="block_div2">
        <table width="100%">
            <tr>
                <td><b>Complect name</b></td>
                <td class="fancy_td" colspan="3"><input type="text" maxlength="200" size="100" name="complect_name" value="<?php echo $row['complect_name'];?>"></td>
            </tr>
            <tr>
                <td><b>Category</b></td>
                <td><?php echo select_stock_class($row['complect_cat'], 0, 'reqired name="complect_cat"');?></td>
                <td><b>Manufacturer</b></td>
                <td><?php echo select_manufacturer($row['complect_maker'],'reqired name="complect_maker"');?></td>
            </tr>
            <tr>
                <td><b>Note</b></td>
                <td class="fancy_td" colspan="3"><textarea name="complect_note" maxlength="200" rows="2" cols="100" style="resize: none;"><?php echo $row['complect_note'];?></textarea></td>
            </tr>
        </table>
    </div>
    <!-- COMPLECT CONTENT-->
    <div>
    <table  border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <thead>
        <th width="50" keyword="stock_id" <?php sort_class('stock_id',$sort_field,$sort_type);?>>ID</th>
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
        <th width="50" keyword="our_name" <?php sort_class('our_name',$sort_field,$sort_type);?>>Owner</th>
        <th keyword="stock_note" <?php sort_class('stock_note',$sort_field,$sort_type);?>>Note</th>
    </thead>
    <tbody>
<?php
    //Вывод содержимого комплекта
    $query='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, c1.cust_id, stock_stat_name,'
        . 'c1.cust_short_name,stockl_id, stockl_name, stock_cat_name, curr_name, our_companies.id, our_companies.our_name,sales_id '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN customers c1 ON stock_supplier=cust_id '
        . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN currency ON stock_currency=curr_id '
        . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
        . 'LEFT JOIN sales ON sales_no=stock_so AND sales_our_comp=stock_so_comp '
        . 'WHERE stock_new.stock_deleted=0 AND stock_compl_id='.$id;
    $result=$db->query($query);
    while($row = $result->fetch_assoc()){
        echo '<tr>';
        echo '<td><a href="#" onclick="stock_edit(',(int)$row['stock_id'],')">',$row['stock_id'],'</td>'
            , '<td ',color_table($row['stock_status'],$row['stock_condition']),'>',$row['stock_stat_name'],'</td>'
            , '<td>',$row['stock_cat_name'],'</td>'
            , '<td>',$row['mnf_short_name'],'</td>'
            , '<td>',$row['stnmc_pn'],'</td>'
            , '<td>',$row['stnmc_type_model'],'</td>'
            , long_td_string($row['stnmc_descr'],20)
            , '<td>',$row['stock_serial'],'</td>'
            , '<td>',view_stocklist_item($row['stock_stock_id'], $row['stockl_name']).'</td>'
            , '<td>',$row['stock_place'],'</td>'
            , '<td>',condition_decode($row['stock_condition']),'</td>'
            , '<td>', view_our_company_link($row['id'], $row['our_name']),'</td>'
            , long_td_string($row['stock_note'],20)
            , '</tr>';
    }
?>
    </tbody></table>   
    <br><b>DELETE position</b> <input type="checkbox" name="stock_deleted" value="1">
    <br><span style="font-style: italic">Last modified by: <?php echo $row['full_name']; ?><br> at <?php echo $row['complect_mod_date']; ?></span>
    <div align="center"><input type="submit" class="green_button" value="Save"> <input type="button" value="Close" onclick="window_close(this)"></div>
    <input type="hidden" name="stock_id" value="<?php echo $row['stock_id'];?>">
    </div>
</form>
</div>
