<?php
require_once 'Base_class.php';

class Task extends Base_class{
    public $id;
    public $to_user;
    public $from_user;
    public $date;
    public $expire;
    public $subject;
    public $description;
    public $status;
    public $priority;
    public $comment;
//    public $task_order_type;
//    public $task_order_comp;
    public $task_order_no;
    
    private $text_fields = [
        'id'=>'ID',
        'to_user'=>'To user',
        'from_user'=>'From user',
        'date'=>'Start date',
        'expire'=>'End date',
        'subject'=>'Subject',
        'description'=>'Description',
        'status'=>'Status',
        'priority'=>'Priority',
        'comment'=>'comment',
//        'task_order_type'=>'Order type',
//        'task_order_comp'=>'Order company',
        'task_order_no'=>'Order number',];
    
    private $status_text = [1 => 'New', 2 => 'In work', 3 =>'Question', 4 => 'Complete', 5 => 'Canceled', 6 => 'Failed', 7 => 'Closed'];
    
    private $priority_text = [0 => 'Not urgent', 1 => 'Normal', 3 =>'Urgent'];

    private $to_user_text = [];
    
    function __construct (array $values=[]) {
        $this->to_user = is_array($values['to_user']) ? $values['to_user']: NULL;
        $this->from_user = $values['from_user'] ?? $_SESSION['uid'];
        $this->date = $values['date'] ? $values['date']:date('Y-m-d');
        $this->expire = $values['expire'] ? $values['expire']:NULL;
        $this->subject =  $values['subject'] ? $values['subject']:'';
        $this->description = $values['description'] ? $values['description']:'';
        $this->status = $values['status'] ? $values['status']:0;
        $this->priority = $values['priority'] ? $values['priority']:0;
        $this->comment = $values['comment'] ? $values['comment']:'';
//        $this->task_order_type = $values['task_order_type'] ? $values['task_order_type']:NULL;
//        $this->task_order_comp = $values['task_order_comp'] ? $values['task_order_comp']:NULL;
        $this->task_order_no = $values['task_order_no'] ? $values['task_order_no']:NULL;
        
//        $this->to_user_text = get_user_list();
//        $this->from_user_text = $this->to_user_text;
    }
        
    function load($db_connection,$id){
        $query='SELECT * FROM tasks WHERE id='.$id;
        $result = $db_connection->query($query);
        if (!$result){
            throw new Exception($db_connection->error);
        }
        if ($result->num_rows != 1){
            throw new Exception('No results.');
        }
        while ($row = $result->fetch_assoc()){
            $this->id = $row['id'];
            $this->to_user = is_null($row['to_user']) ? null: json_decode($row['to_user']);
            $this->from_user = $row['from_user'];
            $this->date = $row['date'];
            $this->expire = $row['expire'];
            $this->subject = $row['subject'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->priority = $row['priority'];
            $this->comment = $row['comment'];
//            $this->task_order_type = $row['task_order_type'];
//            $this->task_order_comp = $row['task_order_comp'];
            $this->task_order_no = $row['task_order_no'];
        }
        $this -> checklist_get($db_connection);
    }
    
    function init ($db_connection){
        $query = 'INSERT INTO tasks SET '
                . 'to_user = '.($this->to_user ? '\''.json_encode($this->to_user).'\'' : 'NULL').','
                . 'from_user = '.($this->from_user ?? 'NULL').','
                . 'date = '.($this->date ? '"'.$this->date.'",' : 'NULL,')
                . 'expire = '.($this->expire ? '"'.$this->expire.'",' : 'NULL,')
                . 'subject = "'.$db_connection->real_escape_string($this->subject).'",'
                . 'description = "'.$db_connection->real_escape_string($this->description).'",'
                . 'status = '.$this->status.','
                . 'priority = '.$this->priority.','
                . 'comment = "'.$db_connection->real_escape_string($this->comment).'",'
//                . 'task_order_type = '.$db_connection->real_escape_string($this->task_order_type ? $this->task_order_type : 'NULL').','
//                . 'task_order_comp = '.$db_connection->real_escape_string($this->task_order_comp ? $this->task_order_comp : 'NULL').','
                . 'task_order_no = "'.$db_connection->real_escape_string($this->task_order_no ? $this->task_order_no : 'NULL').'" ';
        if (!$db_connection->query($query)){
//            throw new Exception($db_connection->error);
            throw new Exception($db_connection->error.$query);
        }
        $this->id = $db_connection->insert_id;
        
        //SENT "created" messgae to history
        $query2 = 'INSERT INTO task_history SET '
                . 'task_id = '.$this->id.','
                . 'type = 1,'
                . 'user_id = '.$_SESSION['uid'].','
                . 'text = "Task created."';
        if (!$db_connection->query($query2)){
//            throw new Exception($db_connection->error);
            throw new Exception($db_connection->error);
        }
    }
    
    function save($db_connection){
        $query='UPDATE tasks SET '
                . 'to_user = '.($this->to_user ? '\''.json_encode($this->to_user).'\',' : 'NULL,')
                . 'from_user = '.($this->from_user ?? 'NULL').','
                . 'date = '.($this->date ? '"'.$this->date.'",' : 'NULL,')
                . 'expire = '.($this->expire ? '"'.$this->expire.'",' : 'NULL,')
                . 'subject = "'.$db_connection->real_escape_string($this->subject).'",'
                . 'description = "'.$db_connection->real_escape_string($this->description).'",'
                . 'status = '.$this->status.','
                . 'priority = '.$db_connection->real_escape_string($this->priority).','
                . 'comment = "'.$db_connection->real_escape_string($this->comment).'",'
//                . 'task_order_type = '.$db_connection->real_escape_string($this->task_order_type ?? 'NULL').','
//                . 'task_order_comp = '.$db_connection->real_escape_string($this->task_order_comp ?? 'NULL').','
                . 'task_order_no = "'.$db_connection->real_escape_string($this->task_order_no ??  'NULL').'" '
                . 'WHERE id='.$this->id;
        if (!$db_connection->query($query)){
            throw new Exception('order_no:'.$this->task_order_no.$db_connection->error.$query);
        }
        return true;
    }
    
    function update_report ($db_connection, array $values){
        $query = 'INSERT INTO task_history SET '
                . 'task_id = '.$this->id.','
                . 'type = 1,'
                . 'user_id = '.$_SESSION['uid'].',';
        $message = 'Task updated:<br />';
        foreach ($values as $key=>$value){
            if (property_exists($this, $key.'_text')){
                $message.= $this->text_fields[$key].': from ['.$this->{$key.'_text'}[$this->{$key}].'] to ['.$this->{$key.'_text'}[$value].']<br />';
            } else{
                $message.= $this->text_fields[$key].': from ['.$this->$key.'] to ['.$value.']<br />';
            }
        }
        $query.='text = "'.$message.'"';
        if (!$db_connection->query($query)){
            throw new Exception($db_connection->error);
        }
    }
    
    function update($db_connection,array $values){
        // clean fields that not changed
        foreach ($values as $key=>$value){
            if ($this->$key == $value  || !property_exists($this, $key)){
                unset($values[$key]);
            }
        }
        if ($values===[]){
//            throw new Exception('No changes have been made');
            return;
        }
        $this->update_report($db_connection, $values); //sent update report to db
        $this->set($values);
        $this->save($db_connection); //save new values
    }
    
    function checklist_new ($db_connection,$checklist_order,$text,$checked){
        $query = 'INSERT into task_checklist SET '
                . 'task_id = '.$this->id.','
                . 'checklist_order = '.$checklist_order.','
                . 'text = "'.$text.'",'
                . 'checked ='.($checked ? 1 : 0);
        if (!$db_connection->query($query)){
            throw new Exception('Checklist init:'.$db_connection->error);
        }
    }
    
    function checklist_delete ($db_connection, array $values){
        foreach ($values as $key=>$value) {
            $query = 'DELETE FROM task_checklist WHERE id ='.$key;
            if (!$db_connection->query($query)){
                throw new Exception('checklist_delete: '.$query);
            }
        }
    }
    
    function checklist_update ($db_connection, $id,$checklist_order,$text,$checked){
        $query = 'UPDATE task_checklist SET '
                . 'task_id = '.$this->id.','
                . 'checklist_order = '.$checklist_order.','
                . 'text = "'.$text.'",'
                . 'checked ='.($checked ? 1 : 0).' '
                . 'WHERE id='.$id;
        if (!$db_connection->query($query)){
            throw new Exception('Checklist update: '.$db_connection->error);
        }
        $this ->checklist_report_updated($db_connection, $id,$checklist_order,$text,$checked);
    }
    
    function checklist_get($db_connection){
        $query = 'SELECT * FROM task_checklist WHERE task_id = '.$this->id.' ORDER BY checklist_order';
        if (!$result = $db_connection->query($query)){
            throw new Exception($db_connection->error);
        }
        $data = [];
        while ($row = $result->fetch_assoc()){
            $data[$row['id']] = [
                'checklist_order' => $row['checklist_order'], 
                'text' => $row['text'], 
                'checked' => $row['checked']];
        }
        $this->checklist = $data;
    }
    
    function checklist_init($db_connection, $values){
        foreach ($values as $value) {
            if (array_key_exists($value[0],$this->checklist)){ // Поле с таким ID уже есть
                if ($this->checklist[$value[0]]['checked'] != $value[3]){
//                    echo $value[3],' = ',$this->checklist[$value[0]]['checked'];
                    $this->checklist_update($db_connection, $value[0], $value[1], $value[2], $value[3]);
                }
                unset($this->checklist[$value[0]]); //Удалить те что уже были
            }
            else{
                $this->checklist_new ($db_connection, $value[1], $value[2], $value[3]);
                $this->checklist_report_new($db_connection, $value[2], $value[3]);
            }
        }
        if(!empty($this->checklist)){
            $this -> checklist_delete($db_connection, $this->checklist);
            $this -> checklist_report_deleted($db_connection,$this->checklist);
        }
    }
    
    function checklist_report_deleted($db_connection, array $values){
        $query = 'INSERT INTO task_history SET '
                . 'task_id = '.$this->id.','
                . 'type = 1,'
                . 'user_id = '.$_SESSION['uid'].',';
        $message = 'Checklist elements has been deleted:<br />';
        foreach ($values as $value){
            $message.= '['.$value['text'].']<br />';
        }
        $query.='text = "'.$message.'"';
        if (!$db_connection->query($query)){
            throw new Exception($db_connection->error);
        }
    }
    
    function checklist_report_updated($db_connection, $id,$checklist_order,$text,$checked){
        $checked_text = $checked ? 'checked' : 'unchecked';
        $query = 'INSERT INTO task_history SET '
                . 'task_id = '.$this->id.','
                . 'type = 1,'
                . 'user_id = '.$_SESSION['uid'].',';
        $message = 'Checklist elements has been updated:<br />';
        $message.= '['.$text.'] status -> '.$checked_text.'<br />';
        $query.='text = "'.$message.'"';
        if (!$db_connection->query($query)){
            throw new Exception($db_connection->error);
        }
    }
    
    function checklist_report_new($db_connection, $text,$checked){
        $checked_text = $checked ? 'checked' : 'unchecked';
        $query = 'INSERT INTO task_history SET '
                . 'task_id = '.$this->id.','
                . 'type = 1,'
                . 'user_id = '.$_SESSION['uid'].',';
        $message = 'Checklist element has been created:<br />';
        $message.= '['.$text.'] status -> '.$checked_text.'<br />';
        $query.='text = "'.$message.'"';
        if (!$db_connection->query($query)){
            throw new Exception($db_connection->error);
        }
    }
    
    function set_read_state($db_connection){
        $query ='INSERT INTO tasks_read_status SET '
                . 'task_id = "'.$this->id.'", '
                . 'user_id = "'.$_SESSION['uid'].'",'
                . 'read_date = CURRENT_TIMESTAMP() '
                . 'ON DUPLICATE KEY UPDATE read_date = CURRENT_TIMESTAMP()';
        if (!$db_connection->query($query)){
            throw new Exception($db_connection->error);
        }
    }
}