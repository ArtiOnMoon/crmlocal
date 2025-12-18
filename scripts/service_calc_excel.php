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
$query= 'SELECT service.*, vessels.vessel_name, our_details.pay_comment, our_companies.*,countries.name as country_name, c1.name as inv_country_name, curr_name, our_c1.our_name AS our_code, our_c1.our_short_name AS service_station '
        . 'FROM service '
        . 'LEFT JOIN vessels ON service.vessel_id=vessels.vessel_id '
        . 'LEFT JOIN our_companies ON srv_inv_from=our_companies.id '
        . 'LEFT JOIN our_companies our_c1 ON service_our_comp=our_c1.id '
        . 'LEFT JOIN countries ON countries.id = our_companies.our_country '
        . 'LEFT JOIN countries c1 ON c1.id = srv_inv_country '
        . 'LEFT JOIN currency ON service_currency=curr_id '
        . 'LEFT JOIN our_details ON our_details.id=srv_our_bank_details '
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
    $invoice_to = htmlspecialchars_decode($service['srv_inv_comp_name']);
    $invoice_to2 = htmlspecialchars_decode($service['srv_inv_comp_name2']);
    $inv_addr1 = htmlspecialchars_decode($service['srv_inv_addr1']);
    $inv_addr2 = htmlspecialchars_decode($service['srv_inv_addr2']);
    $country = $service['inv_country_name'];
    $vat=$service['srv_inv_vat'];
}
else{
    $invoice_to = htmlspecialchars_decode($customer['cust_full_name']);
    $inv_addr1 = htmlspecialchars_decode($customer['InvoicingAddress2']);
    $inv_addr2 = htmlspecialchars_decode($customer['InvoicingAddress']);
    $country = $customer['name'];
    $vat=$customer['vat'];
}
$inv_date = ($service['srv_inv_date']=='' ? date('d.m.Y',strtotime($service['service_date'])) : date('d.m.Y',strtotime($service['srv_inv_date'])));

$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/invoice_service_v4.xlsx');
$document->setActiveSheetIndex(0);

//OUR Bank_details
$query='SELECT * FROM our_details_sub WHERE details_id="'.$service['srv_our_bank_details'].'"';
$result=$db->query($query);
$row=25;
while($bank_det=$result->fetch_assoc()){
    $document->getActiveSheet()
            ->setCellValue('B'.$row, $bank_det['param_name'])
            ->setCellValue('E'.$row, $bank_det['param_value'])
            ->insertNewRowBefore(($row+1), 1);
    $row++;
}

//Реквизиты и пр
$document->getActiveSheet()
    ->setCellValue('B3', $service['our_full_name'])
    ->setCellValue('B4', $service['our_inv_addr'])
    ->setCellValue('B5', $service['our_inv_addr2'])
    ->setCellValue('B6', $service['country_name'])
    ->setCellValue('B7', 'VAT: '.$service['our_vat'])
    ->setCellValue('J6', $service['our_code'].' '.service_id_num($service['service_no'],$service['service_our_comp']))
    ->setCellValue('J7', 'mv '.$service['vessel_name'])
    ->setCellValue('J2', $service['srv_inv_number'])
    ->setCellValue('J4', $inv_date)
    ->setCellValue('B22', $service['pay_comment']);

$row=10; //Заполнение кастомера
if ($invoice_to2 !=''){
    $document->getActiveSheet()
        ->insertNewRowBefore(($row+1), 1)
        ->setCellValue('B'.$row++, htmlspecialchars_decode($invoice_to))
        ->setCellValue('B'.$row++, htmlspecialchars_decode($invoice_to2));
}
else $document->getActiveSheet()->setCellValue('B'.$row++, htmlspecialchars_decode($invoice_to));
$document->getActiveSheet()
    ->setCellValue('B'.$row++, htmlspecialchars_decode($inv_addr1))
    ->setCellValue('B'.$row++, htmlspecialchars_decode($inv_addr2))
    ->setCellValue('B'.$row++, $country)
    ->setCellValue('B'.$row++, 'VAT: '.$vat);

$document->getActiveSheet()
    ->setCellValue('H10','Customer PO Ref:')
    ->setCellValue('J10', $service['PO'])
    ->setCellValue('J11', $service['PO2'])
    ->setCellValue('H12','Payment Terms:')
    ->setCellValue('J12', $service['srv_pay_terms'])
    ->setCellValue('H14','Currency:')
    ->setCellValue('J14', $service['curr_name']);
$i=1;
$row+=4;

$document->getActiveSheet()
        ->setCellValue('C'.$row, 'Product Service Report №'.service_id_num($service['service_no'],$service['service_our_comp']));
$document->getActiveSheet()->getStyle('C'.$row)->applyFromArray($styleArrayBold);
$row++;
$document->getActiveSheet()->insertNewRowBefore(($row), 1);
$document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));

//CALCULATION
//Rates
$entries=$db->query('SELECT * FROM service_calc_entries WHERE (entry_type=3 OR entry_type=1) AND entry_related_id="'.$id.'"');
if ($entries->num_rows > 0){
    $prev_res=0;
    while($entry=$entries->fetch_assoc()){
        if($entry['entry_type']==='3'){
        //HEADER
            //if ($prev_res===3) {
                //Если предыдущий тоже заголовок, задать 0
                //$document->getActiveSheet()
                        //->setCellValue('I'.($row-1), 0)
                        //->setCellValue('J'.($row-1), 0)
                        //->setCellValue('K'.($row-1), '=J'.($row-1).'*I'.($row-1));
            //}
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
        //RATE
        $prev_res=1;
        $document->getActiveSheet()
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['entry_qty']);
        if ($service['srv_cn_flag']==='1'){
            $document->getActiveSheet()
            ->setCellValue('J'.$row, $entry['entry_price'])
            ->setCellValue('K'.$row, 0)
            ->setCellValue('L'.$row, '=J'.$row.'*I'.$row);
        }else{
            $document->getActiveSheet()
            ->setCellValue('J'.$row, $entry['entry_price'])
            ->setCellValue('K'.$row, $entry['entry_discount'])
            ->setCellValue('L'.$row, '=J'.$row.'*I'.$row.'*(1-(K'.$row.'/100))');
        }
        $document->getActiveSheet()->getStyle('B'.$row.':K'.$row)->applyFromArray($styleArrayUnBold);
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
        }
    };
}

//Spare Parts
//if ($prev_res===3) {
    //Если предыдущий тоже заголовок, задать 0
    //$document->getActiveSheet()
        //->setCellValue('I'.($row-1), 0)
        //->setCellValue('J'.($row-1), 0)
        //->setCellValue('K'.($row-1), '=J'.($row-1).'*I'.($row-1));
    //}
if ($prev_res===3) {
    //Если предыдущий тоже заголовок, затереть его
    $document->getActiveSheet()->removeRow($row-1)->removeRow($row-1);
        $row=$row-2;
        $i--;
    }
$entries=$db->query('SELECT * FROM service_calc_entries WHERE (entry_type=0 OR entry_type=2) AND entry_related_id="'.$id.'"');
$document->getActiveSheet()->insertNewRowBefore(($row), 1);
$document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
$row++;
$document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, 'Spare parts and materials:');
$document->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray($styleArrayBold);
$i++; $row++;
$document->getActiveSheet()->insertNewRowBefore(($row), 1);
$document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['entry_qty']);
        if ($service['srv_cn_flag']==='1'){
            $document->getActiveSheet()
            ->setCellValue('J'.$row, $entry['entry_price'])
            ->setCellValue('K'.$row, 0)
            ->setCellValue('L'.$row, '=J'.$row.'*I'.$row);
        }else{
            $document->getActiveSheet()
            ->setCellValue('J'.$row, $entry['entry_price'])
            ->setCellValue('K'.$row, $entry['entry_discount'])
            ->setCellValue('L'.$row, '=J'.$row.'*I'.$row.'*(1-(K'.$row.'/100))');
        }
        $document->getActiveSheet()->getStyle('B'.$row.':K'.$row)->applyFromArray($styleArrayUnBold);
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
    };
} else {
    //Стереть Spare parts
    $document->getActiveSheet()->removeRow($row-1)->removeRow($row-1);
        $row=$row-2;
        $i--;
}

//Other expenses
//Spare Parts
$entries=$db->query('SELECT * FROM service_calc_entries WHERE entry_type=4 AND entry_related_id="'.$id.'"');
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
        $row++;
        if ($service['srv_cn_flag']==='1'){
            $document->getActiveSheet()
                ->setCellValue('B'.$row, $i)
                ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES))
                ->setCellValue('I'.$row, $entry['entry_qty'])
                ->setCellValue('J'.$row, $entry['entry_price'])
                ->setCellValue('K'.$row, 0)
                ->setCellValue('L'.$row, '=J'.$row.'*I'.$row);
        } else {
            $document->getActiveSheet()
                ->setCellValue('B'.$row, $i)
                ->setCellValue('C'.$row, htmlspecialchars_decode($entry['entry_text'],ENT_QUOTES))
                ->setCellValue('I'.$row, $entry['entry_qty'])
                ->setCellValue('J'.$row, $entry['entry_price'])
                ->setCellValue('K'.$row, $entry['entry_discount'])
                ->setCellValue('L'.$row, '=J'.$row.'*I'.$row.'*(1-(K'.$row.'/100))');
        }
        $document->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray($styleArrayBold);
        $i++;
        $row++;
        $document->getActiveSheet()->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->mergeCells('C'.($row).':H'.($row));
    };
}

// NOTE
$document->getActiveSheet()->insertNewRowBefore(($row), 1);
$row++;
$document->getActiveSheet()
        ->setCellValue('B'.$row,'Note:')
        ->setCellValue('C'.$row,'Service have been carried out by "'.$service['service_station'].'" station')
        ;
$styleArray = ['font' => ['italic' => true,'bold'=>true]];
$document->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray($styleArray);
//Последняя пустая строчка
//$document->getActiveSheet()->removeRow($row);
//$document->getActiveSheet()->insertNewRowBefore(19, 1);
//$document->getActiveSheet()->mergeCells('C20:H20');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Invoice '.$service['srv_inv_number'].'.xlsx"');
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