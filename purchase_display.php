<?php
require_once 'functions/db.php';
require_once 'functions/purchase_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'classes/Order_name_engine.php';

session_start();

if(check_access('acl_purchase', 1)) exit('Access denied.');
$limit=50;

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);
$on->type = 'PO';

//SEARCH
$search='';
$keyword=$_POST['keyword'];

if ($keyword!==''){
    try{
        if ($on ->resolve_order($keyword)){
            $search.= ' AND (po_no = ("'.$on->num.'") '
        . 'OR po_note LIKE ("%'.$keyword.'%")) ';
        }
    } catch (Exception $ex) {
        $search.= ' AND (po_no LIKE ("%'.$keyword.'%") '
        . 'OR po_note LIKE ("%'.$keyword.'%")) ';
    }
}
//END SEARCH

//COND
$cond='';
if ($_POST['status']!=='All')$cond.= ' AND po_status="'.clean($_POST['status']).'" ';
if ($_POST['po_our_comp']!=='0')$cond.= ' AND po_our_comp="'.clean($_POST['po_our_comp']).'" ';
if ($_POST['po_supplier']!=='' and $_POST['po_supplier']!=='1')$cond.= ' AND po_supplier="'.clean($_POST['po_supplier']).'" ';
if ($_POST['po_date_from']!=='')$cond.= ' AND po_date>="'.clean($_POST['po_date_from']).'" ';
if ($_POST['po_date_to']!=='')$cond.= ' AND po_date<="'.clean($_POST['po_date_to']).'" ';
if ($_POST['po_content']!=='')$cond.=' AND po_id IN (SELECT po_con_po_id FROM purchase_content WHERE po_con_text LIKE ("%'.clean($_POST['po_content']).'%"))';
//END COND
if (isset($_POST['page'])) $page=$_POST['page'];
if (!is_numeric($page)) $page=1;

$query2='SELECT po_id FROM purchase WHERE 1';
$result2=$db->query($query2.$cond.$search);
$num = $result2->num_rows;
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;        
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort = ' ORDER BY po_no DESC';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT

$query= 'SELECT po_id, po_our_comp,our_name, po_status, po_ship_date, po_no, po_date, po_awb, po_note, po_total, '
        . 'cust_id, cust_short_name, po_stat_name,curr_symb '
        . 'FROM purchase '
        . 'LEFT JOIN purchase_status ON po_stat_id=po_status '
        . 'LEFT JOIN currency ON po_currency=curr_id, '
        . 'customers,our_companies '
        . 'WHERE po_supplier = cust_id AND our_companies.id=po_our_comp ';
$query.=$cond.$search.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);


?>
<div id="main_subheader">
<?php
if (!$result) exit('No purchase yet');
echo '<span style="float:left">Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_purchase_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_purchase_table('.($next_page).')" value="Next page"></span></span>';
?>
</div>
<div id="main_subbody">
    <div id="table_wrap">
    <table id="purchase_table" class="sort_table" width="100%" rules="columns"  border="1px" cellspacing = "0" cellpadding="2px">
        <thead onclick="table_sort(event,'purchase')">
            <th width="90px" keyword='po_no' <?php sort_class('po_no',$sort_field,$sort_type);?>>PO #</th>
            <th width="70px" keyword='po_date' <?php sort_class('po_date',$sort_field,$sort_type);?>>PO date</th>
            <th width="30px" keyword='po_status' <?php sort_class('po_status',$sort_field,$sort_type);?>>Status</th>
            <th width="100px" keyword='po_our_comp' <?php sort_class('po_our_comp',$sort_field,$sort_type);?>>Our company</th>
            <th width="100px" keyword='po_supplier' <?php sort_class('po_supplier',$sort_field,$sort_type);?>>Supplier</th>
            <th width="50px" ></th>
            <th width="70px" keyword='po_total' <?php sort_class('po_total',$sort_field,$sort_type);?>>Total</th>
            <th width="100px" keyword='po_ship_date' <?php sort_class('po_ship_date',$sort_field,$sort_type);?>>Ship. date</th>
            <th width="50px" keyword='po_awb' <?php sort_class('po_awb',$sort_field,$sort_type);?>>AWB</th>
            <th keyword='po_note' <?php sort_class('po_note',$sort_field,$sort_type);?>>Note</th>
        </thead>
        <tbody>
    <?php
    while($row = $result->fetch_assoc()){
        $on->comp_id = $row['po_our_comp'];
        $on->type = 'PO';
        $on->num = $row['po_no'];
        $on->id = $row['po_id'];
        echo '<tr><td class="num"><a href="#" onclick="view_link(\''.$on->get_order().'\')">'.$on->order.'</a></td>'
            . '<td>'.$row['po_date'].'</td>'
            . '<td class="',po_color_table($row['po_status']),'">'.$row['po_stat_name'].'</td>'
            . '<td>'.view_our_company_link($row['po_our_comp'],$row['our_name']).'</td>'
            . '<td>'.view_company_link($row['cust_short_name'],$row['cust_id']).'</td>'
            ,'<td><div class="float_div_holder" data-id="',$row['po_id'],'" onmouseenter="purchase_display_over(this)">Details</div></td>'
            . '<td class="align_right">'.number_format($row['po_total'],2,'.','').' '.$row['curr_symb'].'</td>'
            . '<td>'.$row['po_ship_date'].'</td>'
            . '<td>'.$row['po_awb'].'</td>'
            . '<td>'.$row['po_note'].'</td>'
            . '</tr>';
    }
    ?>
        </tbody>
    </table>
    </div>
</div>
<div id="main_subfooter"></div>
