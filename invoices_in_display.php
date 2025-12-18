<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/invoice_fns.php';
startSession();

$day_in_sec=86400;
//CONDITIONS
$invoice_customer=clean($_POST['invoice_customer']);
$invoice_status=clean($_POST['invoice_status']);
$invoice_our_comp=clean($_POST['invoice_our_comp']);
$invoice_currency=clean($_POST['invoice_currency']);
$invoice_date_from=clean($_POST['invoice_date_from']);
$invoice_date_to=clean($_POST['invoice_date_to']);
$cond='';
if ($invoice_customer!='' AND $invoice_customer!='1')$cond.=' AND invoice_customer="'.$invoice_customer.'"';
if ($invoice_status!='' AND $invoice_status!='0')$cond.=' AND invoice_status="'.$invoice_status.'"';
if ($invoice_our_comp!='' AND $invoice_our_comp!='0')$cond.=' AND invoice_our_comp="'.$invoice_our_comp.'"';
if ($invoice_currency!='' AND $invoice_currency!='0')$cond.=' AND invoice_currency="'.$invoice_currency.'"';
if ($invoice_date_from!='' AND $invoice_date_from!='0')$cond.=' AND invoice_date>="'.$invoice_date_from.'"';
if ($invoice_date_to!='' AND $invoice_date_to!='0')$cond.=' AND invoice_date<="'.$invoice_date_to.'"';
//END CONDITIONS

if(check_access('acl_invoices', 1, $invoice_our_comp)) {exit('Access denied.');} //Access check

//SEARCH
$search='';
$invoice_search=clean($_POST['invoice_search']);
if ($invoice_search!='')$search.=' AND (invoice_num LIKE "%'.$invoice_search.'%")';
//END SEARCH

//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort = ' ORDER BY invoice_id DESC ';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT

$limit=50;
if (isset($_POST['page'])) $page=$_POST['page'];
if (!is_numeric($page)) $page=1;
$db =  db_connect();
$query2='SELECT invoice_id FROM invoices WHERE invoice_type=1 AND invoice_deleted=0'.$cond.$search;
if(!$result2=$db->query($query2))exit ($db->error);
$num = $result2->num_rows;
If ($num===0) exit("No records.");

//Работа со страницами
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;        
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;
$query= 'SELECT invoices.*,customers.cust_id,customers.cust_short_name,invoices_statuses.inv_stat_name, our_companies.our_name,curr_name, '
        . '(SELECT SUM(pay_amount)FROM invoices_payments WHERE pay_inv_id=invoices.invoice_id) AS payment_received '
        . 'FROM invoices '
        . 'LEFT JOIN customers ON invoice_customer=cust_id '
        . 'LEFT JOIN invoices_statuses ON inv_stat_id=invoice_status '
        . 'LEFT JOIN our_companies ON our_companies.id=invoice_our_comp '
        . 'LEFT JOIN currency ON curr_id=invoice_currency '
        . 'WHERE invoice_type=1 AND invoice_deleted=0';
$query.=$cond.$search.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
if(!$result=$db->query($query))exit($db->error);
if ($result->num_rows===0) exit ('Nothing found.');
?>
<div id="main_subheader">
<?php
echo '<span style="float:left">Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_invoice_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_invoice_table('.($next_page).')" value="Next page"></span></span>';
?>
</div>
<div id="main_subbody">
    <table id="invoice_table" class="sort_table" width="100%" rules="columns"  border="1px" cellspacing = "0" cellpadding="2px">
    <thead onclick="table_sort(event,'invoices')">
        <th width="50px">ID</th>
        <th width="30px"></th>
        <th width="100px" keyword='invoice_num' <?php sort_class('invoice_num',$sort_field,$sort_type);?>>Invoice number</th>
        <th width="80px" keyword='invoice_status' <?php sort_class('invoice_status',$sort_field,$sort_type);?>>Status</th>
        <th width="80px" keyword='invoice_date' <?php sort_class('invoice_date',$sort_field,$sort_type);?>>Invoice date</th>
        <th width="30px" keyword='invoice_our_comp' <?php sort_class('invoice_our_comp',$sort_field,$sort_type);?>>Our company</th>
        <th keyword='invoice_customer' <?php sort_class('invoice_customer',$sort_field,$sort_type);?>>Customer</th>
        <th width="80px">Days</th>
        <th width="50px">Currency</th>
        <th width="100px">Amount</th>
        <th width="100px">Payments</th>
        <th width="100px">Balance</th>
        <th>Note</th>
    </thead><tbody>
    <?php
    while($row = $result->fetch_assoc()){
        if ($row['invoice_is_cn']==='0')$type='INV'; else $type='CN';
        $due_date= intdiv(time()-strtotime($row['invoice_date']),$day_in_sec);
        echo '<tr>'
        , '<td>',view_invoice_by_id($row['invoice_id'],$row['invoice_id']),'</td>'
        , '<td>',$type,'</td>'
        , '<td class="num">',view_invoice($row['invoice_our_comp'],$row['invoice_num']),'</td>'
        , '<td class="',invoice_color_table($row['invoice_status']),'">',$row['inv_stat_name'],'</td>'
        , '<td>',$row['invoice_date'],'</td>'
        , '<td>',$row['our_name'],'</td>'
        , '<td>',view_company_link($row['cust_short_name'],$row['cust_id']),'</td>'
        , '<td>',$due_date,'</td>'
        , '<td>',$row['curr_name'],'</td>'
        , '<td class="align_right">',number_format($row['invoice_total'],2,'.',' '),'</td>'
        , '<td class="align_right">',number_format($row['payment_received'],2,'.',' '),'</td>'
        , '<td class="align_right">',number_format(($row['invoice_total']-$row['payment_received']),2,'.',' '),'</td>'
        , '<td>',$row['invoice_note'],'</td>'
        , '</tr>';
    }
    ?>
    </tbody></table>
</div>
<div id="main_subfooter"></div>