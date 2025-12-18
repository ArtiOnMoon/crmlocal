<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/contracts_fns.php';
session_start();
//if(check_access('acl_sales', 1)) exit('Access denied.');
//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort = ' ORDER BY contract_id DESC ';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
//SEARCH
if ($_POST['contract_search']=='')$search='';
else{
    $search_field=clean($_POST['contract_search']);
    $search=' AND (contract_num LIKE ("%'.$search_field.'%") '
            . 'OR contract_our_num LIKE ("%'.$search_field.'%") '
            . 'OR contract_descr LIKE ("%'.$search_field.'%") '
            . 'OR contract_note LIKE ("%'.$search_field.'%"))';
}
//END SEARCH
$limit=100;

if (isset($_POST['page'])) $page=clean($_POST['page']);
else $page=$_SESSION['sales_page'];
if (!is_numeric($page) or $page<=0) $page=1;
$cond='';

$db =  db_connect();
$query='SELECT contract_id FROM contracts WHERE contract_deleted=0';
//CONDITIONS
//$status= $_POST['stat'];
$our_company= $_POST['our_company'];
//$date_start= clean($_POST['date_start']);
//$date_end= clean($_POST['date_end']);
$contract_customer= clean($_POST['contract_customer']);
//if ($status!='All') $cond.=' AND sales_status="'.$status.'"';
if ($our_company!='') $cond.=' AND contract_our_comp="'.$our_company.'"';
//if ($date_start!='') $cond.=' AND sales_date>="'.$date_start.'"';
//if ($date_end!='') $cond.=' AND sales_date<="'.$date_end.'"';
if ($contract_customer!='' AND $contract_customer!='1') $cond.=' AND contract_customer="'.$contract_customer.'"';

$result2=$db->query($query.$cond.$search);
$num = $result2->num_rows;
if ($num<1) exit('No result');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;        
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

$query= 'SELECT contracts.*, customers.cust_short_name, currency.curr_name,currency.curr_symb, our_companies.id AS our_id,our_name, contract_statuses.contract_status_text, full_name '
        . 'FROM contracts '
        . 'LEFT JOIN customers ON contract_customer=cust_id '
        . 'LEFT JOIN our_companies ON our_companies.id=contract_our_comp '
        . 'LEFT JOIN contract_statuses ON contract_status=contract_status_id '
        . 'LEFT JOIN currency ON currency.curr_id=contract_currency '
        . 'LEFT JOIN users ON contract_incharge=users.uid '
        . 'WHERE contract_deleted=0 ';        
$query.=$cond.$search.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);
echo $db->error;

//УПРАВЛЕНИЕ СТРАНИЦАМИ
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
<div id="table_wrap" >
<table id="sales_table" class="sort_table" width="100%" >
<thead onclick="table_sort(event,'sales')">
<th keyword='contract_id' <?php sort_class('contract_id',$sort_field,$sort_type);?>>ID</th>
<th keyword='contract_num' <?php sort_class('sales_stat_name',$sort_field,$sort_type);?> >Number</th>
<th keyword='contract_date' <?php sort_class('contract_date',$sort_field,$sort_type);?>>Date</th>
<th keyword='contract_status' <?php sort_class('contract_status',$sort_field,$sort_type);?>>Status</th>
<th keyword='contract_type' <?php sort_class('contract_type',$sort_field,$sort_type);?>>Type</th>
<th keyword='our_name' <?php sort_class('our_name',$sort_field,$sort_type);?>>Our company</th>
<th keyword='cust_short_name' <?php sort_class('cust_short_name',$sort_field,$sort_type);?>>Customer</th>
<th keyword='contract_currency' <?php sort_class('contract_currency',$sort_field,$sort_type);?>>Currency</th>
<th keyword='contract_amount' <?php sort_class('contract_amount',$sort_field,$sort_type);?>>Amount</th>
<th keyword='contract_expire' <?php sort_class('contract_expire',$sort_field,$sort_type);?>>Expire</th>
<th keyword='contract_incharge' <?php sort_class('contract_incharge',$sort_field,$sort_type);?>>Incharge</th>
<th keyword='contract_note' <?php sort_class('sales_payment',$sort_field,$sort_type);?>>Note</th>
</thead><tbody>
<?php
while($row = $result->fetch_assoc()){
    echo '<tr><td class="num">',
        contract_view($row['contract_id']),'</td>'
        , '<td>',contract_view($row['contract_id'],contract_number($row['contract_our_num'],$row['contract_num'],$row['contract_num_flag'])),'</td>'
        , '<td>',$row['contract_date'],'</td>'
        , '<td>',$row['contract_status_text'],'</td>'
        , '<td>',$row['contract_type'],'</td>'
        , '<td>',$row['our_name'],'</td>'
        , '<td>',view_company_link($row['cust_short_name'],$row['contract_customer']),'</td>'
        , '<td>',$row['curr_name'],'</td>'
        , '<td class="align_right">',number_format($row['contract_amount'],2,'.',''),' ',$row['curr_symb'],'</td>'
        , '<td>',$row['contract_expire'],'</td>'
        , '<td>',$row['full_name'],'</td>'
        , long_td_string($row['contract_note'],30)
        , '</tr>';
    }
?>
</tbody></table></div>