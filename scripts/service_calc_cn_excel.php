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

$id=clean($_GET['service_id']);
$db =  db_connect();
$query= 'SELECT service.*, vessels.vessel_name, our_companies.*,countries.name as country_name, c1.name as inv_country_name, curr_name, our_c1.our_name AS our_code '
        . 'FROM service '
        . 'LEFT JOIN vessels ON service.vessel_id=vessels.vessel_id '
        . 'LEFT JOIN our_companies ON srv_inv_from=our_companies.id '
        . 'LEFT JOIN our_companies our_c1 ON service_our_comp=our_c1.id '
        . 'LEFT JOIN countries ON countries.id = our_companies.our_country '
        . 'LEFT JOIN countries c1 ON c1.id = srv_inv_country '
        . 'LEFT JOIN currency ON service_currency=curr_id '
        . 'WHERE service_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==1){
   exit('Service not found.');
}
$service=$result->fetch_assoc();

// запрос Customer cust short name
$query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$service['comp_id'];
$result=$db->query($query);
if ($result-> num_rows!==1){
    $db->close();
    exit('Customer not found');
}
$customer=$result->fetch_assoc();

//Invoicng Instructions
if ($service['inv_instructions']){
    $invoice_to = $service['srv_inv_comp_name'];
    $invoice_to2 = $service['srv_inv_comp_name2'];
    $inv_addr1 = $service['srv_inv_addr1'];
    $inv_addr2 = $service['srv_inv_addr2'];
    $country = $service['inv_country_name'];
    $vat=$service['srv_inv_vat'];
}
else{
    $invoice_to = $customer['cust_full_name'];
    $inv_addr1 = $customer['InvoicingAddress2'];
    $inv_addr2 = $customer['InvoicingAddress'];
    $country = $customer['name'];
    $vat=$customer['vat'];
}

$inv_date = ($service['srv_inv_date']=='' ? date('d.m.Y',strtotime($service['service_date'])) : date('d.m.Y',strtotime($service['srv_inv_date'])));

$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/invoice_service_cn.xlsx');
$document->setActiveSheetIndex(0);

//Реквизиты и пр
$document->getActiveSheet()
    ->setCellValue('B3', $service['our_full_name'])
    ->setCellValue('B4', $service['our_inv_addr'])
    ->setCellValue('B5', $service['our_inv_addr2'])
    ->setCellValue('B6', $service['country_name'])
    ->setCellValue('B7', 'VAT: '.$service['our_vat'])
    ->setCellValue('K6', $service['our_code'].' '.service_id_num($service['service_no'],$service['service_our_comp']))
    ->setCellValue('K7', 'mv '.$service['vessel_name'])
    ->setCellValue('K2', $service['srv_cn_number'])
    ->setCellValue('K4', $inv_date);

$row=10; //Заполнение кастомера
if ($invoice_to2 !=''){
    $document->getActiveSheet()
        ->insertNewRowBefore(($row+1), 1)
        ->setCellValue('B'.$row++, htmlspecialchars_decode($invoice_to))
        ->setCellValue('B'.$row++, htmlspecialchars_decode($invoice_to2));
}
else $document->getActiveSheet()->setCellValue('B'.$row++, htmlspecialchars_decode($invoice_to));
$document->getActiveSheet()
    ->setCellValue('B'.$row++, $inv_addr1)
    ->setCellValue('B'.$row++, $inv_addr2)
    ->setCellValue('B'.$row++, $country)
    ->setCellValue('B'.$row++, 'VAT: '.$vat);

$document->getActiveSheet()
    ->setCellValue('I10','Customer PO Ref:')
    ->setCellValue('K10', $service['PO'])
    ->setCellValue('K11', $service['PO2'])
    ->setCellValue('I12','Original Invoice:')
    ->setCellValue('K12', $service['srv_inv_number'])
    ->setCellValue('I14','Currency:')
    ->setCellValue('K14', $service['curr_name']);
$i=1;
$row+=4;
$document->getActiveSheet()
        ->setCellValue('C'.$row, 'Product Service Report №'.service_id_num($service['service_no'],$service['service_our_comp']));
$row++;
$document->getActiveSheet()->insertNewRowBefore(($row), 1);
$document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));

//CALCULATION
//RATES
$entries=$db->query('SELECT * FROM service_calc_entries WHERE ((entry_discount<>0 AND entry_type=1) OR entry_type=3)  AND entry_related_id="'.$id.'"');
if ($entries->num_rows > 0){
    $prev_res=0;
    while($entry=$entries->fetch_assoc()){
        if($entry['entry_type']==='3'){
            if ($prev_res===3) {
                //Если предыдущий тоже заголовок, затереть его
                $document->getActiveSheet()->removeRow($row-1)->removeRow($row-1);
                $row=$row-2;
                $i--;
            }
            $prev_res=3;
            $document->getActiveSheet()->insertNewRowBefore(($row), 1);
            $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
            $row++;
            $document->getActiveSheet()
                ->setCellValue('B'.$row, $i)
                ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES));
            $document->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray($styleArrayBold);
            $i++;
            $row++;
            $document->getActiveSheet()->insertNewRowBefore(($row), 1);
            $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
        }
        else{
        $prev_res=2;
        $document->getActiveSheet()
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['entry_qty'])
            ->setCellValue('J'.$row, $entry['entry_price'])
            ->setCellValue('K'.$row, '-'.$entry['entry_discount'])
            ->setCellValue('L'.$row, '=0.01*J'.$row.'*I'.$row.'*K'.$row);
        $document->getActiveSheet()->getStyle('B'.$row.':L'.$row)->applyFromArray($styleArrayUnBold);
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
        }
    };
    if ($prev_res===3) {
        $document->getActiveSheet()->removeRow($row-1)->removeRow($row-1);
        $row=$row-2;
    }
}
//Spare Parts
$entries=$db->query('SELECT * FROM service_calc_entries WHERE (entry_type=0 OR entry_type=2) AND entry_discount<>0 AND entry_related_id="'.$id.'"');
$document->getActiveSheet()->insertNewRowBefore(($row), 1);
$document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
if ($entries->num_rows > 0){
    $row++;
    $document->getActiveSheet()
            ->setCellValue('B'.$row, $i++)
            ->setCellValue('C'.$row, 'Spare parts and materials:');
    $document->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray($styleArrayBold);
    $row++;
    $document->getActiveSheet()->insertNewRowBefore(($row), 1);
    $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['entry_qty'])
            ->setCellValue('J'.$row, $entry['entry_price'])
            ->setCellValue('K'.$row, '-'.$entry['entry_discount'])
            ->setCellValue('L'.$row, '=0.01*J'.$row.'*I'.$row.'*K'.$row);
        $document->getActiveSheet()->getStyle('B'.$row.':L'.$row)->applyFromArray($styleArrayUnBold);
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
    }
}
//Other expenses
$entries=$db->query('SELECT * FROM service_calc_entries WHERE entry_type=4 AND entry_discount<>0 AND entry_related_id="'.$id.'"');
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
        $row++;
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i++)
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['entry_qty'])
            ->setCellValue('J'.$row, $entry['entry_price'])
            ->setCellValue('K'.$row, '-'.$entry['entry_discount'])
            ->setCellValue('L'.$row, '=0.01*J'.$row.'*I'.$row.'*K'.$row);
        $document->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray($styleArrayBold);
        $document->getActiveSheet()->getStyle('I'.$row.':L'.$row)->applyFromArray($styleArrayUnBold);
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
    }
}

//Последняя пустая строчка
$document->getActiveSheet()->removeRow($row);
//$document->getActiveSheet()->insertNewRowBefore(19, 1);
//$document->getActiveSheet()->mergeCells('C20:H20');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="CN'.$service['srv_cn_number'].'.xlsx"');
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