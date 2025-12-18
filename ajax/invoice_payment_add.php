<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$invoice_id=$_POST['invoice_id'];
$db =  db_connect();
$query= 'SELECT invoice_num FROM invoices WHERE invoice_id="'.$invoice_id.'"';
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
    <h2>Payment for invoice # <?php echo $invoice_num;?></h2>
    <form onsubmit="return sales_new_form(this)">
        <input type="hidden" name="pay_inv_id" value="<?php echo $invoice_id;?>">
    <table>
        <tr>
            <td>Payment date</td><td><input type="text" size="10" class="datepicker" name="pay_date"></td>
            <td>Payment amount</td><td><input type="number" step="0.01" size="10" name="pay_amount"></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center">
                <a href="#" class="knopka green_button" onclick="invoice_payment_new(this,<?php echo $invoice_id;?>)">Save</a> 
                <a href="#" class="knopka" onclick="invoice_payments_close(this)">Cancel</a>
            </td>
        </tr>
    </table>
    </form>