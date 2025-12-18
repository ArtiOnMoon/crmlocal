<?php
require_once 'functions/db.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
session_start();
security();
if(check_access('acl_cust', 1)) exit('Access denied.');
do_page_header('Customers search');


$db =  db_connect();
$query= 'select * from customers where deleted=0 and cust_id>1';
if(isset($_POST['name']))$query.= ' and (cust_short_name LIKE "%'.clean($_POST['name']).'%" OR cust_full_name LIKE "%'.clean($_POST['name']).'%")';
if(isset($_POST['country']))$query.= ' and country = "'.clean($_POST['country']).'"';
if(isset($_POST['customer_type']))$query.= ' and cat LIKE "%'.clean($_POST['customer_type']).'%"';
if(isset($_POST['email']))$query.= ' and email LIKE "%'.clean($_POST['email']).'%"';
if(isset($_POST['website']))$query.= ' and website LIKE "%'.clean($_POST['website']).'%"';
if(isset($_POST['phone']))$query.= ' and (contact_phone LIKE "%'.clean($_POST['phone']).'%" OR add_phone LIKE "%'.clean($_POST['phone']).'%")';
if(isset($_POST['bank']))$query.= ' and bank_details LIKE "%'.clean($_POST['bank']).'%"';
if(isset($_POST['addr']))$query.= ' and (address LIKE "%'.clean($_POST['addr']).'%" OR InvoicingAddress LIKE "%'.clean($_POST['addr']).'%")';
if(isset($_POST['bank']))$query.= ' and bank_details LIKE "%'.clean($_POST['bank']).'%"';
if(isset($_POST['note']))$query.= ' and note LIKE "%'.clean($_POST['note']).'%"';
$result=$db->query($query);
?>
<div id="main_div_menu">
<table class="sortable" width="100%" border="1px" cellspacing = "0" cellpadding="2px">
<thead>
<th width="30px">Country</th>
<th axis="num" width="50">ID</th>
<th width="100">Company name</th>
<th width="100">Category</th>
<th width="100">Address</th>
<th width="100">E-mail</th>
<th width="200">Website</th>
<th width="100">Contact phone</th>
<th width="100">Additional phone</th>
<th width="100">invoicing Address</th>
<th width="100">Discount (%)</th>
<th width="100">Payment terms</th>
<th width="100">Credit limit</th>
</thead>
<tbody>
<?php
while($row = $result->fetch_assoc()){
    $color='';
    if ($row['customer_status']==='red') $color='red';
    elseif ($row['customer_status']==='yellow') $color='yellow';
    elseif ($row['customer_status']==='green') $color='greenyellow';
    echo '<tr style="background:'.$color.'">'
            . '<td>'.$row['country']
            . '</td><td class="num">'.view_company_link($row['cust_id'],$row['cust_id'])
            . '</td><td>'.view_company_link($row['cust_short_name'],$row['cust_id'])
            . '</td><td>'.$row['cat']
            . '</td><td>'.$row['address']
            . '</td><td>'.'<a href="mailto:'.$row['email'].'">'.$row['email'].'</a>'
            . '</td><td>'.web_site_link($row['website'])
            . '</td><td>'.$row['contact_phone']
            . '</td><td>'.$row['add_phone']
            . '</td><td>'.$row['InvoicingAddress']
            . '</td><td>'.$row['discount']
            . '</td><td>'.$row['payment_terms']
            . '</td><td>'.$row['credit_limit']
            . '</tr>';
}
echo '</tbody></table></div>';