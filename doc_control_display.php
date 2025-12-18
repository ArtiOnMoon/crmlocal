<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/doc_fns.php';

$limit=50;
//Page
if ($_POST['page']==='undefined') $page=1;
else $page=clean($_POST['page']);
if (!is_numeric($page) or $page<=0) $page=1;
//Page_END
//SEARCH
if (!isset($_POST['search']))$search='';
else{
    $search_field=clean($_POST['search']);
    $search=' AND (doc_name LIKE ("%'.$search_field.'%") OR doc_number LIKE ("%'.$search_field.'%"))';
}
//END SEARCH
//CONDITIONS
$cond='';
if ($_POST['doctype']!=='All')$cond.=' AND type="'.clean($_POST['doctype']).'"';
if ($_POST['comp']!=='0')$cond.=' AND our_company="'.clean($_POST['comp']).'"';
if ($_POST['archived']==='false')$cond.=' AND is_archive="0"';
else $cond.=' AND is_archive="1"';
//CONDITIONS END
//SORT
$sort_field=clean($_POST['sort_field']);
$sort_type=clean($_POST['sort_type']);
$sort=' ORDER BY expire_date';
if (isset($_POST['sort_field']) and $_POST['sort_field']!==''){
    $sort = ' ORDER BY '.$sort_field.' '.$sort_type;
}
//END SORT


$db =  db_connect();
$query= 'SELECT documents.id FROM documents WHERE 1';
$result=$db->query($query.$search.$cond);
if (!$result){
    echo "Ошибка выполнения запроса: " . $db->error;
    echo '<br>'.$query.$search.$cond;
    exit();
}
//CALC PAGES
$num = $result->num_rows;
if ($num<=0)exit ('No results');
$pages = ceil($num/$limit);
if ($page>$pages) $page=$pages;
$offset=$page*$limit-$limit;
$next_page=$page+1;
$previous_page=$page-1;
//CALC PAGES END
$time= time();
$query= 'SELECT documents.*, users.full_name FROM documents LEFT JOIN users ON incharge=uid WHERE 1';
$query.=$search.$cond.$sort.' LIMIT '.$limit.' OFFSET '.$offset;
$result=$db->query($query);
if (!$result){
    echo "Ошибка выполнения запроса: " . $db->error;
    echo '<br>'.$query;
    exit();
}
?>
<div id="main_subheader">
    <?php
    //УПРАВЛЕНИЕ СТРАНИЦАМИ
    echo 'Page <b>'.$page.'</b> of '.$pages.'<br>';
    //Previous page button
    echo '<span><input';
    if ($page<=1)echo ' disabled ';
    echo' type="button" onclick="show_docs_table('.$previous_page.')" value="Previous page"></span>';
    //Next page button
    echo '<span><input ';
    if ($page>=$pages)echo ' disabled ';
    echo 'type="button" onclick="show_docs_table('.($next_page).')" value="Next page"></span>';
    ?>
</div>
<div id="main_subbody">
 <table id="doc_control" class="sort_table" width="100%" border="1px">
    <thead align="center" onclick="table_sort(event,'doc_control')">
        <th width="30px" keyword='id' <?php sort_class('id',$sort_field,$sort_type);?>>ID</th>
        <th width="120px">Type</th>
        <th width="250px" keyword='doc_number' <?php sort_class('doc_number',$sort_field,$sort_type);?>>Number</th>
        <th keyword='doc_name' <?php sort_class('doc_name',$sort_field,$sort_type);?>>Description</th>
        <th width="100px" keyword='start_date' <?php sort_class('start_date',$sort_field,$sort_type);?>>Date</th>
        <th width="100px" keyword='expire_date' <?php sort_class('expire_date',$sort_field,$sort_type);?>>Expire date</th>
        <th width="50px">Days left</th>
        <th width="100px" keyword='incharge' <?php sort_class('incharge',$sort_field,$sort_type);?>>Incharge</th>
        <th width="100px">Download</th>
    </thead>
    <tbody>
    <?php 
    while($row = $result->fetch_assoc()){
        $days=floor(($row['expire_date']-$time) / (60*60*24));
        if ($days>$row['alarm']) $color='background: lime';
        else $color='background: yellow';
        if ($days<0) $color='background: #e53935';

        echo '<tr style="'.$color.'">';
        echo '<td>'.view_doc_link($row['id']).'</td>'
            . '<td>'.$row['type'].'</td>'
            . '<td>'.$row['doc_number'].'</td>'
            . '<td>'.$row['doc_name'].'</td>'
            . '<td>'.date('Y-m-d',$row['start_date']).'</td>'
            . '<td>'.date('Y-m-d',$row['expire_date']).'</td>'
            . '<td>'.$days.'</td>'
            . '<td>'.$row['full_name'].'</td>'
            . '<td>';
            if ($row['file']!=='*NO_FILE*')echo create_download_button($row['id'],$row['file']);
            echo '</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>