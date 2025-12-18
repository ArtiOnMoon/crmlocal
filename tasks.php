<?php
require_once 'functions/auth.php';
require_once 'functions/main.php';
require_once 'functions/tasks_fns.php';
require_once 'functions/db.php';

startSession();
security();

$flag=0;

$page_title = 'Tasks';
include 'header.php';

?>
<div id="side_menu">
    <input type="button" value="New task" onclick="task_new()">
    <?php select_task_status('', 1); //echo $flag;?>
    <span class="span_for_checkbox" onchange="load_tasks()"><label>All<input type="radio" name="radio" id="all" <?php if($flag === 0) echo'checked';?> ></label></span>
    <span class="span_for_checkbox" onchange="load_tasks()"><label>For me<input type="radio" name="radio" id="for_me" <?php if($flag === 1) echo'checked';?> ></label></span>
    <span class="span_for_checkbox" onchange="load_tasks()"><label>From me<input type="radio" name="radio" id="from_me" <?php if($flag=== 2) echo'checked';?> ></label></span>
    <input onchange="load_tasks()" id = "task_search_field" type="search" placeholder="Subject, order">
    
    <label><input type="checkbox" id="tasks_hide_completed" checked onchange="load_tasks()"> Hide closed</label>
</div>
<main id="main_div_menu"></main>

<?php include 'footer.php';?>
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_tasks.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script>
document.addEventListener('domcontentloaded', load_tasks());
$( ".datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd'
    });
function filtr(value){
    var tab = document.getElementById('task_table');
    var max = tab.rows.length;
    for (var i=1; i<max; i++){
        var t = tab.rows[i];
        if (value=='All') {t.style.display = ''; continue;}
        if (t.cells[7].innerHTML==value) {t.style.display = '';} else
        t.style.display = 'none';
    }
}
</script>
<link rel="stylesheet" type="text/css" href="css/tasks.css">