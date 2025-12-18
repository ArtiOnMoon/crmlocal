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
$query= 'select * from invoices_out where id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1){
   exit('Nothing found');
}
$invoice=$result->fetch_assoc();

// запрос Customer cust short name
$query = 'select cust_short_name, cust_full_name, InvoicingAddress from customers where cust_id='.$invoice['customer'];
$result=$db->query($query);
if ($result-> num_rows!==1){
   exit('Nothing found');
}
$customer=$result->fetch_assoc();

// запрос Our company
$query = 'select * from our_companies where id='.$invoice['our_comp'];
$result=$db->query($query);
if ($result-> num_rows!==1){
   exit('Nothing found');
}
$our_comp=$result->fetch_assoc();
$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/invoice_credit_note.xlsx');
$document->setActiveSheetIndex(0);

$document->getActiveSheet()
    ->setCellValue('D6', $customer['cust_full_name'])
    ->setCellValue('D7', $customer['InvoicingAddress'])
    ->setCellValue('I6', $our_comp['our_full_name'])
    ->setCellValue('I7', $our_comp['our_inv_addr'])
    ->setCellValue('B26', $our_comp['our_bank_details'])
    ->setCellValue('D14', $invoice['pay_terms'])
    ->setCellValue('I12', $invoice['our_ref'])
    ->setCellValue('D12', $invoice['your_ref'])
    ->setCellValue('I14', $invoice['currency'])
    ->setCellValue('H3', $invoice['invoice_num'])
    ->setCellValue('K3', $invoice['invoice_date']);

$i=1;
$row=19;
if (isset($invoice['rates']) AND ($invoice['rates']!=='NULL')){           
    $rates= json_decode($invoice['rates']);
    foreach ($rates as $rate){
        $query='select rate_name from service_rates where rate_id="'.$rate[3].'"';
        $result=$db->query($query);
        $rate_name=$result->fetch_row();
        $document->getActiveSheet()
                ->setCellValue('B'.$row, $i)
                ->setCellValue('C'.$row, $rate_name[0])
                ->setCellValue('I'.$row, $rate[0])
                ->setCellValue('J'.$row, $rate[1])
                ->setCellValue('K'.$row, '=J'.$row.'*I'.$row);
        $i++;
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
        }
}
if (isset($proforma['rates']) AND ($proforma['rates']!=='NULL')){  
    $document->getActiveSheet()->insertNewRowBefore(($row+1), 1);
    $document->getActiveSheet()->mergeCells('C'.($row+1).':H'.($row+1));
    $row++;
}
if (isset($invoice['spares']) AND ($invoice['spares']!=='NULL')){  
    $spare= json_decode($invoice['spares']);
    foreach ($spare as $sp){
        $document->getActiveSheet()
                ->setCellValue('B'.$row, $i)
                ->setCellValue('C'.$row, $sp[1])
                ->setCellValue('I'.$row, $sp[2])
                ->setCellValue('J'.$row, $sp[3])
                ->setCellValue('K'.$row, '=J'.$row.'*I'.$row);
        $i++;
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
        }
}
$document->getActiveSheet()->removeRow($row);
//$document->getActiveSheet()->insertNewRowBefore(19, 1);
//$document->getActiveSheet()->mergeCells('C20:H20');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Document.xlsx"');
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