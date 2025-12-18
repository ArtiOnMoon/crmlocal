<?php
require_once 'functions/db.php';
require_once 'functions/stock_fns.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
session_start();
if(check_access('acl_stock', 1)) exit('Access denied.');

$class=clean($_POST['class']);
$manufacturer=clean($_POST['manufacturer']);
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$keyword=clean($_POST['keyword']);
$nmnc_stock_for_sort=clean($_POST['nmnc_stock_for_sort']);
$nmnc_hide_0=clean($_POST['nmnc_hide_0']);

if ($manufacturer=='All')$manufacturer='';

$limit=100;
if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;

$db =  db_connect();
$query='SELECT stnmc_id FROM stock_nmnc WHERE stnmc_deleted=0';
$condition='';
$condition2='';
if ($class!=='All') $condition.=' and stnmc_type="'.$class.'"';
if ($manufacturer!='') $condition.=' and stnmc_manuf="'.$manufacturer.'"';
if ($nmnc_hide_0==='true') $condition2.=' and num > 0';
if ($keyword!='') $condition.=' and (stnmc_pn like ("%'.$keyword.'%") '
        . 'OR stnmc_id = ("'.$keyword.'") '
        . 'OR stnmc_note like ("%'.$keyword.'%") '
        . 'OR stnmc_type_model like ("%'.$keyword.'%") '
        . 'OR stnmc_descr like ("%'.$keyword.'%"))';
$query.=$condition;
//echo $query;
$result2=$db->query($query);
$num = $result2->num_rows;
if ($num<=0) exit('No results');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;
//$_SESSION['stock_page']=$page; //номер страницы в переменную сессии

$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

//SORT
$sort = ' ORDER BY stnmc_id DESC ';
if ($sort_field!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT
$subquery = '(SELECT count(stock_id) as num FROM stock_new WHERE stock_nmnc_id=stnmc_id AND stock_status=1';
if ($nmnc_stock_for_sort!='All') $subquery.=' and stock_stock_id="'.$nmnc_stock_for_sort.'"';
$subquery.=')';
$query='select stock_nmnc.*,manufacturers.mnf_short_name, stock_cat_name, curr_name,curr_symb, '
        . $subquery.' as num '
        . 'FROM stock_nmnc '
        . 'LEFT JOIN manufacturers ON stnmc_manuf=mnf_id '
        . 'LEFT JOIN stock_cats ON stnmc_type=stock_cats.id '
        . 'LEFT JOIN currency ON stnmc_curr=curr_id '
        . 'HAVING stnmc_deleted=0';
$query.=$condition.$condition2.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
//echo $query;
$result=$db->query($query);
if ($result->num_rows===0) exit('No results');
?>
<div id="main_subheader">
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_nmnc_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_nmnc_table('.($next_page).')" value="Next page"></span>';
echo '<span style="float:right">Records:<span id="rec1">'.$num.'</span>&nbsp</span>';
?>
</div>
<div id="main_subbody">
<div id="table_wrap">
    <form action="stock_multi_edit.php" method="POST" target='_blank'>
    <table id="stock_table" class="sort_table" border="1px" cellspacing = "0" cellpadding="2px" width="100%">
        <thead id="thead" onclick="table_sort(event,'stock_nmnc')">
            <th width="10px"><input type='checkbox' id='main_checkbox' onchange='check_all_checkboxes(this)'></th>
            <th width="50" keyword="stnmc_id" <?php sort_class('stnmc_id',$sort_field,$sort_type);?>>ID</th>
            <th width="75" keyword="stnmc_type" <?php sort_class('stnmc_type',$sort_field,$sort_type);?>>Category</th>
            <th width="150" keyword="cust_short_name" <?php sort_class('cust_short_name',$sort_field,$sort_type);?>>Manufacturer</th>
            <th width="100" keyword="stnmc_pn" <?php sort_class('stnmc_pn',$sort_field,$sort_type);?>>P/N</th>
            <th keyword="stnmc_type_model" <?php sort_class('stnmc_type_model',$sort_field,$sort_type);?>>Type/model</th>
            <th keyword="stnmc_descr" <?php sort_class('stnmc_descr',$sort_field,$sort_type);?>>Description</th>
            <th width='70'>End user</th>
            <th width='75'>Ru End user</th>
            <th width='75' keyword="num" <?php sort_class('num',$sort_field,$sort_type);?>>In stock</th>
        </thead>
        <tbody>
        <?php
        while($row = $result->fetch_assoc()){
            //if ($row['num']==='0') continue;
            $price=$row['stnmc_price']*(1-$row['stnmc_discount']/100)*$sales_gloobal_multiplier;
            $ru_price=$row['stnmc_price']*(1-$row['stnmc_discount']/100)*$sales_gloobal_multiplier*$row['stnmc_russia_mult'];
            echo '<tr ',color_table($row['status'],$row['cond']),'><td><input type="checkbox" class="table_checkbox" name="edit[]" value="',$row['id'],'"></td>';
            echo '<td><a href="#" onclick="nmnc_view(',(int)$row['stnmc_id'],')">',$row['stnmc_id'],'</td>'
                    , '<td>',$row['stock_cat_name'],'</td>'
                    , '<td><strong>',$row['mnf_short_name'],'</strong></td>'
                    , '<td>',$row['stnmc_pn'],'</td>'
                    , '<td>',$row['stnmc_type_model'],'</td>'
                    , '<td>',$row['stnmc_descr'],'</td>'
                    , '<td class="align_right">',' ',number_format($price,2,'.',''),' ',$row['curr_symb'],'</td>'
                    , '<td class="align_right">',' ',number_format($ru_price,2,'.',''),' ',$row['curr_symb'],'</td>'
                    , '<td>',$row['num'],'</td>'
                    , '</tr>';
        }
        ?>
        </tbody>
    </table>
    </form>
</div>

</div>