<?php

class Order_name_engine {
    public $type = null;
    public $comp_id = null;
    public $num = null;
    public $id = null;
    public $order = null;
    public $comp_list = [];
    public $types_list=[
        'SR', 'SL', 'PO', 'AD', 'TN', 'CL', 'CT'
    ];
    
    function init ($db_connection){
        $query = 'SELECT id, our_name FROM our_companies';
        $result = $db_connection->query($query);
        if (!$result){throw new Exception($db_connection->error);}
        while ($row = $result->fetch_assoc()){
            $this->comp_list[$row['id']] = $row['our_name'];
        }
    }
    
    function set($type, $comp_id, $num, $id = null){
        $this->type = $type;
        $this->comp_id = $comp_id;
        $this->num = intval($num);
        if (!is_null($id)){
            $this->id = $id;
        }
    }

    function resolve_order($order){
        $order = strtoupper(trim($order));
        $this -> check_order($order);
        $num = substr($order, -5);
        $type = substr($order, 0,2);
        if (!in_array($type, $this->types_list)){
            $this -> type = null;
            throw new Exception('Incorrect order type');
        }
        $comp_id = array_search(substr($order, 3,3), $this->comp_list);
        if (!$comp_id){
            $this -> comp_id = null;
            throw new Exception('Incorrect company in order number');
        }
        $this->order = $order;
        $this->type = $type;
        $this->comp_id = $comp_id;
        $this->num = $num;
        return true;
    }
    
    function resolve_id($db_connection){
        $this ->check_order($this->order);
        $query_list = [
        'SR' => 'SELECT service_id FROM service WHERE service_our_comp="'.$this->comp_id.'" and service_no="'.$this->num.'"',
        'SL' => 'SELECT sales_id FROM sales WHERE sales_our_comp="'.$this->comp_id.'" and sales_no="'.$this->num.'"',
        'PO' => 'SELECT po_id FROM purchase WHERE po_our_comp="'.$this->comp_id.'" and po_no="'.$this->num.'"',
        'AD' => 'SELECT id FROM administrative WHERE comp_id="'.$this->comp_id.'" and number="'.$this->num.'"',
        'TN' => [],
        'CT' => [],
        'CL' => [],
        ];
        $result = $db_connection->query($query_list[$this->type]);
        if (!$result){
            throw new Exception($db_connection->error);
        }
        if ($result->num_rows != 1){
            throw new Exception('No results.');
        }
        $this->id = $result->fetch_row()[0];
        return true;
    }

    function get_order(){
        $this->order = $this->type.'-'.$this->comp_list[(int)$this->comp_id].'-'.numberFormat($this->num,5);
        return $this->order;
    }
    
    function check_order($order){
        if (strlen($order) != 12){
            throw new Exception('Incorrect order format(length)'.$order);
        }
        if (! preg_match('/[A-Z][A-Z][-][A-Z][A-Z][A-Z][-]\d\d\d\d\d/', $order)){
          throw new Exception('Incorrect order format');
        }
    }
    
    function numberFormat($digit, $width) {
        while(strlen($digit) < $width){
            $digit = '0' . $digit;
        }
        return $digit;
    }    
}
