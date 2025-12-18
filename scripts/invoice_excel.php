<?php
require_once '../functions/main.php';
require_once '../functions/auth.php';
require_once '../functions/db.php';
require_once '../functions/service.php';
startSession();

require '../vendor/autoload.php';
// Create new Spreadsheet object
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\IOFactory;

$styleArrayUnBoldItalic = ['font' => ['bold' => false, 'italic' => true]];

$id=clean($_GET['id']);
$db =  db_connect();
$query= 'SELECT invoices.*, our_companies.*, curr_name, countries.name as our_country, c1.name AS invoice_country, full_name, '
        . 'customers.client_of, customers.cust_full_name, customers.vat, customers.InvoicingAddress as address,customers.InvoicingAddress2 as address2 '
        . 'FROM invoices '
        . 'LEFT JOIN currency ON invoices.invoice_currency=curr_id '
        . 'LEFT JOIN our_companies ON invoice_our_comp=our_companies.id '
        . 'LEFT JOIN customers ON invoice_customer=cust_id '
        . 'LEFT JOIN countries ON countries.id = our_companies.our_country '
        . 'LEFT JOIN countries c1 ON c1.id = customers.country '
        . 'LEFT JOIN users ON invoices.invoice_modified = uid '
        . 'WHERE invoice_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==1){
   exit($query);
}
$invoice=$result->fetch_assoc();

//Открытие Шаблона
$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/invoice_sales.xlsx');
$document->setActiveSheetIndex(0);

//print_r($invoice);
//exit();

//OUR Bank_details
$query='SELECT * FROM our_details_sub WHERE details_id="'.$invoice['invoice_our_bank_det'].'"';
if(!$result=$db->query($query)) echo 'Bank details error. ',$query;
$row=33;
while($bank_det=$result->fetch_assoc()){
    $document->getActiveSheet()
            ->setCellValue('B'.$row, $bank_det['param_name'])
            ->setCellValue('F'.$row, $bank_det['param_value'])
            ->insertNewRowBefore(($row+1), 1);
    $row++;
}
//Invoice to
$invoice_to_name1=$invoice['cust_full_name'];
$invoice_to_addr1=$invoice['address'];
$invoice_to_addr2=$invoice['address2'];
$invoice_to_country=$invoice['invoice_country'];  
$invoice_to_vat=$invoice['vat']; 

//Shipped TO
if($invoice['invoice_shipped_to_flag']=='0'){
    $shipped_to_name=$invoice_to_name1;
    $shipped_to_addr1=$invoice_to_addr1;
    $shipped_to_addr2=$invoice_to_addr2;
    $shipped_to_country=$invoice_to_country;
    $shipped_to_vat=$invoice_to_vat;
} elseif($invoice['invoice_shipped_to_flag']==1){
    // запрос Customer SHIPPED_TO
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$invoice['invoice_shipped_to'];
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Customer not found. Code 2.');
    }
    else $shipped_to=$result->fetch_assoc();
    $shipped_to_name=$shipped_to['cust_full_name'];
    $shipped_to_addr1=$shipped_to['InvoicingAddress'];
    $shipped_to_addr2=$shipped_to['InvoicingAddress2'];
    $shipped_to_country=$shipped_to['name'];
    $shipped_to_vat=$shipped_to['vat']; 
} ELSE { //invoice_shipped_to_flag===2
    $shipped_to_name=$invoice['invoice_shipped_name'];
    $shipped_to_addr1=$invoice['invoice_shipped_addr1'];
    $shipped_to_addr2=$invoice['invoice_shipped_addr2'];
    $shipped_to_country=$invoice['invoice_shipped_country'];
    $shipped_to_vat=$invoice['invoice_shipped_vat'];
}
//Shipped FROM
if($invoice['invoice_shipped_from_flag']==='0'){
    $shipped_from_name=$invoice['our_full_name'];
    $shipped_from_addr1=$invoice['our_inv_addr'];
    $shipped_from_addr2=$invoice['our_inv_addr2'];
    $shipped_from_country=$invoice['our_country'];
    $shipped_from_vat=$invoice['our_vat'];
}elseif ($invoice['invoice_shipped_from_flag']==='1'){
    // запрос Customer SHIPPED_FROM
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$invoice['invoice_shipped_from'];
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Customer not found. Code 3.');
    } else $shipped_from=$result->fetch_assoc();
    $shipped_from_name=$shipped_from['cust_full_name'];
    $shipped_from_addr1=$shipped_from['InvoicingAddress'];
    $shipped_from_addr2=$shipped_from['InvoicingAddress2'];
    $shipped_from_country=$shipped_from['name'];
    $shipped_from_vat=$shipped_from['vat']; 
} else{
    $shipped_from_name=$invoice['invoice_shipped_from_name'];
    $shipped_from_addr1=$invoice['invoice_shipped_from_addr1'];
    $shipped_from_addr2=$invoice['invoice_shipped_from_addr2'];
    $shipped_from_country=$invoice['invoice_shipped_from_country'];
    $shipped_from_vat=$invoice['invoice_shipped_from_vat'];
}

//Реквизиты и пр
$document->getActiveSheet()
    ->setCellValue('D2', $invoice['invoice_num'])
    ->setCellValue('I2', $invoice['invoice_date'])
    ->setCellValue('B6', $invoice['our_full_name'])
    ->setCellValue('B7', $invoice['our_inv_addr'])
    ->setCellValue('B8', $invoice['our_inv_addr2'])
    ->setCellValue('B9', $invoice['our_country'])
    ->setCellValue('B10',$invoice['our_vat'])
    ->setCellValue('H6', htmlspecialchars_decode($shipped_from_name,ENT_QUOTES))
    ->setCellValue('H7', htmlspecialchars_decode($shipped_from_addr1,ENT_QUOTES))
    ->setCellValue('H8', htmlspecialchars_decode($shipped_from_addr2,ENT_QUOTES))
    ->setCellValue('H9', htmlspecialchars_decode($shipped_from_country,ENT_QUOTES))
    ->setCellValue('H10',$shipped_from_vat)
    ->setCellValue('B13', htmlspecialchars_decode($invoice_to_name1,ENT_QUOTES))
    ->setCellValue('B14', htmlspecialchars_decode($invoice_to_addr1,ENT_QUOTES))
    ->setCellValue('B15', htmlspecialchars_decode($invoice_to_addr2,ENT_QUOTES))
    ->setCellValue('B16', $invoice_to_country)
    ->setCellValue('B17', $invoice_to_vat)
    ->setCellValue('H13', htmlspecialchars_decode($shipped_to_name,ENT_QUOTES))
    ->setCellValue('H14', htmlspecialchars_decode($shipped_to_addr1,ENT_QUOTES))
    ->setCellValue('H15', htmlspecialchars_decode($shipped_to_addr2,ENT_QUOTES))
    ->setCellValue('H16', htmlspecialchars_decode($shipped_to_country,ENT_QUOTES))
    ->setCellValue('H17', $shipped_to_vat)
    ->setCellValue('I20', $invoice['invoice_ship_date'])
    ->setCellValue('D19', $invoice['invoice_cust_ref'])
    ->setCellValue('D20', $invoice['invoice_pay_terms'])
    ->setCellValue('I21', trim($invoice['invoice_shipped_on'].' '.$invoice['invoice_awb']))
    ->setCellValue('I19', numberFormat($invoice['invoice_order_comp'], 2).'.'.$invoice['invoice_order_num'])
    ->setCellValue('D21', $invoice['curr_name'])
    ->setCellValue('B29', $invoice['invoice_vat_remarks']);
    //->setCellValue('D30', $invoice['invoice_delevery_terms']);


//QOUTATION
$row=26;
$i=1;
$entries=$db->query('SELECT * FROM invoices_content WHERE inv_con_inv_id="'.$id.'" and inv_con_qty > 0');
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['inv_con_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['inv_con_qty']);
        $document->getActiveSheet()
            ->setCellValue('J'.$row, $entry['inv_con_price']*(1-$entry['inv_con_discount']/100))
            ->setCellValue('K'.$row, $entry['inv_con_price']*(1-$entry['inv_con_discount']/100)*$entry['inv_con_qty']);
//        if ($invoice['invoice_cn_flag']==='0'){
//            $document->getActiveSheet()
//            ->setCellValue('J'.$row, $entry['inv_con_price']*(1-$entry['inv_con_discount']/100))
//            ->setCellValue('K'.$row, $entry['inv_con_price']*(1-$entry['inv_con_discount']/100)*$entry['inv_con_qty']);
//        }else{
//            $document->getActiveSheet()
//            ->setCellValue('J'.$row, $entry['inv_con_price'])
//            ->setCellValue('K'.$row, $entry['inv_con_price']*$entry['inv_con_qty']);
//        }
        $row++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row), 1)
            ->insertNewRowBefore(($row), 1)
            ->mergeCells('C'.($row+1).':H'.($row+1));
        $row++;
        $i++;
    }
}
$document->getActiveSheet()
    ->removeRow(($row - 1), 1)
    ->removeRow(($row - 1), 1);

//INVOICE NOTE
if ($invoice['invoice_invoice_note']!==''){
    $document->getActiveSheet()
        ->insertNewRowBefore(23, 1)
        ->mergeCells('C23'.':H23')
        ->setCellValue('B23', 'Note:')
        ->setCellValue('C23', $invoice['invoice_invoice_note'])
        ->getRowDimension('23')->setRowHeight(13);
    $document->getActiveSheet()->getStyle('C23')->applyFromArray($styleArrayUnBoldItalic);
}

//ВЫВОД
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Invoice '.$invoice['invoice_num'].'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($document, 'Xlsx');
$writer->save('php://output');