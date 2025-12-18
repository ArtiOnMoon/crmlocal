<?php
require_once './functions/db.php';
require_once './functions/main.php';

function order_selector($type='',$id='',$text='',$name=''){
    $out='<div class="order_selector">';    
    $out.= entity_type($type,'class="order_selector_type"');
    $out.='<input type="hidden" class="order_selector_to_send" value="'.$id.'">';
    $out.='<input type="search" class="order_selector_value" oninput="order_selector_oninput(this)" onblur = "order_selector_blur(this)" value="'.$text.'">';
    $out.='<input type="button" class="order_selector_button" value="▼">';
    $out.='<div class="order_selector_search"></div>';
    $db = db_connect();
    switch ($type) {
        case 1:
            $query = 'SELECT service_id, service_no, service_our_comp, ';
            break;

        default:
            break;
    }
    return $out.'</div>';
}