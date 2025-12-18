<?php
require_once './functions/main.php';
require_once './functions/auth.php';
require_once './functions/db.php';
require_once './functions/stock_fns.php';
startSession();
if(check_access('acl_stock', 1)) exit('Access denied.');
require './vendor/autoload.php';
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

do_page_header('Stock import');
?>
<form action="./scripts/stock_import.php" method="POST">
    <div id="main_div_menu" style="height:calc(100vh - 250px);overflow:auto;">
<?php
if ($_FILES['userfile']['size']==0) exit('File not found.');
if ($_FILES['userfile']['type']!=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') exit('<span class="redtext">Incorrect file type.</span>');
$spreadsheet=IOFactory::load($_FILES['userfile']['tmp_name']);
$spreadsheet->setActiveSheetIndex(0);

//Find end of file
$i=0;
while ($cellValue!=='*END_OF_FILE*'){
    if ($i>=1000) exit('Error<br>Too long data or incorrect file');
    $i++;
    $cellValue = $spreadsheet->getActiveSheet()->getCell('A'.$i)->getValue();
}

$dataArray = $spreadsheet->getActiveSheet()
    ->rangeToArray(
        'A5:M'.($i-1),     // The worksheet range that we want to retrieve
        NULL,        // Value that should be returned for empty cells
        false,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
        TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
        TRUE         // Should the array be indexed by cell row and cell column
    );
?><div style="overflow:visible"><table width="100%">
<thead><th>Base item</th><th>Condition</th><th>Status</th>
<th>Serial</th><th>Date</th>
<th>Supplier code</th><th>Stock</th><th>Place</th><th>Net</th>
<th>Currency</th><th>Note</th><th>P/O</th><th>S/O</th>
</thead><tbody>
    <?php
$data=get_stock_category_list();
$curr_data= get_currency_list();
foreach ($dataArray as $subArr) {
    echo'<tr>'
    . '<td><input type="hidden" name="stock_nmnc_id[]" value="'.$subArr['A'].'">'.view_stock_nmnc($subArr['A']).'</td>'
    . '<td>',select_condition($subArr['B'],0,'name="stock_condition[]"'),'</td>'
    . '<td>',select_stock_stat($subArr['C'],0,'name="stock_status[]"'),'</td>'
    . '<td><input name="stock_serial[]" size="15" value="'.$subArr['D'].'"></td>'
    . '<td><input name="stock_date_receipt[]" class="datepicker" size="10" maxlength="10" value="'.$subArr['E'].'"></td>'
    . '<td>',select_customer($subArr['F'],0,'name="stock_supplier[]" class="short_select"','splr'),'</td>'
    . '<td>',select_stock($subArr['G'],'name="stock_stock_id[]"'),'</td>'
    . '<td><input name="stock_place[]" size="10" value="'.$subArr['H'].'"></td>'
    . '<td><input name="stock_price[]" size="10" value="'.$subArr['I'].'"></td>'
    . '<td>'.select_currency2($curr_data, $subArr['J'], $headers='name="stock_currency[]"').'</td>'
    . '<td><input name="stock_note[]" value="'.$subArr['K'].'"></td>'
    . '<td><input name="stock_po[]" size="10" value="'.$subArr['L'].'"></td>'
    . '<td><input name="stock_so[]" size="10" value="'.$subArr['M'].'"></td>'
    . '';
    echo '</tr>';
}
?>
</tbody>
</table>
</div>
<input type="submit" class="button" value="Add to stock" style="padding: 10px; margin:10px">
</div>
</form>

<script type="text/javascript" src="java/java_func.js"></script>
