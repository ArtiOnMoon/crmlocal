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
$query= 'SELECT sales.*, our_companies.*, curr_name, countries.name as our_country, c1.name AS invoice_country, full_name, '
        . 'customers.client_of, customers.cust_full_name, customers.vat, customers.address,customers.address2 '
        . 'FROM sales '
        . 'LEFT JOIN currency ON sales.sales_currency=curr_id '
        . 'LEFT JOIN our_companies ON sales_invoice_from=our_companies.id '
        . 'LEFT JOIN customers ON sales_customer=cust_id '
        . 'LEFT JOIN countries ON countries.id = our_companies.our_country '
        . 'LEFT JOIN countries c1 ON c1.id = customers.country '
        . 'LEFT JOIN users ON sales.modified = uid '
        . 'WHERE sales_id = "'.$id.'"';
$result=$db->query($query);
if ($result->num_rows!==1){
   exit($query);
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
$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/CustomInv-PackingList.xlsx');
$document->setActiveSheetIndex(0);

//INVOICE TO
if($sale['sales_inv_instructions']==='1'){
    $invoice_to_name1=$sale['sales_inv_name1'];
    $sales_inv_addr1=$sale['sales_inv_addr1'];
    $sales_inv_addr2=$sale['sales_inv_addr2'];
    $sales_inv_country=$sale['invoice_country'];  
    $sales_inv_vat='VAT: '.$sale['sales_inv_vat'];    
} ELSE {
    $invoice_to_name1=$invoice_to['cust_full_name'];
    $invoice_to_name2='';
    $sales_inv_addr1=$invoice_to['InvoicingAddress'];
    $sales_inv_addr2=$invoice_to['InvoicingAddress2'];
    $sales_inv_country=$invoice_to['name']; 
    $sales_inv_vat='VAT: '.$invoice_to['vat']; 
}

//Shipped FROM
if($sale['sales_shipped_from']==='1'){
    $shipped_from_name=$sale['our_full_name'];
    $shipped_from_addr1=$sale['our_inv_addr'];
    $shipped_from_addr2=$sale['our_inv_addr2'];
    $shipped_from_country=$sale['our_country'];
    $shipped_from_vat='VAT: '.$sale['our_vat'];
}
ELSE {
    $shipped_from_name=$sale['sales_shipped_from_name'];
    $shipped_from_addr1=$sale['sales_shipped_from_addr1'];
    $shipped_from_addr2=$sale['sales_shipped_from_addr2'];
    $shipped_from_country=$sale['sales_shipped_from_country'];
    $shipped_from_vat=$sale['sales_shipped_from_vat'];
}
//Shipped TO
if($sale['sales_shipped_to']==='1' and $sale['sales_inv_instructions']==='0'){
    $shipped_to_name=$invoice_to['cust_full_name'];
    $shipped_to_addr1=$invoice_to['InvoicingAddress'];
    $shipped_to_addr2=$invoice_to['InvoicingAddress2'];
    $shipped_to_country=$invoice_to['name'];
    $shipped_to_vat=$invoice_to['vat'];
} elseif($sale['sales_shipped_from']==='1' and $sale['sales_inv_instructions']==='1'){
    $shipped_to_name=$sale['sales_inv_name1'];
    $shipped_to_addr1=$sale['sales_inv_addr1'];
    $shipped_to_addr2=$sale['sales_inv_addr2'];
    $shipped_to_country=$sale['invoice_country'];
    $shipped_to_vat='VAT: '.$sale['sales_inv_vat']; 
} ELSE {
    $shipped_to_name=$sale['sales_shipped_name'];
    $shipped_to_addr1=$sale['sales_shipped_addr1'];
    $shipped_to_addr2=$sale['sales_shipped_addr2'];
    $shipped_to_country=$sale['sales_shipped_country'];
    $shipped_to_vat=$sale['sales_shipped_vat'];
}

//Shipped ON
if ($sale['sales_ship_date']!='' and $sale['sales_shipped_on']!='')$shipped_on=$sale['sales_ship_date'].' by '.$sale['sales_shipped_on'];
elseif ($sale['sales_ship_date']=='' and $sale['sales_shipped_on']!='')$shipped_on=$sale['sales_shipped_on'];
elseif ($sale['sales_ship_date']!='' and $sale['sales_shipped_on']=='')$shipped_on=$sale['sales_ship_date'];
else $shipped_on='';

//Реквизиты и пр
$document->getActiveSheet()
    ->setCellValue('I2', $sale['sales_invoice'])
    ->setCellValue('I3', $sale['sales_date'])
    ->setCellValue('D4', $sale['our_full_name'])
    ->setCellValue('D5', $sale['our_inv_addr'])
    ->setCellValue('D6', $sale['our_inv_addr2'])
    ->setCellValue('D7', $sale['our_country'])
    ->setCellValue('D8', 'VAT: '.$sale['our_vat'])
    ->setCellValue('D9', htmlspecialchars_decode($shipped_from_name,ENT_QUOTES))
    ->setCellValue('D10', htmlspecialchars_decode($shipped_from_addr1,ENT_QUOTES))
    ->setCellValue('D11', htmlspecialchars_decode($shipped_from_addr2,ENT_QUOTES))
    ->setCellValue('D12', htmlspecialchars_decode($shipped_from_country,ENT_QUOTES))
    ->setCellValue('D13',$shipped_from_vat)
    ->setCellValue('I4', $invoice_to_name1)
    ->setCellValue('I5', $sales_inv_addr1)
    ->setCellValue('I6', $sales_inv_addr2)
    ->setCellValue('I7', $sales_inv_country)
    ->setCellValue('I8', $sales_inv_vat)
    ->setCellValue('I9', htmlspecialchars_decode($shipped_to_name,ENT_QUOTES))
    ->setCellValue('I10', htmlspecialchars_decode($shipped_to_addr1,ENT_QUOTES))
    ->setCellValue('I11', htmlspecialchars_decode($shipped_to_addr2,ENT_QUOTES))
    ->setCellValue('I12', htmlspecialchars_decode($shipped_to_country,ENT_QUOTES))
    //->setCellValue('I13', $shipped_to_vat)
    //->setCellValue('D19', $shipped_on)
    ->setCellValue('L3', $sale['sales_cust_po'])
    //->setCellValue('D21', $sale['sales_pay_terms'])
    //->setCellValue('I19', $sale['sales_awb'])
    ->setCellValue('L2', $sale['id'].'.'.$sale['sales_no'].$sale['client_of'])
    ->setCellValue('K23', $sale['curr_name']);
    //->setCellValue('B29', $sale['sales_vat_remarks'])
    //->setCellValue('D30', $sale['sales_delevery_terms']);

//PACKAGE
$package=$db->query('SELECT * FROM sales_package WHERE sales_pack_sale_id="'.$id.'"');
$row=14;
if ($package->num_rows > 0){
    while($pack=$package->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('D'.$row, $pack['sales_pack_box_no'])
            ->setCellValue('G'.$row, $pack['sales_pack_width'].'x'.$pack['sales_pack_depth'].'x'.$pack['sales_pack_height'])
            ->setCellValue('L'.$row, $pack['sales_pack_weight']);
        $row++;
        $i++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row+1), 1);
    }
}

//QUOTATION
$row+=5;
$i=1;
$entries=$db->query('SELECT sales_content.*,stnmc_commod_code,countries.code AS origin FROM sales_content '
        . 'LEFT JOIN stock_nmnc ON stnmc_id=scont_base_id '
        . 'LEFT JOIN countries ON stnmc_origin=countries.id '
        . 'WHERE scont_sale_id="'.$id.'" AND scont_box_no != ""');
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['scont_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['scont_box_no'])
            ->setCellValue('J'.$row, $entry['scont_qty'])
            ->mergeCells('C'.($row).':H'.($row))
            ->setCellValue('K'.$row, $entry['scont_price'])
            ->setCellValue('L'.$row, $entry['scont_price']*$entry['scont_qty']);
        $row++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row), 1)
            ->mergeCells('C'.$row.':F'.$row)
            ->setCellValue('C'.$row, 'Commodity Code: '.$entry['stnmc_commod_code'])
            ->setCellValue('G'.$row, 'Country of Origin: '.$entry['origin']);
        $row++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row), 1)
            ->mergeCells('D'.($row).':H'.($row))
            ->setCellValue('C'.$row, 'Seraial No. ')
            ->setCellValue('D'.$row, $entry['scont_serials']) ;
        if (strlen($entry['scont_serials'])>0){
            $document->getActiveSheet()->getRowDimension($row)->setRowHeight(ceil(strlen($entry['scont_serials'])/55)*15);  
            $document->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setWrapText(true);
        }
        $row++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row), 1)
            ->insertNewRowBefore(($row), 1);
        $document->getActiveSheet()->getRowDimension($row+1)->setRowHeight(15);
        $document->getActiveSheet()->getRowDimension($row)->setRowHeight(15);
        $row++;
        $i++;
    }
}
$document->getActiveSheet()
    ->removeRow(($row - 1), 1)
    ->removeRow(($row - 1), 1);


//ВЫВОД
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$sale['id'].'.'.$sale['sales_no'].$sale['client_of'].'_packing_list.xlsx"');
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