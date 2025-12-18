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

$styleArrayBold = ['font' => ['bold' => true]];
$styleArrayUnBold = ['font' => ['bold' => false]];

$id=clean($_GET['id']);
$db =  db_connect();
$query= 'SELECT sales.*, our_companies.*,customers.client_of, curr_name, countries.name as our_country, our_details.id as our_bank_details, our_details.pay_comment '
        . 'FROM sales    '
        . 'LEFT JOIN currency ON sales.sales_currency=curr_id '
        . 'LEFT JOIN our_companies ON sales_invoice_from=our_companies.id '
        . 'LEFT JOIN customers ON sales_customer=cust_id '
        . 'LEFT JOIN countries ON countries.id = our_companies.our_country '
        . 'LEFT JOIN our_details ON our_details.id=sales_our_bank_details '
        . 'WHERE sales_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==1){
    echo $query;
    echo $db->error;
   exit('Sale order not found.');
}
$sale=$result->fetch_assoc();

// запрос Customer Invoice_TO
$query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$sale['sales_invoice_to'];
$result=$db->query($query);
if ($result-> num_rows!==1){
    $db->close();
    exit('Customer not found');
}
$invoice_to=$result->fetch_assoc();

//Открытие Шаблона
$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/invoice_cn.xlsx');
$document->setActiveSheetIndex(0);

//OUR Bank_details
$query='SELECT * FROM our_details_sub WHERE details_id="'.$sale['our_bank_details'].'"';
$result=$db->query($query);
$row=22;
while($bank_det=$result->fetch_assoc()){
    $document->getActiveSheet()
            ->setCellValue('B'.$row, $bank_det['param_name'])
            ->setCellValue('D'.$row, $bank_det['param_value'])
            ->insertNewRowBefore(($row+1), 1)
            ->mergeCells('D'.($row+1).':K'.($row+1));
    $row++;
}

//INVOICE TO
if($sale['sales_inv_instructions']==='1'){
    $invoice_to_name1=$sale['sales_inv_name1'];
    $sales_inv_addr1=$sale['sales_inv_addr1'];
    $sales_inv_addr2=$sale['sales_inv_addr2'];
    $sales_inv_country=$sale['invoice_country'];  
    $sales_inv_vat=$sale['sales_inv_vat'];    
} ELSE {
    $invoice_to_name1=$invoice_to['cust_full_name'];
    $sales_inv_addr1=$invoice_to['InvoicingAddress'];
    $sales_inv_addr2=$invoice_to['InvoicingAddress2'];
    $sales_inv_country=$invoice_to['name']; 
    $sales_inv_vat='VAT: '.$invoice_to['vat']; 
}

//Реквизиты и пр
$document->getActiveSheet()
    ->setCellValue('D2', $sale['sales_cn'])
    ->setCellValue('K2', $sale['sales_invoice_date'])
    ->setCellValue('I6', $sale['our_full_name'])
    ->setCellValue('I7', $sale['our_inv_addr'])
    ->setCellValue('I8', $sale['our_inv_addr2'])
    ->setCellValue('I9', $sale['our_country'])
    ->setCellValue('I10',$sale['our_vat'])
    ->setCellValue('B6', $invoice_to_name1)
    ->setCellValue('B7', $sales_inv_addr1)
    ->setCellValue('B8', $sales_inv_addr2)
    ->setCellValue('B9', $invoice_to['name'])
    ->setCellValue('B10', $invoice_to['vat'])
    ->setCellValue('D12', $sale['sales_cust_po'])
    ->setCellValue('D13', $sale['sales_invoice'])
    ->setCellValue('K12', $sale['id'].'.'.$sale['sales_no'].$sale['client_of'])
    ->setCellValue('K13', $sale['curr_name']);

//QOUTATION
$row=17;
$i=1;
$entries=$db->query('SELECT * FROM sales_content WHERE scont_sale_id="'.$id.'" AND scont_discount<>0');
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['scont_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['scont_qty'])
            ->setCellValue('J'.$row, $entry['scont_price'])
            ->setCellValue('L'.$row, $entry['scont_discount'])
            ->setCellValue('M'.$row, $entry['scont_price']*$entry['scont_qty']*(-1*$entry['scont_discount']/100));
        $row++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row), 1)
            ->insertNewRowBefore(($row), 1)
            ->mergeCells('C'.($row+1).':H'.($row+1))
            ->mergeCells('J'.($row+1).':K'.($row+1));;
        $row++;
        $i++;
    }
}
$document->getActiveSheet()
    ->removeRow(($row - 1), 1)
    ->removeRow(($row - 1), 1);


//ВЫВОД
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="CN_'.$sale['sales_cn'].'.xlsx"');
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