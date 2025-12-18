<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/service.php';
set_include_path(get_include_path() . PATH_SEPARATOR . "../dompdf");
require_once '../dompdf/autoload.inc.php';
startSession();
use Dompdf\Dompdf;

$dompdf = new DOMPDF();
$dompdf->set_paper("A4");
$html = '';
$total=0;
//START

$id=clean($_GET['service_id']);
$saved=clean($_GET['saved']);
$db =  db_connect();
//Проверка существования проформы
$proforma_exist=1;
$query= 'select * from invoice_proforma where proforma_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1){
   exit('Nothing found');
}
$proforma=$result->fetch_assoc();

// запрос Customer cust short name
$query = 'select cust_short_name, cust_full_name, InvoicingAddress from customers where cust_id='.$proforma['comp_id'];
$result=$db->query($query);
$customer=$result->fetch_assoc();

// запрос Our company
$query = 'select * from our_companies where id='.$proforma['proforma_our_company'];
$result=$db->query($query);
$our_comp=$result->fetch_assoc();

//Вывод в HTML
$html.='
<html><head>
<style> body { font-family: Arial, sans-serif; font-size: 12px;} </style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head><body>
<div style="width:100%;align-content:center;display:inline-block;vertical-align:text-top;">
<h1>Invoice №'.$id.'</h1>
<table width="100%" style="border-collapse: collapse;">
    <tr>
        <td><strong>From:</strong></td>
        <td>'.$our_comp['our_full_name'].'</td>
        <td><strong>To:</strong></td>
        <td>'.$customer['cust_full_name'].'</td>
    </tr>
    <tr>
        <td></td>
        <td>'.$our_comp['our_inv_addr'].'</td>
        <td></td>
        <td>'.$customer['InvoicingAddress'].'</td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td><strong>Service ID</strong></td>
        <td>'.$id.'</td>
    </tr>
</table>
<table width="100%" style="border-collapse: collapse;">
    <tr>
        <td style="border: 1px solid black;"><strong>Date</strong></td>
        <td style="border: 1px solid black;"><strong>Payment terms:</strong></td>
        <td style="border: 1px solid black;"><strong>Our ref</strong></td>
        <td style="border: 1px solid black;"><strong>Your ref</strong></td>
        <td style="border: 1px solid black;"><strong>Currency</strong></td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">'.$proforma['pay_terms'].'</td>
        <td style="border: 1px solid black;">'.$proforma['our_ref'].'</td>
        <td style="border: 1px solid black;">'.$proforma['your_ref'].'</td>
        <td style="border: 1px solid black;">'.$proforma['currency'].'</td>
    </tr>
</table>
';
$html.='
<p>
<strong>Rates:</strong>
</p>
<table width="100%" style="text-align:center">
    <tr>
        <th>#</th>
        <th>Description</th>
        <th>Q-ty</th>
        <th>Unit price</th>
        <th>Amount</th>
    </tr>';
// Вставка RATES
$i=1;
        if (isset($proforma['rates']) AND ($proforma['rates']!=='NULL')){           
            $rates= json_decode($proforma['rates']);
            foreach ($rates as $rate){
                $query='select rate_name from service_rates where rate_id="'.$rate[3].'"';
                $result=$db->query($query);
                $rate_name=$result->fetch_row();
                $html.='<tr><td>'
                .$i.'</td><td>'
                . $rate_name[0].'</td><td>'
                .$rate[0].'</td><td>'
                .$rate[1].'</td><td>'
                .$rate[1]*$rate[0].'</td></tr>';
                $i++;
                $total+=$rate[1]*$rate[0];
            }
        }
$html.='</table>';

$html.='
<p>
<strong>Spare parts and materials:</strong>
</p>
<table id="proforma_spare" width="100%" style="text-align:center">
    <tr>
        <th>#</th>
        <th>Part number</th>
        <th>Description</th>
        <th">Q-ty</th>
        <th">Unit price</th>
        <th>Amount</th>
    </tr>
    <tbody>';
        if (isset($proforma['spare']) AND ($proforma['spare']!=='NULL')){
            $spare= json_decode($proforma['spare']);
            foreach ($spare as $sp){
                $html.='<tr>';
                $html.= '<td>'
                .$i.'</td><td>'
                .$sp[0].'</td><td>'
                .$sp[1].'</td><td>'
                .$sp[2].'</td><td>'
                .$sp[3].'</td><td>'
                .$sp[3]*$sp[2].'</td></td>';
                $html.= '</tr>';
                $i++;
                $total+=$sp[3]*$sp[2];
            }
        }
$html.='</tbody></table>';
$html.=' 
<div >
    <div style="min-width:150px;text-align:right;border-top:1px solid black;"><strong>Total:'.$total.'</strong></div>
</div>';
$html.='
    <table style="width: 100%;">
     <tr>
        <td><i>Your bank’s charges cannot be deducted from the amount of this invoice.Make sure that we will receive the full amount of this invoice to our bank account in accordance to details below.</i></td>
    </tr>
    <tr>
        <td><pre>'.$our_comp['our_bank_details'].'</pre></td>
    </tr>
    </table>
    </div></body></html>';
//Вывод документа
$dompdf->load_html($html);
$dompdf->render();

$dompdf->stream("proforma_".$id.".pdf");