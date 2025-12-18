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

$id=clean($_GET['invoice_id']);
$db =  db_connect();
//Проверка существования проформы
$query= 'select * from invoice_service where inv_srv_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==1){
   exit('Nothing found');
}
$invoice=$result->fetch_assoc();

// запрос Customer cust short name
$query = 'select cust_short_name, cust_full_name, InvoicingAddress from customers where cust_id='.$invoice['inv_srv_customer'];
$result=$db->query($query);
if ($result-> num_rows!==1){
    $db->close();
    exit('Nothing found');
}
$customer=$result->fetch_assoc();

// запрос Our company
$query = 'select * from our_companies where id='.$invoice['inv_srv_our_comp'];
$result=$db->query($query);
if ($result-> num_rows!==1){
    $db->close();
    exit('Nothing found');
}
$our_comp=$result->fetch_assoc();
$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/invoice_service.xlsx');
$document->setActiveSheetIndex(0);

$document->getActiveSheet()
    ->setCellValue('B7', htmlspecialchars_decode($customer['cust_full_name']))
    ->setCellValue('B8', $customer['InvoicingAddress'])
    ->setCellValue('H7', $our_comp['our_full_name'])
    ->setCellValue('H8', $our_comp['our_inv_addr'])
    ->setCellValue('B25', $our_comp['our_bank_details'])
    ->setCellValue('D14', $invoice['inv_srv_pay_terms'])
    ->setCellValue('I12', $invoice['inv_srv_our_ref'])
    ->setCellValue('D12', $invoice['inv_srv_your_ref'])
    ->setCellValue('I14', $invoice['inv_srv_currency'])
    ->setCellValue('H3', $invoice['inv_srv_num'])
    ->setCellValue('K3', $invoice['inv_srv_date']);
$i=1;
$row=19;
//RATES
$rates=$db->query('select invoice_service_rates.*, service_rates.rate_name from invoice_service_rates, service_rates where invsr_inv_rate_id=rate_id and invsr_inv_id="'.$id.'"');
if ($rates->num_rows > 0){
    while($rate=$rates->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, $rate['rate_name'])
            ->setCellValue('I'.$row, $rate['invsr_inv_rate_qnt'])
            ->setCellValue('J'.$row, $rate['invsr_inv_rate_price'])
            ->setCellValue('K'.$row, '=J'.$row.'*I'.$row);
        $i++;
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
    }
    $row++;
    $document->getActiveSheet()->insertNewRowBefore(($row), 1);
    $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
}
//SPARES
$spares=$db->query('select * from invoice_service_items where invsi_inv_id="'.$id.'"');
if ($spares->num_rows > 0){
    while($spare=$spares->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, $spare['invsi_descr'])
            ->setCellValue('I'.$row, $spare['invsi_qnt'])
            ->setCellValue('J'.$row, $spare['invsi_price'])
            ->setCellValue('K'.$row, '=J'.$row.'*I'.$row);
        $i++;
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
                
    }
}
//Последняя пустая строчка
$document->getActiveSheet()->removeRow($row);
//$document->getActiveSheet()->insertNewRowBefore(19, 1);
//$document->getActiveSheet()->mergeCells('C20:H20');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$invoice['inv_srv_num'].'.xlsx"');
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