<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'classes/Order_name_engine.php';

session_start();
if(check_access('acl_stock', 1)) exit('Access denied.');

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);

$our_companies_list= get_our_companies_list(1);

$class=clean($_POST['class']);
//$status=clean($_POST['stat']);
$status=json_decode($_POST['stock_statuses']);
$cond=clean($_POST['cond']);
if (isset($_POST['supplier']))$supplier=clean($_POST['supplier']);else $supplier = '';
if (isset($_POST['on_balance']))$on_balance=clean($_POST['on_balance']);else $on_balance = '';
$manufacturer=clean($_POST['manufacturer']);
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$stock=clean($_POST['stock']);
$our_company=clean($_POST['our_company']);
$pn_or_type=clean($_POST['pn_or_type']);
$serial=clean($_POST['serial']);
$description=clean($_POST['description']);
$type_model=clean($_POST['type_model']);
$stock_id=clean($_POST['stock_id']);
$place=clean($_POST['place']);
$note=clean($_POST['note']);
$ccd=clean($_POST['ccd']);
$po=clean($_POST['po']);
$so=clean($_POST['so']);
if (isset($_POST['stock_calc_price']))$stock_calc_price=clean($_POST['stock_calc_price']);

if ($supplier=='All')$supplier='';
if ($manufacturer=='All')$manufacturer='';
//DATE
$rdate_start=clean($_POST['rdate_start']);
$rdate_end=clean($_POST['rdate_end']);
$sdate_start=clean($_POST['sdate_start']);
$sdate_end=clean($_POST['sdate_end']);

//SEARCH
$search_field=clean($_POST['search']);
$search='';
if ($search_field!=='')$search=' AND (stnmc_pn LIKE ("%'.$search_field.'%") '
        . 'OR stnmc_descr LIKE ("%'.$search_field.'%") '
        . 'OR stock_note LIKE ("%'.$search_field.'%") '
        . 'OR stock_serial LIKE ("%'.$search_field.'%") '
        . 'OR stnmc_type_model LIKE ("%'.$search_field.'%")) ';
//END SEARCH
$limit=100;
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;

$query='SELECT stock_id FROM stock_new LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id WHERE stock_deleted=0';
$condition='';
if ($class!=='All') $condition.=' and stnmc_type="'.$class.'"';
if (!empty($status)) $condition.=' and stock_status IN ('.implode(',',$status).')';
if ($cond!='0') $condition.=' and stock_condition="'.$cond.'"';
if ($on_balance!='') $condition.=' and on_balance="'.$on_balance.'"';
if ($stock!='All') $condition.=' and stock_stock_id="'.$stock.'"';
if ($manufacturer!='') $condition.=' and stnmc_manuf="'.$manufacturer.'"';
//if ($our_company!=='All') $condition.=' and stock_our_company="'.$our_company.'"';
if ($pn_or_type!=='') $condition.=' and stnmc_pn like ("%'.$pn_or_type.'%")';
if ($serial!=='') $condition.=' and stock_serial like ("%'.$serial.'%")';
if ($description!=='')  $condition.=' and stnmc_descr like ("%'.$description.'%")';
if ($type_model!=='')   $condition.=' and stnmc_type_model like ("%'.$type_model.'%")';
if ($stock_id!=='')   $condition.=' and (stock_id="'.$stock_id.'" OR stock_compl_id="'.$stock_id.'")';
//if ($place!=='') $condition.=' and stock_place like ("%'.$place.'%")';
if ($place!=='') $condition.=' and stock_place = ("'.$place.'")';
if ($note!=='') $condition.=' and stock_note like ("%'.$note.'%")';
if ($ccd!=='') $condition.=' and stock_ccd like ("%'.$ccd.'%")';
if ($po!=='') $condition.=' and CONCAT_WS(".",stock_po_comp,stock_po) like ("%'.$po.'%")';
if ($so!=='') $condition.=' and CONCAT_WS(".",stock_so_comp,stock_so) like ("%'.$so.'%")';
if ($rdate_start!=='') $condition.=' and stock_date_receipt > ("'.$rdate_start.'")';
if ($rdate_end!=='') $condition.=' and stock_date_receipt < ("'.$rdate_end.'")';
if ($sdate_start!=='') $condition.=' and stock_sale_date >= ("'.$sdate_start.'")';
if ($sdate_end!=='') $condition.=' and stock_sale_date <= ("'.$sdate_end.'")';

$query.=$condition.$search;
if (!$result2=$db->query($query)) {
    echo $db->error;
    echo $query;
}
$num = $result2->num_rows;
//if ($num===0 OR $num=='') exit('No results');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;
$_SESSION['stock_page']=$page; //номер страницы в переменную сессии

$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

//SORT
$sort = ' ORDER BY stock_id DESC ';
if ($sort_field!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
$query='SELECT stock_new.*, stock_nmnc.*,mnf_short_name, stock_stat_name,'
        . 'stockl_id, stockl_name, stock_cat_name, curr_symb, our_companies.id, our_companies.our_name,'
        . '(stock_price + stock_freight) as stock_calc_price '
        . 'FROM stock_new '
        . 'LEFT JOIN stock_nmnc ON stock_nmnc_id=stnmc_id '
        . 'LEFT JOIN stock_status ON stock_status=stock_stat_id '
        . 'LEFT JOIN our_companies ON stock_our_company=our_companies.id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN currency ON stock_currency=curr_id '
        . 'LEFT JOIN stock_list ON stock_stock_id=stockl_id '
        . 'WHERE stock_new.stock_deleted=0 ';
$query.=$condition.$search.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);
//if ($result->num_rows===0) exit('No results');
//echo $query;
?>
<div id="main_subheader">
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_stock_new_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_stock_new_table('.($next_page).')" value="Next page"></span>';
echo '<span style="float:right">Records:<span id="rec1">'.$num.'</span>&nbsp</span>';
//echo '<input type="button" onclick="go_to()" value="Go to"><input id="go_to" type="text" value="" size="3"><br>';
?>
</div>
<div id="main_subbody">
<div id="table_wrap">
    <table id="stock_table" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
    <form action="stock_multi_edit.php" method="POST" target='_blank'>
    <thead class="stock_thead" onclick="table_sort(event,'stock')">
        <th width="10px"><input type='checkbox' id='main_checkbox' onchange='check_all_checkboxes(this)'></th>
        <th width="60px" colspan="2" keyword="stock_id" <?php sort_class('stock_id',$sort_field,$sort_type);?>>ID<br><input style="width:100%;"type="search" id="stock_id" size="4" onchange="show_stock_new_table()" value="<?php echo $stock_id;?>"></th>
        <!--<th width="50" keyword="stock_stat_name" <?php sort_class('stock_stat_name',$sort_field,$sort_type);?>>Status<br><?php echo select_stock_stat($status,1,'onchange="show_stock_new_table()"'); ?></th>-->
        <!--<th width="50" keyword="stock_stat_name" <?php sort_class('stock_stat_name',$sort_field,$sort_type);?>>Status<br><?php echo selector_multi_stock_status(); ?></th>-->
        <th width="80px" keyword="stock_stat_name" <?php sort_class('stock_stat_name',$sort_field,$sort_type);?>>Status
            <?php echo selector_multi_stock_status($status);?>
        <!--<div class="hidden_conteiner"  style="display:inline-block;position:relative;width:100%"><label onclick="display_menu('hidden_div','list_sign')">Select<span id="list_sign" class="sign">&#9660</span></label>
            <div class="hidden_div stock_selector_window" id="hidden_div" style="margin-left:-5px;margin-top:5px; vertical-align: top;">
                <label><input class="service_filter" type='checkbox' id='status_1' onchange="show_service_table()">Request</label><br>
                <label><input class="service_filter" type='checkbox' id='status_2' onchange="show_service_table()">Quotation</label><br>
                <label><input class="service_filter" type='checkbox' id='status_3' onchange="show_service_table()">Confirmed</label><br>
                <label><input class="service_filter" type='checkbox' id='status_6' onchange="show_service_table()">Completed</label><br>
                <label><input class="service_filter" type='checkbox' id='status_7' onchange="show_service_table()">Follow-Up</label><br>
                <label><input class="service_filter" type='checkbox' id='status_5' onchange="show_service_table()">Canceled</label><br>
                <label><input class="service_filter" type='checkbox' id='status_8' onchange="show_service_table()">Expired</label><br>
                <button type="button" onclick="check_all()">Check all</button>
            </div>
        </div>-->
            <div id="invis_wrap" onclick="close_menu()"></div>
        </th>
        <th width="60px" keyword="stock_cat_name" <?php sort_class('stock_cat_name',$sort_field,$sort_type);?>>Category<br><?php echo select_stock_class($class,1,'id="stock_view" onchange="show_stock_new_table()"');?></th>
        <th width="80px" keyword="mnf_short_name" <?php sort_class('mnf_short_name',$sort_field,$sort_type);?>>Maker<br><?php echo select_manufacturer($manufacturer,'id="manufacturer" onchange="show_stock_new_table()"','1'); ?></th>
        <th width="120" keyword="stnmc_pn" <?php sort_class('stnmc_pn',$sort_field,$sort_type);?>>P/N<br><input type="search" id="pn_or_type" class="stock_thead_input" size="10" onchange="show_stock_new_table()" value="<?php echo $pn_or_type;?>"></th>
        <th width="120" keyword="stnmc_type_model" <?php sort_class('stnmc_type_model',$sort_field,$sort_type);?>>Type\model<br><input type="search" style="width:100%" id="type_model" size="10" onchange="show_stock_new_table()" value="<?php echo $type_model;?>"></th>
        <th keyword="stnmc_descr" <?php sort_class('stnmc_descr',$sort_field,$sort_type);?>>Description<br><input type="search" id="description" style="width:100%" onchange="show_stock_new_table()" value="<?php echo $description;?>"></th>
        <th width="125" keyword="stock_serial" <?php sort_class('stock_serial',$sort_field,$sort_type);?>>Serial<br><input type="search" class="stock_thead_input" id="serial" size="10" onchange="show_stock_new_table()" value="<?php echo $serial;?>"></th>
        <th width="75" keyword="stockl_name" <?php sort_class('stockl_name',$sort_field,$sort_type);?>>Stock<br><?php echo select_stock($stock,'id="select_stock" onchange="show_stock_new_table()"',1);?></th>
        <th width="25" keyword="stock_place" <?php sort_class('stock_place',$sort_field,$sort_type);?>>Place<br><input type="search" id="place" class="stock_thead_input" size="4" onchange="show_stock_new_table()" value="<?php echo $place;?>"></th>
        <th width="25" keyword="stock_condition" <?php sort_class('stock_condition',$sort_field,$sort_type);?>>Condition<br><?php echo select_condition($cond,1,'id="cond" onchange="show_stock_new_table()"'); ?></th>
<!--    <th width="50" keyword="our_name" <?php sort_class('our_name',$sort_field,$sort_type);?>>Owner<br><?php echo select_our_company2($our_companies_list,$our_company,'id="our_company" onchange="show_stock_new_table()"',1); ?></th> -->
        <th width="150px" keyword="stock_note" <?php sort_class('stock_note',$sort_field,$sort_type);?>>Note<br><input type="search" id="note" class="stock_thead_input" onchange="show_stock_new_table()" value="<?php echo $note;?>"></th>
<!--    <th width="75px" keyword="stock_ccd" <?php sort_class('stock_ccd',$sort_field,$sort_type);?>>CCD<br><input type="search" id="ccd" class="stock_thead_input" onchange="show_stock_new_table()" value="<?php echo $ccd;?>"></th>-->
<!--    <th width='50' keyword="stock_price" <?php sort_class('stock_price',$sort_field,$sort_type);?>>Net price</th>
        <th width='50' keyword="curr_name" <?php sort_class('curr_name',$sort_field,$sort_type);?>>Currency</th>
-->     <th width="90px" keyword="stock_po" <?php sort_class('stock_po',$sort_field,$sort_type);?>>PO №<br><input type="search" id="po" class="stock_thead_input" size="3" onchange="show_stock_new_table()" value="<?php echo $po;?>"></th>
        <th width="90px" keyword="stock_so" <?php sort_class('stock_so',$sort_field,$sort_type);?>>SO №<br><input type="search" id="so" class="stock_thead_input" size="3" onchange="show_stock_new_table()" value="<?php echo $so;?>"></th>
<!--    <th width="60px" keyword="stock_calc_price" <?php sort_class('stock_calc_price',$sort_field,$sort_type);?>>Price</th> -->
    </thead>
    <tbody>
<?php
if ($result->num_rows===0 || !$result) {exit('<tr><td colspan="18">No results</td></tr></table>');}

while($row = $result->fetch_assoc()){
    echo '<tr class="',stock_tr_is_sold($row['stock_status']),'">';
    echo '<td><input type="checkbox" class="table_checkbox" name="edit[]" value="',$row['stock_id'],'"></td>';
    echo stock_view_link($row['stock_id'],$row['stock_compl_id'],$row['stock_is_compl'])//,'</td>'
            , '<td ',color_table($row['stock_status'],$row['stock_condition']),'>',$row['stock_stat_name'],'</td>'
            , '<td>',$row['stock_cat_name'],'</td>'
            , '<td>',$row['mnf_short_name'],'</td>'
            , '<td>',$row['stnmc_pn'],'</td>'
            , '<td>',$row['stnmc_type_model'],'</td>'
            , long_td_string($row['stnmc_descr'],60)
            , '<td>',$row['stock_serial'],'</td>'
            , '<td>',view_stocklist_item($row['stock_stock_id'], $row['stockl_name']).'</td>'
            , '<td>',$row['stock_place'],'</td>'
            ,condition_decode($row['stock_condition'])
            //, '<td>', view_our_company_link($row['id'], $row['our_name']),'</td>'
            , long_td_string($row['stock_note'],20)
            //, '<td>',$row['stock_ccd'],'</td>'
            //, '<td>',$row['stock_price'],'</td>'
            //, '<td>',$row['curr_name'],'</td>'
//            , '<td>',stock_view_po($row['stock_po_comp'],$row['stock_po'],$row['stock_po_type']),'</td>'
            , '<td><a href="#" onclick="view_link(\'',$row['stock_po'],'\')">',$row['stock_po'],'</a></td>'
//            , '<td>',stock_view_sales($row['stock_so_comp'],$row['stock_so'],$row['stock_so_type']).'</td>'
            , '<td><a href="#" onclick="view_link(\'',$row['stock_so'],'\')">',$row['stock_so'],'</a></td>'
            //, '<td class="align_right">',number_format($row['stock_calc_price'],2,'.','').' ',$row['curr_symb'],'</td>'
            , '</tr>';
}
?>
    </tbody>
    </form>
    </table>
</div>
</div>
<div id="main_subfooter">
<!--<input type="submit" value="Edit selected">-->
<input type="button" value="Edit selected" onclick="stock_edit2()">
</div>