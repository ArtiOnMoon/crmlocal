<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
startSession();
security();
if(check_access('acl_service', 1)) exit('Access denied.');
$limit=100;
$page=1;
//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort = ' ORDER BY vessel_id DESC ';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}

if (isset($_POST['page'])) $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;
$query='SELECT vessel_id FROM vessels WHERE vessel_deleted=0 AND vessel_id>1';
$cond='';
if (isset($_POST['search'])){
    $search=clean($_POST['search']);
    $cond.= ' AND (imo LIKE ("%'.$search.'%") OR vessel_name LIKE ("%'.$search.'%") OR vessel_note LIKE ("%'.$search.'%"))';
}
if (isset($_POST['owner'])) $cond.= ' AND company="'.clean($_POST['owner']).'"';
if (isset($_POST['agent'])) $cond.= ' AND ship_manager="'.clean($_POST['agent']).'"';
if (isset($_POST['operator'])) $cond.= ' AND ship_operator="'.clean($_POST['operator']).'"';
$db =  db_connect();
$result2=$db->query($query.$cond);
$num = $result2->num_rows;

if ($num===0) exit('Nothing found');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;        
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;

$query= 'SELECT vessels.*, c1.cust_short_name as owner, c1.cust_id, c2.cust_short_name as manager, c2.cust_id, c3.cust_short_name as operator, c3.cust_id, countries.code '
        . 'FROM vessels LEFT JOIN customers c1 ON company=c1.cust_id LEFT JOIN customers c2 ON c2.cust_id=ship_manager Left JOIN customers c3 ON c3.cust_id=ship_operator '
        . 'LEFT JOIN countries ON flag=id '
        . 'WHERE vessel_deleted=0 AND vessel_id>1';
$query.=$cond;
$query.=$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);
?>

<div id="main_subheader">
    
<?php
echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
//Previous page button
echo '<span><input';
if ($page<=1)echo ' disabled ';
echo' type="button" onclick="show_vessel_table('.$previous_page.')" value="Previous page"></span>';
//Next page button
echo '<span><input ';
if ($page>=$pages)echo ' disabled ';
echo 'type="button" onclick="show_vessel_table('.($next_page).')" value="Next page"></span>';
echo '<span style="float:right">Records:<span id="rec1">'.$num.'</span>&nbsp</span>';
?>
</div>
<div id="main_subbody">
    <div id="table_wrap">
        <table id="vessels_table" class="sort_table" width="100%">
        <thead onclick="table_sort(event,'vessels')">
            <th keyword='imo' <?php sort_class('imo',$sort_field,$sort_type);?> width="50">IMO</th>
            <th keyword='code' <?php sort_class('code',$sort_field,$sort_type);?> width="30">Flag</th>
            <th keyword='vessel_name' <?php sort_class('vessel_name',$sort_field,$sort_type);?> width="vessel_name">Vessel name</th>
            <th keyword='class_societies' <?php sort_class('class_societies',$sort_field,$sort_type);?> width="100">Class</th>
            <th keyword='owner' <?php sort_class('owner',$sort_field,$sort_type);?> width="150">Owner</th>
            <th keyword='manager' <?php sort_class('manager',$sort_field,$sort_type);?> width="150">Manager</th>
            <th keyword='operator' <?php sort_class('operator',$sort_field,$sort_type);?> width="150">Operator</th>
            <th keyword='vessel_mail_1' <?php sort_class('vessel_mail_1',$sort_field,$sort_type);?> width="150">E-mail</th>
            <th keyword='vessel_mob_1' <?php sort_class('vessel_mob_1',$sort_field,$sort_type);?> width="100">Phone</th>
            <th keyword='vessel_inmarsat_1' <?php sort_class('vessel_inmarsat_1',$sort_field,$sort_type);?> width="100">Inmarsat</th>
        </thead>
        <tbody>
        <?php
        while($row = $result->fetch_assoc()){
            echo '<tr>'
            , '<td>',view_vessel_link($row['imo'], $row['vessel_id']),'</td>'
            , '<td>',$row['code'],'</td>'
            , '<td>',view_vessel_link($row['vessel_name'], $row['vessel_id']),'</td>'
            , '<td>',$row['class_societies'],'</td> '
            , '<td>',view_company_link($row['owner'], $row['company']),'</td>'
            , '<td>',view_company_link($row['manager'], $row['ship_manager']),'</td>'
            , '<td>',view_company_link($row['operator'], $row['ship_operator']),'</td>'
            , '<td><a href="',$row['vessel_mail_1'],'">',$row['vessel_mail_1'],'</a>';
            if ($row['vessel_mail_2']!='' AND $row['vessel_mail_2']!='NULL')echo '<br><a href="',$row['vessel_mail_2'],'">',$row['vessel_mail_2'],'</a></td>';
            echo '<td>',$row['vessel_mob_1'];
            if ($row['vessel_mob_2']!='' AND $row['vessel_mob_2']!='NULL')echo '<br>',$row['vessel_mob_2'],'</td>';
            echo '<td>',$row['vessel_inmarsat_1'];
            if ($row['vessel_inmarsat_2']!='' AND $row['vessel_inmarsat_2']!='NULL')echo '<br>',$row['vessel_inmarsat_2'],'</td></tr>';
        }
        ?></tbody>
        </table>
    </div>
</div>
<div id="main_subfooter"></div>