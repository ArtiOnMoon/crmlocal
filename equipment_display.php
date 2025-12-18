<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
session_start();

if(check_access('acl_service', 1)) exit('Access denied.');
$limit=100;
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;

$cond='';
if (isset($_POST['category'])) $cond.= ' AND srv_eq_cat="'.clean($_POST['category']).'"';
if (isset($_POST['vessel_id'])) $cond.= ' AND vessel_equipment.vessel_id="'.clean($_POST['vessel_id']).'"';
if (isset($_POST['manuf_id'])) $cond.= ' AND mnf_id="'.clean($_POST['manuf_id']).'"';
if (isset($_POST['keyword'])) {
    $keyword=clean($_POST['keyword']);
    $cond.= ' AND (vessel_equipment.note LIKE ("%'.$keyword.'%") '
            . 'OR service_equipment.srv_eq_name LIKE ("%'.$keyword.'%") '
        . 'OR vessel_equipment.serial LIKE ("%'.$keyword.'%"))';
}

$db =  db_connect();
$query= 'SELECT vessel_equipment.id '
        . 'FROM vessels, vessel_equipment '
        . 'LEFT JOIN service_equipment ON nmnc_id=srv_eq_id '
        . 'LEFT JOIN manufacturers ON srv_eq_manuf=mnf_id '
        . 'LEFT JOIN stock_cats ON srv_eq_cat=stock_cats.id '
        . 'WHERE equip_deleted=0 AND vessel_equipment.vessel_id=vessels.vessel_id';
$query.=$cond;
$result2=$db->query($query);
$num = $result2->num_rows;
if ($num===0) exit('No results');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;

$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;
$query= 'SELECT vessel_equipment.*, vessels.vessel_name, vessels.imo, mnf_short_name, stock_cat_name, srv_eq_name '
        . 'FROM vessels, vessel_equipment '
        . 'LEFT JOIN service_equipment ON nmnc_id=srv_eq_id '
        . 'LEFT JOIN manufacturers ON srv_eq_manuf=mnf_id '
        . 'LEFT JOIN stock_cats ON srv_eq_cat=stock_cats.id '
        . 'WHERE equip_deleted=0 AND vessel_equipment.vessel_id=vessels.vessel_id';
$query.=$cond;
$query.=' ORDER BY id DESC LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);

if ($result->num_rows===0) exit('No results');
?>
<div id='main_subheader'>
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
if ($page<$pages)echo '<span><input type="button" onclick="show_equipment_table('.($next_page).')" value="Next page"></span>';
if ($page>1)echo '<span><input type="button" onclick="show_equipment_table('.$previous_page.')" value="Previous page"></span>';
echo '<span style="float:right">Records:<span id="rec1">'.$result->num_rows.'</span>&nbsp</span>';
//echo '<input type="button" onclick="go_to()" value="Go to"><input id="go_to" type="text" value="" size="3"><br>';
?>
</div>
<div id='main_subbody'>
    <table id="stock_table" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px" width="100%" style="position: relative;">
    <thead>
        <th width=50>ID</th><th width=250>Vessel</th><th width=100>Category</th><th width=150>Manufacturer</th>
        <th>Description</th><th width=100>Serial</th><th width=100>Check date</th><th width=100>Expire/replace</th>
        <th width=100>Days left</th><th>Note</th>
    </thead>
    <tbody>
<?php
$time= time();
while($row = $result->fetch_assoc()){
    if ($row['expire_date']!='' AND $row['expire_date']!='NULL')$days=(floor((strtotime($row['expire_date'])-$time) / (60*60*24)));
    else $days='';
    echo '<tr>';
    echo '<td>'.view_equipment_link($row['id'],$row['id']).'</td>'
        . '<td>'.view_vessel_link($row['vessel_name'], $row['vessel_id']).'</td>'
        . '<td>'.$row['stock_cat_name'].'</td>'
        . '<td>'.$row['mnf_short_name'].'</td>'
        . '<td>'.$row['srv_eq_name'].'</td>'
        . '<td>'.$row['serial'].'</td>'
        . '<td>'.$row['check_date'].'</td>'
        . '<td>'.$row['expire_date'].'</td>'
        . '<td>'.days_check($days).'</td>'
        . long_td_string($row['note'])
        . '</tr>';
}
?>
    </tbody>
    </table>
</div>';
    
    