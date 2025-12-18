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
$query= 'SELECT sales.*, our_companies.*,our1.id as our_id, curr_name, countries.name as our_country, c1.name AS invoice_country, full_name, '
        . 'customers.client_of, customers.cust_full_name, customers.vat, customers.InvoicingAddress as address,customers.InvoicingAddress2 as address2 '
        . 'FROM sales '
        . 'LEFT JOIN currency ON sales.sales_currency=curr_id '
        . 'LEFT JOIN our_companies ON sales_invoice_from=our_companies.id '
        . 'LEFT JOIN our_companies our1 ON sales_our_comp=our1.id '
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

//Открытие Шаблона
$document=IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/templates/sales_delivery_note.xlsx');
$document->setActiveSheetIndex(0);

//OUR Bank_details
$query='SELECT * FROM our_details_sub WHERE details_id="'.$sale['sales_our_bank_details'].'"';
if(!$result=$db->query($query)) echo 'Bank details error. ',$query;
$row=33;
while($bank_det=$result->fetch_assoc()){
    $document->getActiveSheet()
            ->setCellValue('B'.$row, $bank_det['param_name'])
            ->setCellValue('F'.$row, $bank_det['param_value'])
            ->insertNewRowBefore(($row+1), 1)
            ->mergeCells('B'.($row+1).':E'.($row+1))
            ->mergeCells('F'.($row+1).':K'.($row+1));
    $row++;
}
//Invoice to
if($sale['sales_invoice_to_flag']==='0'){
    $invoice_to_name1=$sale['cust_full_name'];
    $invoice_to_addr1=$sale['address'];
    $invoice_to_addr2=$sale['address2'];
    $invoice_to_country=$sale['invoice_country'];  
    $invoice_to_vat=$sale['vat'];    
} ELSE {
    // запрос Customer Invoice_TO
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$sale['sales_invoice_to'];
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Customer not found. Code 1.');
    } else $invoice_to=$result->fetch_assoc();
    $invoice_to_name1=$invoice_to['cust_full_name'];
    $invoice_to_addr1=$invoice_to['InvoicingAddress'];
    $invoice_to_addr2=$invoice_to['InvoicingAddress2'];
    $invoice_to_country=$invoice_to['name'];
    $invoice_to_vat=$invoice_to['vat'];
}
//Shipped TO
if($sale['sales_shipped_to_flag']==='0'){
    $shipped_to_name=$invoice_to_name1;
    $shipped_to_addr1=$invoice_to_addr1;
    $shipped_to_addr2=$invoice_to_addr2;
    $shipped_to_country=$invoice_to_country;
    $shipped_to_vat=$invoice_to_vat;
} elseif($row['sales_shipped_to_flag']==='1'){
    // запрос Customer SHIPPED_TO
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$sale['sales_shipped_to'];
    $result=$db->query($query);
    if ($result-> num_rows!==1){
        exit('Customer not found. Code 2.');
    } else $shipped_to=$result->fetch_assoc();
    $shipped_to_name=$shipped_to['cust_full_name'];
    $shipped_to_addr1=$shipped_to['InvoicingAddress'];
    $shipped_to_addr2=$shipped_to['InvoicingAddress2'];
    $shipped_to_country=$shipped_to['name'];
    $shipped_to_vat=$shipped_to['vat']; 
} ELSE { //sales_shipped_to_flag===2
    $shipped_to_name=$sale['sales_shipped_name'];
    $shipped_to_addr1=$sale['sales_shipped_addr1'];
    $shipped_to_addr2=$sale['sales_shipped_addr2'];
    $shipped_to_country=$sale['sales_shipped_country'];
    $shipped_to_vat=$sale['sales_shipped_vat'];
}
//Shipped FROM
if($sale['sales_shipped_from_flag']==='0'){
    $shipped_from_name=$sale['our_full_name'];
    $shipped_from_addr1=$sale['our_inv_addr'];
    $shipped_from_addr2=$sale['our_inv_addr2'];
    $shipped_from_country=$sale['our_country'];
    $shipped_from_vat=$sale['our_vat'];
}elseif ($sale['sales_shipped_from_flag']==='1'){
    // запрос Customer SHIPPED_FROM
    $query = 'SELECT cust_short_name, cust_full_name,vat, countries.name, InvoicingAddress, InvoicingAddress2, vat '
        . 'FROM customers '
        . 'LEFT JOIN countries ON countries.id = customers.country '
        . 'WHERE cust_id='.$sale['sales_shipped_from'];
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
    $shipped_from_name=$sale['sales_shipped_from_name'];
    $shipped_from_addr1=$sale['sales_shipped_from_addr1'];
    $shipped_from_addr2=$sale['sales_shipped_from_addr2'];
    $shipped_from_country=$sale['sales_shipped_from_country'];
    $shipped_from_vat=$sale['sales_shipped_from_vat'];
}

//Реквизиты и пр
$document->getActiveSheet()
    ->setCellValue('D2', $sale['id'].'.'.$sale['sales_no'].$sale['client_of'])
    ->setCellValue('I2', $sale['sales_invoice_date'])
    ->setCellValue('B6', $sale['our_full_name'])
    ->setCellValue('B7', $sale['our_inv_addr'])
    ->setCellValue('B8', $sale['our_inv_addr2'])
    ->setCellValue('B9', $sale['our_country'])
    ->setCellValue('B10',$sale['our_vat'])
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
    ->setCellValue('I20', $sale['sales_ship_date'])
    ->setCellValue('D19', $sale['sales_cust_po'])
    ->setCellValue('D20', $sale['sales_pay_terms'])
    ->setCellValue('I21', trim($sale['sales_shipped_on'].' '.$sale['sales_awb']))
    ->setCellValue('I19', $sale['id'].'.'.$sale['sales_no'].$sale['client_of'])
    ->setCellValue('D21', $sale['curr_name'])
    ->setCellValue('B29', $sale['sales_vat_remarks']);
    //->setCellValue('D30', $sale['sales_delevery_terms']);


//QOUTATION
$row=26;
$i=1;
$entries=$db->query('SELECT * FROM sales_content WHERE scont_sale_id="'.$id.'" AND scont_box_no != ""');
if ($entries->num_rows > 0){
    while($entry=$entries->fetch_assoc()){
        $document->getActiveSheet()
            ->setCellValue('B'.$row, $i)
            ->setCellValue('C'.$row, htmlspecialchars_decode($entry['scont_text'],ENT_QUOTES))
            ->setCellValue('I'.$row, $entry['scont_cfm_qty']);
        if ($sale['sales_cn_flag']==='0'){
            $document->getActiveSheet()
            ->setCellValue('J'.$row, $entry['scont_price']*(1-$entry['scont_discount']/100))
            ->setCellValue('K'.$row, $entry['scont_price']*(1-$entry['scont_discount']/100)*$entry['scont_cfm_qty']);
        }else{
            $document->getActiveSheet()
            ->setCellValue('J'.$row, $entry['scont_price'])
            ->setCellValue('K'.$row, $entry['scont_price']*$entry['scont_cfm_qty']);
        }
        //Поиск SN
        $query='SELECT stock_serial FROM stock_new WHERE stock_nmnc_id="'.$entry['scont_base_id'].'" AND stock_so_type=1 AND stock_so_comp="'.$sale['id'].'" AND stock_so="'.$sale['sales_no'].'"';
        $serials=$db->query($query);
        $sn_list='';
        while($serial=$serials->fetch_assoc()){
            $sn_list.=$serial['stock_serial'].', ';
        }
        $sn_list = substr($sn_list,0,-2);
        $row++;
        $document->getActiveSheet()
            ->insertNewRowBefore(($row), 1)
            ->mergeCells('C'.($row).':H'.($row))
            ->setCellValue('C'.$row, 'S/N:'.$sn_list);
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
if ($sale['sales_invoice_note']!==''){
    $document->getActiveSheet()
        ->insertNewRowBefore(23, 1)
        ->mergeCells('C23'.':H23')
        ->setCellValue('B23', 'Note:')
        ->setCellValue('C23', $sale['sales_invoice_note'])
        ->getRowDimension('23')->setRowHeight(13);
    $document->getActiveSheet()->getStyle('C23')->applyFromArray($styleArrayUnBoldItalic);
}

//ВЫВОД
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$sale['id'].'.'.$sale['sales_no'].$sale['client_of'].'_Delivery_note.xlsx"');
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