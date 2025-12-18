<?php
require_once '../functions/auth.php';
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/service.php';
startSession();
security();
if(check_access('acl_sales', 2)) exit('Access denied.');

$id=$_POST['id'];
$db= db_connect();
$db->autocommit(false);
//COPY SERIVCE ORDER
$query='INSERT INTO 
    sales (`sales_our_comp`, `sales_our_bank_details`, `sales_status`, `sales_date`, `sales_descr`, sales_qte_note, `sales_customer`, `sales_cust_po`, `sales_request`, `sales_awb`, `sales_currency`, `sales_payment`, `sales_pay_date`, `sales_total`,`sales_total_cfm`, `sales_total_vat`,`sales_vat_cfm`, `sales_invoice`, `sales_invoice_date`, `sales_cn`, `sales_cn_flag`, `sales_invoice_note`, `sales_delevery_terms`, `sales_pay_terms`, `sales_invoice_from`, `sales_invoice_to`, `sales_invoice_to_flag`, `sales_shipped_from`, `sales_shipped_from_flag`, `sales_shipped_from_name`, `sales_shipped_from_addr1`, `sales_shipped_from_addr2`, `sales_shipped_from_country`, `sales_shipped_from_vat`, `sales_shipped_to`, `sales_shipped_to_flag`, `sales_shipped_name`, `sales_shipped_addr1`, `sales_shipped_addr2`, `sales_shipped_country`, `sales_shipped_vat`, `sales_shipped_on`, `sales_ship_date`, `sales_vat_remarks`, `modified_date`, `modified`, `sales_deleted`)
    SELECT `sales_our_comp`, `sales_our_bank_details`, `sales_status`, `sales_date`, `sales_descr`, sales_qte_note,`sales_customer`, `sales_cust_po`, `sales_request`, `sales_awb`, `sales_currency`, `sales_payment`, `sales_pay_date`, `sales_total`,`sales_total_cfm`, `sales_total_vat`,`sales_vat_cfm`, `sales_invoice`, `sales_invoice_date`, `sales_cn`, `sales_cn_flag`, `sales_invoice_note`, `sales_delevery_terms`, `sales_pay_terms`, `sales_invoice_from`, `sales_invoice_to`, `sales_invoice_to_flag`, `sales_shipped_from`, `sales_shipped_from_flag`, `sales_shipped_from_name`, `sales_shipped_from_addr1`, `sales_shipped_from_addr2`, `sales_shipped_from_country`, `sales_shipped_from_vat`, `sales_shipped_to`, `sales_shipped_to_flag`, `sales_shipped_name`, `sales_shipped_addr1`, `sales_shipped_addr2`, `sales_shipped_country`, `sales_shipped_vat`, `sales_shipped_on`, `sales_ship_date`, `sales_vat_remarks`, `modified_date`, `modified`, `sales_deleted`
    FROM `sales` 
    WHERE sales_id="'.$id.'"';
if (!$db->query($query)){
    echo $db->error;
    echo $query;
    $db->rollback();
    $db->close();
    exit();
}
$new_id = $db->insert_id;
//Sales_id into service_no
if($result=$db->query('SELECT MAX(sales_no)AS max FROM sales WHERE sales_our_comp=(SELECT sales_our_comp FROM sales WHERE sales_id='.$id.')')){
    $row = $result->fetch_assoc();
    $db->query('UPDATE sales SET sales_no='.($row['max']+1).' WHERE sales_id='.$new_id);
} else {
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();
}
//Copying sales content
$query='INSERT INTO `sales_content`(`scont_sale_id`, `scont_base_id`, `scont_text`, `scont_price`, `scont_qty`, `scont_discount`, `scont_currency`, `scont_currency_rate`, `scont_vat`, `scont_has_serial`, `scont_serials`, `scont_box_no`, `scont_cfm_qty`)'
        . 'SELECT "'.$new_id.'", `scont_base_id`, `scont_text`, `scont_price`, `scont_qty`, `scont_discount`, `scont_currency`, `scont_currency_rate`, `scont_vat`, `scont_has_serial`, `scont_serials`, `scont_box_no`,`scont_cfm_qty` FROM sales_content '
        . 'WHERE scont_sale_id="'.$id.'"';
if (!$db->query($query)){
    echo $db->error;
    echo $query;
    $db->rollback();
    $db->close();
    exit();
}

$db->commit();
echo 'true';