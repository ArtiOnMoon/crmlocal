<?php
require_once 'functions/fns.php';
do_page_header('Questions','Questions');
startSession();
security ();
if (!access_check([],[],1)) exit ('Access denied');
?>
<div id="side_menu">
    <input type="button" value="New question" onclick="display('new_question')">
    <select onchange="filtr(this.value)">
        <option selected>All</option>
        <option>published</option>
        <option>accepted</option>
        <option>declined</option>
    </select>
</div>
<div id="wrap" onclick="cancel()"></div>
<div class="hidden" id="new_question">
    <h2 align="center">Add new question</h2>
    <form action="add_question.php" method="POST">
        <table width="90%" align="center">
        <tr><td>
        <textarea name="task_text" maxlength="250" cols="100" rows="3"></textarea>
        </td></tr>
        </table>
    <div align="right" width="100%" style="padding: 10px">
    <input type="submit" value="Add question" >
    <input type="button" value="Close" onclick="cancel()"> 
    </div>
    </form>
</div>
<div id="main_div_menu">
    <table id="question_table" class="sortable" width="100%" border="1px" cellspacing = "0" cellpadding="2px">
    <thead>
        <th width=30>ID</th><th width=150>User name</th><th width=100>Date</th><th>Question</th><th>Answer</th><th width=100>Status</th><th width=100>Action</th>
    </thead>
    <tbody>
<?php
$query= 'select * from questions, users WHERE user=username ORDER BY name';
$db =  db_connect();
$result=$db->query($query);
if (!$result){
    echo "Ошибка выполнения запроса: " . $db->error;
    exit();
}
while($row = $result->fetch_assoc()){
    switch ($row['status']) {
        case "accepted" :
            $color='background: greenyellow'; break;
        case "declined" :
            $color='background: red'; break;
        case "published" :
            $color='background: yellow'; break;
}
    echo '<tr style="'.$color.'">';
    echo '<td>'.$row['id'].'</td>'
            . '<td>'.$row['full_name'].'</td>'
            . '<td>'.$row['date'].'</td>'
            . '<td style="word-break: break-all;">'.$row['text'].'</td>'
            . '<td style="word-break: break-all;"><b>'.$row['answer'].'</b></td>'
            . '<td>'.$row['status'].'</td><td>';
            if (access_check([''],['vk','kvv',$row['user']]))echo'<a href="/view_question.php?question_id='.$row['id'].'">Change</a>';
            echo'</td></tr>';
}
?>
</tbody></table>
<div>
<script>
    function filtr(value){
    var tab = document.getElementById('question_table');
    var max = tab.rows.length;
    for (var i=1; i<max; i++){
        var t = tab.rows[i];
        if (value=='All') {t.style.display = ''; continue;}
        if (t.cells[5].innerHTML==value) {t.style.display = '';} else
        t.style.display = 'none';
    }
}    
</script>
<script type="text/javascript" src="java/java_func.js"></script>