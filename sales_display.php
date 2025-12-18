<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/sales_fns.php';
require_once 'classes/Order_name_engine.php';

session_start();

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);
$on->type = 'SL';

//if ($_POST['sales_our_comp']=='')  exit('Please, select company.');
//TOTAL PRE-CALCULATIONS
$data= get_currency_list();
$total_calcs=$data;
foreach ($total_calcs as $key => $value) {
    $total_calcs[$key]=array('total'=>0, 'paid'=>0);
}
//End total

//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort = ' ORDER BY sales_no DESC ';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
//SEARCH
if ($_POST['search']=='')$search='';
else{
    $search_field=clean($_POST['search']);
    try {
        if ($on ->resolve_order($search_field)){
            $search=' AND (sales_no = ("'.$on->num.'") '
            . 'OR sales_cust_po LIKE ("%'.$search_field.'%") OR sales_awb LIKE ("%'.$search_field.'%") OR sales_request LIKE ("%'.$search_field.'%") '
            . 'OR sales_invoice LIKE ("%'.$search_field.'%") OR sales_descr LIKE ("%'.$search_field.'%"))';
        }
        
    } catch (Exception $ex) {
        $search=' AND (sales_no LIKE ("%'.$search_field.'%") '
            . 'OR sales_cust_po LIKE ("%'.$search_field.'%") OR sales_awb LIKE ("%'.$search_field.'%") OR sales_request LIKE ("%'.$search_field.'%") '
            . 'OR sales_invoice LIKE ("%'.$search_field.'%") OR sales_descr LIKE ("%'.$search_field.'%"))';
    }
}
//END SEARCH
$limit=100;

if (isset($_POST['page'])) $page=clean($_POST['page']);
else $page=$_SESSION['sales_page'];
if (!is_numeric($page) or $page<=0) $page=1;
$_SESSION['sales_page']=$page;
$cond='';
$query='SELECT sales_id FROM sales WHERE sales_deleted=0';
//CONDITIONS
$status= $_POST['stat'];
$sales_our_comp= $_POST['sales_our_comp'];
$date_start= clean($_POST['date_start']);
$date_end= clean($_POST['date_end']);
$sales_customer= clean($_POST['sales_customer']);
$sales_content = clean($_POST['sales_content']);
$sales_vessel = clean($_POST['sales_vessel']);

//ACCESS CHECK
if(check_access('acl_sales', 1, $sales_our_comp)) exit('Access denied.');

if ($status!='All') $cond.=' AND sales_status="'.$status.'"';
if ($sales_our_comp!='') $cond.=' AND sales_our_comp="'.$sales_our_comp.'"';
if ($date_start!='') $cond.=' AND sales_date>="'.$date_start.'"';
if ($date_end!='') $cond.=' AND sales_date<="'.$date_end.'"';
if ($sales_customer!='' AND $sales_customer!='1') $cond.=' AND sales_customer="'.$sales_customer.'"';
if ($sales_content!='')$cond.=' AND sales_id IN (SELECT scont_sale_id FROM sales_content WHERE scont_text LIKE ("%'.$sales_content.'%"))';
if ($sales_vessel!='')$cond.=' AND sales_vessel_id="'.$sales_vessel.'"';

$result2=$db->query($query.$cond.$search);
$num = $result2->num_rows;
if ($num<1) exit('No result');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;        
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

$query= 'SELECT sales.*, vessel_name, customers.cust_short_name, currency.curr_name,currency.curr_symb, our_companies.id AS our_id, customers.client_of, sales_statuses.sales_stat_name, '
        . 'sales_total_cfm-sales_payment AS sales_balance '
        . 'FROM sales '
        . 'LEFT JOIN customers ON sales_customer=cust_id '
        . 'LEFT JOIN vessels ON sales_vessel_id=vessel_id '
        . 'LEFT JOIN our_companies ON our_companies.id=sales_our_comp '
        . 'LEFT JOIN sales_statuses ON sales_status=sales_stat_id '
        . 'LEFT JOIN currency ON currency.curr_id=sales.sales_currency '
        . 'WHERE sales_deleted=0 ';        
$query.=$cond.$search.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);

//УПРАВЛЕНИЕ СТРАНИЦАМИ
?>
<div id="main_subheader">
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_sales_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_sales_table('.($next_page).')" value="Next page"></span><br>';
?>
</div>
<div id="main_subbody">
<div id="table_wrap" style="width:100%;float:left;">
<table id="sales_table" class="sort_table" width="100%" >
<thead onclick="table_sort(event,'sales')">
<th keyword='sales_no' <?php sort_class('sales_no',$sort_field,$sort_type);?>>ID</th>
<th keyword='sales_stat_name' <?php sort_class('sales_stat_name',$sort_field,$sort_type);?> >Status</th>
<th keyword='sales_date' <?php sort_class('sales_date',$sort_field,$sort_type);?>>Date</th>
<th width="40px"></th>
<th keyword='cust_short_name' <?php sort_class('cust_short_name',$sort_field,$sort_type);?>>Customer</th>
<!--<th keyword='sale_shipment_dew' <?php sort_class('sale_shipment_dew',$sort_field,$sort_type);?>>Ship untill</th>-->
<th keyword='vessel_name' <?php sort_class('vessel_name',$sort_field,$sort_type);?>>Vessel</th>
<th keyword='sales_cust_po' <?php sort_class('sales_cust_po',$sort_field,$sort_type);?>>Customer's PO</th>
<th keyword='sales_awb' <?php sort_class('sales_awb',$sort_field,$sort_type);?>>AWB</th>
<th keyword='sales_invoice' <?php sort_class('sales_invoice',$sort_field,$sort_type);?>>Invoice №</th>
<!--<th keyword='sales_po_num' <?php sort_class('sales_po_num',$sort_field,$sort_type);?>>Our PO</th>-->
<th keyword='sales_total' <?php sort_class('sales_total',$sort_field,$sort_type);?>>Amount</th>
<th keyword='sales_payment' <?php sort_class('sales_payment',$sort_field,$sort_type);?>>Payment</th>
<th keyword='sales_balance' <?php sort_class('sales_balance',$sort_field,$sort_type);?>>Balance</th>
<th keyword='sales_descr' <?php sort_class('sales_descr',$sort_field,$sort_type);?>>Note</th>
</thead><tbody>
<?php
while($row = $result->fetch_assoc()){
    $on->comp_id = $row['sales_our_comp'];
    $on->num = $row['sales_no'];
    $on->id = $row['sales_id'];
    echo '<tr><td class="num"><a href="#" onclick="view_link(\''.$on->get_order().'\')">',$on->order,'</a>'
//        view_sales_link($row['sales_our_comp'],$row['sales_no'],$on->get_order()),'</td>'
        , '<td class="',sales_color_table($row['sales_status'],$row['sale_shipment_dew']),'">',$row['sales_stat_name'],'</td>'
        , '<td>',$row['sales_date'],'</td>'
        ,'<td><div class="float_div_holder" data-id="',$row['sales_id'],'" onmouseenter="sales_display_over(this)">Details</div></td>'
        , '<td>',view_company_link($row['cust_short_name'],$row['sales_customer']),'</td>'
//        , '<td>',$row['sale_shipment_dew'],'</td>'
        ,'<td>',view_vessel_link($row['vessel_name'],$row['sales_vessel_id']),'</td>'
        , '<td>',$row['sales_cust_po'],'</td>'
        ,long_td_string($row['sales_awb'],20)
        , '<td>',$row['sales_invoice'],'</td>'
        //, '<td>',view_po('',$row['sales_po_comp'],$row['sales_po_num']),'</td>'
        , '<td class="align_right">',number_format($row['sales_total_cfm'],2,'.',''),' ',$row['curr_symb'],'</td>'
        , '<td class="align_right">',number_format($row['sales_payment'],2,'.',''),' ',$row['curr_symb'],'</td>'
        , '<td class="align_right">',number_format($row['sales_balance'],2,'.',''),' ',$row['curr_symb'],'</td>'
        , long_td_string($row['sales_descr'],30)
        , '</tr>';
    //TOTAL CALCS
    if ($row['sales_status']==='2' || $row['sales_status']==='3' || $row['sales_status']==='4'){
    $total_calcs[$row['sales_currency']]['total']+=$row['sales_total_cfm'];
    $total_calcs[$row['sales_currency']]['paid']+=$row['sales_payment'];
    }
}
?>
</tbody></table></div>
</div>
<!--<div style="width:20%;float:right;box-sizing:border-box;border:1px solid black;text-align: center;">
<h3>Payment control</h3>
<table border="1px" width="100%">
    <thead><th>Currency</th><th>Amount</th><th>Payment</th><th>Balance</th></thead>
    //<?php
//foreach ($total_calcs as $key => $value) {
//    echo '<tr><td>'.$data[$key].'</td><td>'.number_format($total_calcs[$key]['total'],2,'.','').'</td><td>'.number_format($total_calcs[$key]['paid'],2,'.','').'</td><td><strong>'.number_format(($total_calcs[$key]['total']-$total_calcs[$key]['paid']),2,'.','').'</strong></td></tr>';
//}
//?>
</table>
</div>-->