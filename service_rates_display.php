<?php
require_once 'functions/db.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';

$cond='';
if (isset($_POST['currency'])) $cond.=' AND rate_currency="'.clean($_POST['currency']).'"';
if (isset($_POST['search'])) $cond.=' AND rate_name LIKE ("%'.clean($_POST['search']).'%")';
if (isset($_POST['rate_our_comp'])) $rate_our_comp='rate_our_comp="'.clean($_POST['rate_our_comp']).'"'; ELSE $rate_our_comp='1';
$db =  db_connect();
?>
<div id="table_wrap">
    <table id="service_rates" class="sort_table" width="100%" rules="columns"  border="1px" cellspacing = "0" cellpadding="2px">
        <thead>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Currency</th>
            <th>Action</th>
        </thead>
        <tbody>
<?php
$query='SELECT * FROM service_rates_cat WHERE '.$rate_our_comp.' ORDER BY rate_our_comp,rate_order' ;
$result=$db->query($query);
    while($row = $result->fetch_assoc()){
        $query2='SELECT service_rates.*, curr_name FROM '
                . 'service_rates LEFT JOIN currency ON curr_id=rate_currency, service_rates_cat '
                . 'WHERE rate_cat=rate_cat_id AND rate_cat="'.$row['rate_cat_id'].'"'.$cond;
        $result2=$db->query($query2);
        echo '<tr><td colspan=4><strong>'.$row['rate_cat_name'].'</strong></td>'
                . '<td><a href="/view_rate_cat.php?rate_cat_id='.$row['rate_cat_id'].'">Edit category</a></td><tr>';
        while($row2=$result2->fetch_assoc()){
            echo '<tr>'
        . '<td class="num"><a href="/view_rate.php?rate_id='.$row2['rate_id'].'">'.$row2['rate_id'].'</td>'
        . '<td>'.$row2['rate_name'].'</td>'
        . '<td>'.$row2['rate_price'].'</td>'
        . '<td>'.$row2['curr_name'].'</td>'
        . '<td><a class="knopka" href="/view_rate.php?rate_id='.$row2['rate_id'].'">Change rate</a></td>'
        . '</tr>';
        }
    }
?> 
        </tbody>
    </table>
</div>