<?php
function select_order_type(){
    $str='<select name="order_type" id="order_type">'
            . '<option value="0">Sales</option>'
            . '<option value="1">Service</option>'
            . '</select>';
    return $str;
}
function order_type_decode($val){
   if ($val=='0')return 'Sales';
    elseif($val=='1') return 'Service';
    return 'Not set';
}
function select_urgency(){
    $str='<select name="order_urgency" id="order_urgency">'
            . '<option value="0">Ordinary</option>'
            . '<option value="1">Urgent</option>'
            . '</select>';
    return $str;
}
function order_urgency_decode($val){
    if ($val=='0')return 'Normal';
    elseif($val=='1') return 'Urgent';
    return 'Not set';
}