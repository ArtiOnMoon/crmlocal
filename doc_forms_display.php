<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
?>
<table  class="sortable linked_table" width="100%" border="1px" align="center">
    <thead>
        <th>Name</th><th width=150>Uploaded</th><th width=100>Uploader</th><th width=100>Modified</th><th width=100>Download</th>
    </thead>
    <tbody align="center">
<?php 
$query= 'select docs_forms.*, users.full_name from docs_forms LEFT JOIN users ON uploader=username where cat=1';
if (isset($_POST['keyword']))$query.=' and (docs_forms.name like "%'.$_POST['keyword'].'%" or docs_forms.description like "%'.$_POST['keyword'].'%")';
$db =  db_connect();
$result=$db->query($query);
if (!$result){
    echo "Ошибка выполнения запроса: " . $db->error;
    exit();
}
while($row = $result->fetch_assoc()){
    echo '<tr elem_id="'.$row['id'].'">';
    echo '<td><strong>'.$row['name'].'</strong><br>'.$row['description'].'</td>'
        . '<td>'.$row['date'].'</td>'
        . '<td>'.$row['full_name'].'</td>'
        . '<td>'.$row['modified'].'</td>'
        . '<td><a href="#" class="knopka" onclick="download_docs_file(\'1\',\''.$row['id'].'\',\''.$row['file_name'].'\')">Download</a></td></tr>';
}
?>
</tbody>
</table>