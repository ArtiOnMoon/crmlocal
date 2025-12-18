<?php

class Base_class {
    
    function set(array $values){
        foreach ($values as $key=>$value){
            if (property_exists($this, $key)){
                $this->$key = $value;
            }
        }
    }
    
    private function get_users_list($db_connection){
        $query = 'SELECT uid, full_name FROM users';
        $result = $db_connection -> query($query);
        while ($row = $result->fetch_assoc()) {
            $data[$row['uid']]=($row['full_name']);        
        }
        return $data;
    }
}
