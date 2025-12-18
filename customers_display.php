<?php
require_once 'functions/db.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
session_start();
security();
if(check_access('acl_cust', 1)) exit('Access denied.');
//
$limit=50;
$search=clean($_POST['search']);
//
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;
//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort = ' ORDER BY cust_id DESC ';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
//Поле поиска
$cond='';
if (isset($_POST['country'])) {
    if (isset($_POST['exclude_country'])) $cond=' and country!="'.clean($_POST['country']).'"';
    else  $cond=' and country="'.clean($_POST['country']).'"';
}
if ($search!=='')
{
    $cond.=' and (cust_full_name like ("%'.$search.'%") OR cust_short_name like ("%'.$search.'%")'
            . ' OR cust_id in (SELECT customer_id FROM customers_contacts where name like ("%'.$search.'%")))';
}
$category='';
if (isset($_POST['is_mnfr']))$category.= 'is_mnfr="1" or ';
if (isset($_POST['is_mngr']))$category.= 'is_mngr="1" or ';
if (isset($_POST['is_sppl']))$category.= 'is_sppl="1" or ';
if (isset($_POST['is_serv']))$category.= 'is_serv="1" or ';
if (isset($_POST['is_agnt']))$category.= 'is_agnt="1" or ';
if (isset($_POST['is_ownr']))$category.= 'is_ownr="1" or ';
if (isset($_POST['is_optr']))$category.= 'is_optr="1" or ';
if (isset($_POST['is_fchk']))$category.= 'is_fchk="1" or ';
if ($category!==''){
    $category=substr($category, 0, strlen($category)-4);
    $cond.=' and ('.$category.')';
}
$db =  db_connect();
$result2=$db->query('select cust_id from customers where deleted=0'.$cond);
$num = $result2->num_rows;
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;
$_SESSION['cust_page']=$page; //номер страницы в переменную сессии

$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;
$query= 'SELECT customers.*, countries.code FROM customers LEFT JOIN countries ON country=id WHERE deleted=0'.$cond.' and cust_id>1 '.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);
if (!$result) exit('No customers found.');
?>
<div id="main_subheader">
<?php
 //Создание таблицы 
echo 'Page<b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_customers_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_customers_table('.($next_page).')" value="Next page"></span>';

//echo '<input type="button" onclick="go_to()" value="Go to"><input id="go_to" type="text" value="" size="3"><br>';
?>
</div>
<div id="main_subbody">
    <div id="table_wrap">
        <table id="customers_table" class="sort_table" width="100%" border="0px" cellpadding="2px">
        <thead onclick="table_sort(event,'customers')">
        <th width="10px"><input type='checkbox' id='main_checkbox' onchange='check_all_checkboxes(this)'></th>
            <th width="30px" keyword='country' <?php sort_class('country',$sort_field,$sort_type);?>>Country</th>
            <th keyword='cust_id' width="50" <?php sort_class('cust_id',$sort_field,$sort_type);?>>ID</th>
            <th keyword='cust_short_name'  <?php sort_class('cust_short_name',$sort_field,$sort_type);?>>Company name</th>
            <th keyword='email'  <?php sort_class('email',$sort_field,$sort_type);?>>E-mail</th>
            <th keyword='website' width="200"  <?php sort_class('website',$sort_field,$sort_type);?>>Website</th>
            <th keyword='contact_phone' width="200"  <?php sort_class('contact_phone',$sort_field,$sort_type);?>>Contact phone</th>
            <th keyword='add_phone' width="200"  <?php sort_class('additioal_phone',$sort_field,$sort_type);?>>Additional phone</th>
            <th keyword='discount' width="50"  <?php sort_class('discount',$sort_field,$sort_type);?>>Discount (%)</th>
            <th keyword='payment_terms' width="100"  <?php sort_class('payment_terms',$sort_field,$sort_type);?>>Payment terms</th>
            <th keyword='credit_limit'width="50"  <?php sort_class('credit_limit',$sort_field,$sort_type);?>>Credit limit</th>
        </thead>
        <tbody onclick="click_catch(event)">
        <?php
        while($row = $result->fetch_assoc()){
            $class='row_white';
            if ($row['is_fchk'])$class='row_grey';
            elseif ($row['customer_status']==='red') $class="row_red";
            elseif ($row['customer_status']==='yellow') $class='row_yellow';
            elseif ($row['customer_status']==='green') $class='row_green';
            echo '<tr class="'.$class.'">'
                    ,'<td><input type="checkbox" class="table_checkbox" data-email="',$row['email'],'" value="',$row['cust_id'],'"></td>'
                    , '<td>'.$row['code']
                    , '</td><td><a href="#" data-id="',$row['cust_id'],'">',$row['cust_id'],'</a>'
                    , '</td><td><a href="#" data-id="',$row['cust_id'],'">',$row['cust_short_name'],'</a>'
                    , '</td><td>','<a href="mailto:',$row['email'],'">',$row['email'],'</a>';
            if ($row['email2']!='') echo ', <a href="mailto:',$row['email2'],'">',$row['email2'],'</a>';
            if ($row['email3']!='') echo ', <a href="mailto:',$row['email2'],'">',$row['email2'],'</a>';
            echo '</td><td>',web_site_link($row['website'])
                    , '</td><td>',$row['contact_phone']
                    , '</td><td>',$row['add_phone']
                    , '</td><td>',$row['discount']
                    , '</td><td>',$row['payment_terms']
                    , '</td><td>',$row['credit_limit']
                    , '</tr>';
        }
        ?>
        </tbody>
        </table>
    </div>
</div>
<div id="main_subfooter">
    <button class="button" onclick="send_email()">Send e-mail</button>
</div>