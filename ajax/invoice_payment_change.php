<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$pay_id=$_POST['pay_id'];
$db =  db_connect();
$query= 'SELECT * FROM invoices_payments WHERE pay_id="'.$pay_id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1){
    echo 'Error. Invoice not found.'.$db->error;
    echo $query;
    exit();
}
$row=$result->fetch_assoc();
$invoice_num=$row['invoice_num'];
?>
    <div class="close_button_div"><a class="close_button" href="#" onclick="invoice_payments_close(this);">&#10006;</a></div>
    <h2>Edit payment</h2>
    <form onsubmit="return sales_new_form(this)">
        <input type="hidden" name="pay_id" value="<?php echo $pay_id;?>">
    <table>
        <tr>
            <td>Payment date</td><td><input type="text" size="10" class="datepicker" value="<?php echo $row['pay_date'];?>" name="pay_date"></td>
            <td>Payment amount</td><td><input type="text" size="10" name="pay_amount" value="<?php echo $row['pay_amount'];?>"></td>
        </tr>
        <tr><td><strong>Delete</strong></td><td><input type="checkbox" name="pay_delete" value="1"></td></tr>
        <tr><td colspan="4" style="text-align: center"><a href="#" class="knopka green_button" onclick="invoice_payment_change(this,<?php echo $row['pay_inv_id'];?>)">Save</a> <a href="#" class="knopka" onclick="invoice_payments_close(this)">Cancel</a></td></tr>
    </table>
    </form>