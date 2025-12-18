<?php
require_once 'functions/auth.php';
require_once 'functions/tasks_fns.php';
require_once 'functions/db.php';
require_once 'functions/main.php';

startSession();
$time = time();
$order_list = get_order_types();
$user_list = get_user_list();

$for_me = clean($_POST['for_me']);
$from_me = clean($_POST['from_me']);
$hide_completed = clean($_POST['hide_completed']);
$search = clean($_POST['search']);
?>
<div id="main_subheader"></div>
<div id="main_subbody">
    <table id="task_table" class="sortable" width="100%" border="5px" cellspacing = "4px" cellpadding="4px">
        <thead>
            <th style="width:5em">ID</th>
            <th style="width:7em">Date</th>
            <th style="width:10em">Status</th>
            <th style="width:10em">New messages</th>
            <th style="width:10em">Order</th>
            <th style="width:12em">From</th>
            <th style="width:30em">To</th>
            <th>Subject</th>
            <th style="width:6em">Expire</th>
            <th style="width:10em"></th>
        </thead>
        <tbody>
    <?php
    if (($_POST['for_me']==='true') && ($_POST['from_me']==='true')) exit('Wrong parametr to/from.');

    $query= 'SELECT tasks.*, u1.full_name as fromname, task_stat_name, '
            . '(SELECT date FROM task_history WHERE task_history.task_id=tasks.id AND user_id!='.$_SESSION['uid'].' ORDER BY date DESC LIMIT 1) as last_message_date, ' // Колонка с датой последнего сообщения
            . '(SELECT read_date FROM tasks_read_status WHERE task_id=tasks.id AND user_id='.$_SESSION['uid'].' ) as read_date '
            . 'FROM tasks '
            . 'LEFT JOIN task_statuses ON status=task_stat_id '
            . 'LEFT JOIN users u1 ON from_user=u1.uid '
            . 'WHERE ';
    if ($for_me==='true') {
        $query.= 'JSON_CONTAINS(to_user, CONCAT(\'"\','.$_SESSION['uid'].',\'"\'))';
    }
    elseif ($from_me==='true') {
        $query.= 'from_user="'.$_SESSION['uid'].'"';    
    }
    else  {
        $query.= '(from_user="'.$_SESSION['uid'].'" '
                . 'OR JSON_CONTAINS(to_user, CONCAT(\'"\','.$_SESSION['uid'].',\'"\')) '
                . 'OR to_user is NULL)';
    }
    if ($hide_completed == 'true'){
        $query.= ' AND status IN (1,2,3,4)';
    }
    if ($search !== ''){
        $query.= ' AND (subject LIKE ("%'.$search.'%") OR task_order_no LIKE ("%'.$search.'%") OR description LIKE ("%'.$search.'%"))';
    }

    $db =  db_connect();
    $result=$db->query($query.' ORDER BY id DESC');
    if (!$result){
        echo "Ошибка выполнения запроса: " . $query;
        exit();
    }
    //echo $query;
    while($row = $result->fetch_assoc()){
        $users = json_decode($row['to_user']);
        $to_users = '';
        if (is_array($users)){
            foreach ($users as $key=>$value) {
              $to_users.=$user_list[$value].', ';
            }
            $to_users= substr($to_users, 0,-2);
        }
        if (($row['expire']!='') and ((strtotime($row['expire'])-$time) < 0)) $expire='class="blink"'; 
        else {$expire='';}
        //NEW MESSAGE CHECK
        if (strtotime($row['last_message_date']) > strtotime($row['read_date'])) { $new_message = '<span class="blink">New message!<span>'; }
        else { $new_message = 'No'; }
        
        $button = '';
        switch ($row['status']) { // New
            case "1" : {
                $stat='row_confirmed';
                $button = '<input type="button" class="nd_button_small nd_wide_element" onclick="task_signal('.$row['id'].', 1)" value="Accept task">';
                break;}
            case "2" : { // In work
                $stat='row_light_green';
                $button = '<input type="button" class="nd_button_small nd_wide_element" onclick="task_signal('.$row['id'].', 2)" value="Set completed">';
                break;
            }
            case "3" : $stat='row_yellow'; break; // Question
            case "4" : { // Complete
                $stat='row_complete';
                if($row['from_user'] === $_SESSION['uid']){
                    $button = '<input type="button" class="nd_button_small nd_wide_element" onclick="task_signal('.$row['id'].', 3)" value="Close task">';
                }
                break;
            }
            case "5" : $stat='row_grey'; break; // Canceled
            case "6" : $stat='row_red'; break; // Failed  
            case "7" : $stat='row_violet'; break; // Closed  
        }
        echo '<tr>';
        echo '<td><a href="#" onclick="task_view('.$row['id'].')">'.$row['id'].'</a></td>'
                . '<td>'.$row['date'].'</td>'
                . '<td class="'.$stat.'">'.$row['task_stat_name'].'</td>'
                . '<td>'.$new_message.'</td>'
                //. '<td '.$class.'>'.$row['num'].'</td>'
                . '<td><a href="#" onclick="view_link(\''.$row['task_order_no'].'\')">'.$row['task_order_no'].'</a></td>'
                . '<td>'.$row['fromname'].'</td>'
                . '<td>'.$to_users.'</td>'
                . '<td>'.$row['subject'].'</td>'
                . '<td '.$expire.'>'.$row['expire'].'</td>';
        echo '<td>'.$button.'</td>';
        echo'</tr>';
    }
    ?>
    </tbody></table>
</div>
<div id="main_subfooter"></div>