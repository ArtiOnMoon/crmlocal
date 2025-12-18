<?php
require_once '../functions/auth.php';
require_once '../functions/tasks_fns.php';
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/stock_fns.php';
require_once '../ui/multiSelect/multiSelect.php';
startSession();

$comp_list = get_our_companies_list(1);
$user_list = get_user_list();
?>
<form name="new_task_form" method="post" onsubmit="return tasks_new_form(this)" style="height:100%">
<div id="new_task" class="window_internal" style="width:1280px;height:80%;background: #EEE">
    <!-- Line 1-->
    <div class="task_grid">
        <div class="task_header">
            <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
            <h2 align="center">New Task</h2>
        </div>
        <div class="task_subject task_block">
            <label class="task_label">Subject</label>
            <input class="task_input" type="text" placeholder="Subject" maxlength="100" name="subject">
        </div>
        <div class="task_order task_block">
            <label class="task_label">Related order</label>
                <?php // echo entity_type(0, 'name="task_order_type" class="task_select task_line"')?>
                <?php // echo select_our_company2($comp_list,$row['stock_so_comp'],'class="task_select task_line" name="task_order_comp"',2);?>
                <input class="task_input task_line" type="text" maxlength="12"  placeholder="Order #" name="task_order_no" value="<?php echo $task->task_order_no;?>">
        </div>
        <div class="task_priority task_block">
            <label class="task_label">Priority</label>
            <?php echo select_task_priority();?>
        </div>
        <div class="task_status task_block">
            <label class="task_label">Status</label>
            <?php echo select_task_status();?>
        </div>
        <!-- Line 2-->
        <div class="task_description task_block">
            <label class="task_label">Description</label>
            <textarea class="task_textarea" placeholder="Description of the task" maxlength="1000" name="description"></textarea>
        </div>
        <div class="task_set_by task_block">
            <label class="task_label">Set by</label>
            <?php select_user($_SESSION['uid'], 'disabled class="task_select" name="from_user"') ?>
        </div>
        <div class="task_set_to task_block">
            <label class="task_label">Set to</label>
            <?php // select_user('', 'class="task_select" name="to_user"','',1) ?>
            <?php echo multi_selector($user_list, [],'multiselect', 'to_user');?>
        </div>
        <div class="task_start task_block">
            <label class="task_label">Start date</label>
            <input class="task_input datepicker" type="text" maxlength="100" name="date" value="<?php echo date("Y-m-d");?>">
        </div>
        <div class="task_end task_block">
            <label class="task_label">Deadline</label>
            <input class="task_input datepicker" type="text" maxlength="100" name="expire" value="<?php echo date("Y-m-d");?>">
        </div>
        <!-- Line 3-->
        <div class="task_body task_block">
            Here will be task history
        </div>
        <div class="task_reply task_block">
            
        </div>
<!--        <div class="task_controls task_block">
            controls
        </div>-->
         <!-- Line 4-->
         <div class="task_footer task_block">
             <input type="submit" class="nd_button_green" onsubmit="return tasks_new_form(this)" value="Save">
        </div>
    </div>
</div>
</form>
