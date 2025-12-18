<?php
require_once 'functions/auth.php';
require_once 'functions/tasks_fns.php';
require_once 'functions/db.php';
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
require_once './ui/multiSelect/multiSelect.php';

require_once 'classes/Task.php';

startSession();
$comp_list = get_our_companies_list(1);
$user_list = get_user_list();

$task_id = clean($_POST['task_id']);
$db =  db_connect();
$task = new Task();
try{
    $task->load($db,$task_id);
    $task->set_read_state($db); // SET READ STATUS
} catch (Exception $e){
    exit($e->getMessage());
}

if ($task->from_user === $_SESSION['uid']){
    $disabled ='';
} else {
    $disabled = ' disabled';
}
?>
<div class="window_internal" style="width:1280px;height:80%;background: #EEE;">
    <form name="task_form" method="post" onsubmit="return tasks_change(this)" style="height:100%">
    <input type="hidden" name="id" value="<?php echo $task_id;?>">
    <input type="hidden" name="task_from" value="<?php echo $_SESSION['uid'];?>">
    <!-- Line 1-->
    <div class="task_grid">
        <div class="task_header">
            <div class="close_button_div"><a class="close_button" href="#" onclick="task_close(this);">&#10006;</a></div>
            <h2 align="center">Task # <?php echo $task->id; ?></h2>
        </div>
        <div class="task_subject task_block">
            <label class="task_label">Subject</label>
            <input <?php echo $disabled; ?> class="task_input" type="text" placeholder="Subject" maxlength="100" name="subject" value="<?php echo $task->subject; ?>">
        </div>
        <div class="task_order task_block">
            <label class="task_label">Related order <a href="#" onclick="view_link('<?php echo $task->task_order_no;?>')">Link</a></label>
                <?php // echo entity_type($task->task_order_type, 'name="task_order_type" class="task_select task_line"'.$disabled)?>
                <?php // echo select_our_company2($comp_list,$task->task_order_comp, 'class="task_select task_line" name="task_order_comp"'.$disabled,2);?>
                <input <?php echo $disabled; ?> class="task_input task_line" type="text" maxlength="12"  placeholder="Order #" name="task_order_no" value="<?php echo $task->task_order_no;?>">
        </div>
        <div class="task_priority task_block">
            <label class="task_label">Priority</label>
            <?php echo select_task_priority($task->priority,'class="task_select" name="priority"'.$disabled);?>
        </div>
        <div class="task_status task_block">
            <label class="task_label">Status</label>
            <?php echo select_task_status($task->status,'class="task_select" name="status"'.$disabled);?>
        </div>
        <!-- Line 2-->
        <div class="task_description task_block">
            <label class="task_label">Description</label>
            <textarea <?php echo $disabled; ?> class="task_textarea" placeholder="Subject" maxlength="1000" name="description"><?php echo $task->description;?></textarea>
        </div>
        <div class="task_set_by task_block">
            <label class="task_label">Set by</label>
            <?php select_user($task->from_user, 'class="task_select" readonly name="from_user"'.$disabled,'',1) ?>
        </div>
        <div class="task_set_to task_block">
            <label class="task_label">Set to</label>
            <?php echo multi_selector($user_list, $task->to_user,'multiselect', 'to_user');?>
            <?php //select_user($task->to_user, 'class="task_select" name="to_user"'.$disabled,'',1) ?>
        </div>
        <div class="task_start task_block">
            <label class="task_label">Start date</label>
            <input <?php echo $disabled; ?> class="task_input datepicker" type="text" maxlength="100" name="date" value="<?php echo $task->date;?>">
        </div>
        <div class="task_end task_block">
            <label class="task_label">Deadline</label>
            <input <?php echo $disabled; ?> class="task_input datepicker" type="text" maxlength="100" name="expire" value="<?php echo $task->expire;?>">
        </div>
        <!-- Line 3-->
        <div class="task_body task_block">
            <?php
            $subquery='SELECT task_history.*, full_name FROM task_history LEFT JOIN users ON user_id=uid WHERE task_id="'.$task_id.'" ORDER BY date DESC';
            $result = $db->query($subquery);
            while ($row2 = $result->fetch_assoc()){
                if ($row2['type'] == 1) {$class="task_message_t2";}
                else{
                    if ($row2['user_id'] == $_SESSION['uid']){ $class="task_message_t0";}
                    else {$class="task_message_t1";}
                }
            ?>
                <div class="task_message <?php echo $class;?>">
                    <div class="task_message_header">
                        <label class="task_message_user"><?php echo $row2['full_name'];?></label> |
                        <label class="task_message_date"><?php echo $row2['date'];?></label>
                    </div>
                    <div><pre class="task_text"><?php echo $row2['text'];?></pre></div>
                </div>
            <?php
            };
            ?>
        </div>
<!--        <div class="task_controls task_block">            
        </div>-->
        <div class="task_checklist task_block">
            <table width="100%"><tr>
                    <td class="align_center"><strong>Checklist</strong></td>
                    <td class="align_right"><input class="task_button" type="button" value="Add" onclick="task_add_checklist(this)"></td>
                </tr>
            </table>
            <div class="task_checklist_body">
                <?php 
                if (count($task->checklist) > 0){
                    foreach ($task->checklist as $key=>$value) {
                        checklist_draw($key, $value);
                    }
                }
                ?>
            </div>
        </div>
        <div class="task_reply task_block">
            <textarea class="task_textarea" name="text" placeholder="Type reply here" maxlength="1000"></textarea>
            <br>
            <input class="task_button" type="button" value="Add reply" onclick="task_add_reply(this)">
        </div>
        <div class="task_action task_block">
            <input class="task_button task_button2" type="button" value="Accept task" onclick="task_signal(<?php echo $task_id;?>, 1)">
            <input class="task_button task_button2" type="button" value="Report Completed" onclick="task_signal(<?php echo $task_id;?>, 2)">
        </div>
         <!-- Line 4-->
         <div class="task_footer task_block">
             <input class="nd_button_green" type="submit" value="Save">
        </div>
    </div>
    </form>
</div>
