<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/service.php';
require_once 'functions/auth.php';
require_once 'classes/Order_name_engine.php';
require_once 'classes/Adm.php';

startSession();
security();

$limit=50;

$customer = clean($_POST['customer']);
$comp_id = clean($_POST['comp_id']);
$search = clean($_POST['search']);
$incharge = clean($_POST['incharge']);
$status = clean($_POST['status']);

$cond = '';
if ($customer !== '' AND $customer !== "1"){
    $cond.= ' AND customer="'.$customer.'"';
}
if ($comp_id !== ''){
    $cond.= ' AND comp_id="'.$comp_id.'"';
}
if ($incharge !== ''){
    $cond.= ' AND incharge="'.$incharge.'"';
}
if ($status !== ''){
    $cond.= ' AND status="'.$status.'"';
}
if ($search !== ''){
    $cond.= ' AND (title LIKE "%'.$search.'%" OR note LIKE "%'.$search.'%")';
}

if ($_POST['page']==='undefined') {$page=$_SESSION['service_page'];}
else {$page=clean($_POST['page']);}
if (!is_numeric($page) or $page<=0) { $page=1; }

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);
$on->type = 'AD';

$adm = new Adm();

$query='SELECT administrative.*, u1.full_name, u2.full_name as user_modified,cust_short_name FROM administrative '
        . 'LEFT JOIN users u1 ON u1.uid=incharge '
        . 'LEFT JOIN users u2 ON u2.uid=administrative.modified_by '
        . 'LEFT JOIN customers ON customer=cust_id '
        . 'WHERE is_deleted=0'.$cond;
//SEARCH be here

$result=$db->query($query.$cond);
if (!$result) {exit($db->error);}
if ($result->num_rows === 0) {exit('No results');}

$num = $result->num_rows;
$pages = ceil($num/$limit);
if ($page>$pages) {
    $page=$pages;
}
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

//УПРАВЛЕНИЕ СТРАНИЦАМИ
?>
<div id="main_subheader">
    <?php
    echo '<div id="main_table_controls">'
    . 'Page <b>'.$page.'</b> of '.$pages.'<br>';
    //Previous page button
    echo '<input';
    if ($page<=1)echo ' disabled ';
    echo' type="button" onclick="adm_display('.$previous_page.')" value="Previous page">';
    //Next page button
    echo '<input ';
    if ($page>=$pages)echo ' disabled ';
    echo 'type="button" onclick="adm_display('.($next_page).')" value="Next page"></div>';
    ?>
</div>

<div id="main_subbody">
    <table class="sort_table">
        <thead>
            <th>Order</th>
            <th>Date</th>
            <th>Status</th>
            <th>Customer</th>
            <th>Title</th>
            <th>Note</th>
            <th>Incharge</th>
            <th>Modified</th>
        </thead>
        <tbody>
<?php
    while($row = $result->fetch_assoc()){
        $on->comp_id = $row['comp_id'];
        $on->num = $row['number'];
        $on->id = $row['id'];
        ?>
        <tr>
            <td><a href="#" onclick="view_link('<?php echo $on->get_order();?>')"><?php echo $on->get_order();?></a></td>
            <td><?php echo $row['date'];?></td>
            <td class="<?php echo $adm->color_table($row['status']); ?>"><strong><?php echo $adm->status_list[$row['status']]; ?></strong></td>
            <td><?php echo view_company_link($row['cust_short_name'],$row['customer']);?></td>
            <td><strong><?php echo $row['title'];?></strong></td>
            <td><i><?php echo $row['note'];?></i></td>
            <td><strong><?php echo $row['full_name'];?></strong></td>
            <td>Last modified:<?php echo $row['modified'];?> by <?php echo $row['user_modified']; ?></td>
        </tr>
    <?php    
    }
?>
</tbody>
</table>