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
$query= 'SELECT purchase.*, our_companies.*, curr_name, countries.name as our_country, '
        . 'customers.client_of, customers.cust_full_name, customers.vat, customers.InvoicingAddress as address,customers.InvoicingAddress2 as address2 '
        . 'FROM purchase '
        . 'LEFT JOIN currency ON po_currency=curr_id '
        . 'LEFT JOIN our_companies ON po_our_comp=our_companies.id '
        . 'LEFT JOIN customers ON po_supplier=cust_id '
        . 'LEFT JOIN countries ON countries.id = our_companies.our_country '
        . 'WHERE po_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==1){
    echo $db->error;
    exit($query);
}
$po=$result->fetch_assoc();
($po['po_delivery1']!=='') ? $po_delivery1=$po['po_delivery1']:$po_delivery1=$po['our_full_name'];
($po['po_delivery1']!=='') ? $po_delivery2=$po['po_delivery2']:$po_delivery2=$po['our_inv_addr2'];
($po['po_delivery1']!=='') ? $po_delivery3=$po['po_delivery3']:$po_delivery3=$po['our_inv_addr'];
($po['po_delivery1']!=='') ? $po_delivery4=$po['po_delivery4']:$po_delivery4=$po['our_country'];
($po['po_pic_name']!=='') ? $po_pic_name=$po['po_pic_name']:$po_pic_name=$po['our_pic_name'];
($po['po_pic_name']!=='') ? $po_pic_phone=$po['po_pic_phone']:$po_pic_phone=$po['our_pic_phone'];
//Открытие Шаблона
$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/po.xlsx');
$document->setActiveSheetIndex(0);
$document->getActiveSheet()
    ->setCellValue('B1', $po['our_full_name'])
    ->setCellValue('B3', $po['our_inv_addr2'].' '.$po['our_country'])
    ->setCellValue('B4', $po['our_inv_addr'])
    ->setCellValue('B5', 'Tel.:'.$po['our_phone'])
    ->setCellValue('B6', $po['our_mail'])
    ->setCellValue('B12', htmlspecialchars_decode($po['cust_full_name'],ENT_QUOTES))
    ->setCellValue('B15', $po_delivery1)
    ->setCellValue('B16', $po_delivery2)
    ->setCellValue('B17', $po_delivery3)
    ->setCellValue('B18', $po_delivery4)
    ->setCellValue('I15', $po_pic_name)
    ->setCellValue('I16', $po_pic_phone)
    ->setCellValue('F12', $po['po_date'])
    ->setCellValue('I12', $po['po_our_comp'].'.'.$po['po_no'])
    ->setCellValue('J32', $po['curr_name'])
    ->setCellValue('B21', $po['po_print_note']);
//Invoice to
if($po['po_invoice_to_flag']==='0'){
    $document->getActiveSheet()
            ->setCellValue('F15', $po['our_full_name'])
            ->setCellValue('F16', $po['our_inv_addr2'])
            ->setCellValue('F17', $po['our_inv_addr'])
            ->setCellValue('F18', $po['our_country'])
            ->setCellValue('F19', 'VAT.:'.$po['our_vat']);   
} ELSE {
    // запрос Customer Invoice_TO
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$po['po_invoice_to'];
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Customer not found. Code 1.');
    } else $invoice_to=$result->fetch_assoc();
    $document->getActiveSheet()
            ->setCellValue('F15', htmlspecialchars_decode($invoice_to['cust_full_name'],ENT_QUOTES))
            ->setCellValue('F16', $invoice_to['InvoicingAddress'])
            ->setCellValue('F17', $invoice_to['InvoicingAddress2'])
            ->setCellValue('F18', $invoice_to['name'])
            ->setCellValue('F19', 'VAT.:'.$invoice_to['vat']);
}

//QOUTATION
$row=28;
$i=1;
$entries=$db->query('SELECT * FROM purchase_content WHERE po_con_po_id="'.$id.'"');
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['po_con_text'],ENT_QUOTES))
            ->setCellValue('G'.$row, $entry['po_con_qty'])
            ->setCellValue('H'.$row, $entry['po_con_price'])
            ->setCellValue('I'.$row, $entry['po_con_discount'])   
            ->setCellValue('J'.$row, $entry['po_con_price']*(1-$entry['po_con_discount']/100)*$entry['po_con_qty']);
        $row++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row), 1)
            ->insertNewRowBefore(($row), 1)
            ->mergeCells('C'.($row+1).':F'.($row+1));
        $row++;
        $i++;
    }
}
$document->getActiveSheet()
    ->removeRow(($row - 1), 1)
    ->removeRow(($row - 1), 1);


//ВЫВОД
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PO '.$po['po_our_comp'].'.'.$po['po_no'].'.xlsx"');
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