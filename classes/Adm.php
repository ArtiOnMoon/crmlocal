<?php
require_once 'Base_class.php';

class Adm extends Base_class{
    public $id;
    Public $number;
    public $comp_id;
    public $date;
    public $status;
    public $customer;
    public $title;
    public $note;
    public $incharge;
    public $modified_by;
    public $modified;
    public $is_deleted;
    
    public $status_list = [1 => 'New', 2 => 'In work', 3 => 'Complete', 4 => 'Canceled', 5 => 'Failed'];
    public $users_list = [];
    
    function __construct() {
        $this->id = null;
        $this->number = null;
        $this->comp_id = null;
        $this->date = date('Y-m-d');
        $this->title = '';
        $this->note = '';
        $this->incharge = null;
        $this->is_deleted = 0;  
    }
    
    function load($db_connection, $id){ // Load from DB
        $query='SELECT * FROM administrative WHERE id="'.$id.'"';
        $result = $db_connection->query($query);
        if (!$result){
            throw new Exception($db_connection->error);
        }
        if ($result->num_rows != 1){
            throw new Exception('No results.');
        }
        while ($row = $result->fetch_assoc()){
            $this->id = $row['id'];
            $this->comp_id = $row['comp_id'];
            $this->date = $row['date'];
            $this->number = $row['number'];
            $this->status = $row['status'];
            $this->title = $row['title'];
            $this->customer = $row['customer'];
            $this->note = $row['note'];
            $this->incharge = $row['incharge'];
            $this->modified_by = $row['modified_by'];
            $this->modified = $row['modified'];
            $this->is_deleted = $row['is_deleted'];
        }
    }
    
    function init($db){
        $query_number = 'SELECT MAX(number) FROM administrative '
                . 'WHERE comp_id = "'.$this->comp_id.'"';
        if (! $result = $db->query($query_number)){
            throw new Exception($db->error);
        }
        $this->number = $result->fetch_row()[0] + 1;
        $query = 'INSERT INTO administrative SET '
                . 'comp_id = "'.$this->comp_id.'",'
                . 'number = "'.$this->number.'",'
                . 'date = "'.$this->date.'",'
                . 'status = "'.$this->status.'",'
                . 'title = "'.$this->title.'",'
                . 'customer = '.$this->customer.','
                . 'note = "'.$this->note.'",'
                . 'incharge = "'.$this->incharge.'",'
                . 'modified_by = "'.$this->modified_by.'",'
                . 'is_deleted = '.$this->is_deleted.' ';
        if (!$db->query($query)){
//            throw new Exception($db->error);
            throw new Exception($db->error.$query);
        }
        $this->id = $db->insert_id; 
    }
    
    function save($db){
       $query = 'UPDATE administrative SET '
                . 'comp_id = "'.$this->comp_id.'",'
                . 'number = "'.$this->number.'",'
                . 'date = "'.$this->date.'",'
                . 'status = "'.$this->status.'",'
                . 'title = "'.$this->title.'",'
                . 'customer = "'.($this->customer ?? 'NULL').'",'
                . 'note = "'.$db->real_escape_string($this->note).'",'
                . 'incharge = '.$this->incharge.','
                . 'modified_by = "'.$_SESSION['uid'].'",'
                . 'is_deleted = '.$this->is_deleted.' '
                . 'WHERE id = "'.$this->id.'"';
        if (!$db->query($query)){
            throw new Exception($db->error.$query);
        }
    }
    
    function update($db, array $values){
       // clean fields that not changed
        foreach ($values as $key=>$value){
            if ($this->$key == $value  || !property_exists($this, $key)){
                unset($values[$key]);
            }
        }
        if ($values===[]){
            throw new Exception('No changes have been made.');
        }
        $this->set($values);
        $this->save($db); //save new values
    }
    
    function resolve_by_order($db, $num, $comp){
        $query = 'SELECT * FROM administrative WHERE comp_id="'.$comp.'" and number="'.$num.'"';
        if (! $result = $db->query($query)){
             throw new Exception($db->error);
        }
        $row = $result ->fetch_assoc();
        $this->id = $row['id'];
        $this->number = $row['number'];
        $this->comp_id = $row['comp_id'];
        $this->date = $row['date'];
        $this->status = $row['status'];
        $this->title = $row['title'];
        $this->customer = $row['customer'];
        $this->note = $row['note'];
        $this->incharge=$row['incharge'];
        $this->modefied_by = $row['modified_by'];
        $this->modified = $row['modified'];
        $this->is_deleted = $row['is_deleted'];
    }
    
    function color_table($val=''){
        if ($val == ''){$val = $this->status;}
        if ($val === '1'){$out = 'row_white';}//Qoutation
        elseif ($val === '2') {$out= 'row_confirmed';} //in work
        elseif ($val === '3') {$out= 'row_complete';} //Complete
        elseif ($val === '4') {$out= 'row_grey';} //Cancelled
        elseif ($val === '5') {$out= 'row_red';} //Failed
        else {$out= 'row_white';}
        return $out;
    }
}