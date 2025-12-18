<?php

function view_task_link ($id){
    $var='<a href="view_task.php?id='.$id.'">'.$id.'</a>';
    return $var;
}

function select_task_priority($current="", $headers='class="task_select" name="priority"'){
    $out = '<select '.$headers.'>';
    $list = [1 => 'Normal', 0 => 'Not urgent', 3 => 'Urgent'];
    foreach ($list as $key => $value){
        $out .= '<option value="'.$key.'"';
        if ($current == $key) $out .= ' selected';
        $out .= '>'.$value.'</option>';
    }
    $out .= '</select>';
    return $out;
}

function select_task_status($current="", $headers='class="task_select" name="status"'){
    $out = '<select '.$headers.'>';
    $db = db_connect();
    $query = 'SELECT * FROM task_statuses';
    $result = $db->query($query);
    while ($row = $result->fetch_assoc()){
        $out .= '<option value="'.$row['task_stat_id'].'"';
        if ($current == $row['task_stat_id']) $out .= ' selected';
        $out .= '>'.$row['task_stat_name'].'</option>';
    }
    $out .= '</select>';
    return $out;
}

function checklist_draw($id,$data){
    $checked = ($data['checked']==1) ? ' checked' : '';
    echo '<div class="task_checklist_container" data-id="'.$id.'" data-text="'.$data['text'].'" data-order="'.$data['checklist_order'].'">
            <div class="task_checklist_switch">
                <input type="checkbox" class="task_checklist_checkbox"'.$checked.'> 
            </div>
            <div class="task_checklist_text">'.$data['text'].'</div>
            <div class="task_checklist_controls">
                <div class="task_checklist_button task_checklist_up" onclick="checklist_move_up(this)"></div>
                <div class="task_checklist_button task_checklist_del" onclick="checklist_delete(this)"></div>
                <div class="task_checklist_button task_checklist_down" onclick="checklist_move_down(this)"></div>
            </div>
        </div>';
}