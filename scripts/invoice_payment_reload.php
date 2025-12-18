<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
$invoice_id=clean($_POST['invoice_id']);
?>
<div class="align_center"><strong>Payments</strong></div>
    <table width="100%">
        <thead><th>Date</th><th>Amount</th><th>Edit</th></thead>                
        <?php
        $db=db_connect();
        $query='SELECT * FROM invoices_payments WHERE pay_inv_id="'.$invoice_id.'"';
        $result=$db->query($query);
        $total_payments=0;
        if (!$result)echo 'Nothing found'.$db->error;
        while ($payment=$result->fetch_assoc()){
            $total_payments+=$payment['pay_amount'];
            echo'<tr>'
                . '<td>',$payment['pay_date'],'</td>'
                . '<td>',$payment['pay_amount'],'</td>'
                . '<td><a href="#" onclick="invoice_payment_edit(this,\'',$payment['pay_id'],'\')">Edit</a></td></tr>';
        }
        ?>
        <tr style="border-top:2px solid black;">
             <td><strong>Total:</strong></td><td><?php echo $total_payments;?></td>
            <td><a href="#" class="knopka3" onclick="invoice_add_payment(this,<?php echo $invoice_id;?>)">ADD</a></td>
        </tr>
    </table>