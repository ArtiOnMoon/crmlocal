<?php
require_once 'functions/fns.php';
require_once 'functions/stock_fns.php';
require_once 'functions/selector.php';
startSession();
if(check_access('acl_stock', 1)) exit('Access denied.');

$comp_list = get_our_companies_list(1);

$page_title = 'Stock';
include 'header.php';

?>
<!--<div id="wrap" onclick="cancel()"></div>
<div id="window" class="hidden" style="width:90%;height:90%;"></div>-->
<div id="side_menu">
    <a class="knopka" href="#" onclick="stock_new()">New stock item</a>
    <a class="knopka" href="#" onclick="stock_multi_insert()">Multi insert</a>
    <a class="knopka" href="#" onclick="reset_filter()">Reset all filters</a>
    <label>Search <input type="search" id="stock_gobal_search" onchange="show_stock_new_table();" placeholder="PN, model, description"></label>
    
    Receipt date <input size="10" type="text" class="datepicker" id="rdate_start" onchange="show_stock_new_table()" value=""> 
    - <input size="10" type="text" class="datepicker" id="rdate_end" onchange="show_stock_new_table()">
    Sale date<input size="10" type="text" class="datepicker" id="sdate_start" onchange="show_stock_new_table()" value=""> 
    - <input size="10" type="text" class="datepicker" id="sdate_end" onchange="show_stock_new_table()">
    
    <div style="float:right; padding:3px;"><label><input id="auto_refresh" type="checkbox" checked="true">Auto refresh</label></div>
</div>

<main id="main_div_menu"></main>

<!-- Multi insert -->
<div id="multiinsert" class="hidden" style="width: 1280px;height:80%;overflow:auto;">
<div class="close_button_div"><a class="close_button" href="#" onclick="cancel();">&#10006;</a></div>
<form id="multi_insert" method="POST" action="scripts/stock_multi_insert.php" onsubmit="return multiple_stock_insert()">
    <h2 align="center">Multiple insert</h2>
        <table width="100%">
            <tr>
                <td><b>Owner</b></td><td><?php select_our_company('', 'required name="stock_our_company"')?></td>
                <td><b>Date of receipt</b></td><td><input type="text" name="date_receipt" required class="datepicker" value="<?php echo date('Y-m-d');?>"></td>
                <td><b>Stock</b></td><td><?php echo select_stock();?></td>
                <td><b>Purchase order</b></td><td><?php echo select_our_company2($comp_list,$row['stock_po_comp'],'name="stock_po_comp"'); ?><input type="text" size="10"  name="stock_po"></td>
                <td><b>Sales order</b></td><td><?php echo select_our_company2($comp_list,$row['stock_so_comp'],'name="stock_so_comp"'); ?><input type="text" size="10"  name="stock_so"></td>
            </tr>
        </table>
<p>
<div class="multiinsert_conteiner" onkeydown="enter_catch(event)">
    <div class="multiinsert_line block_div2">
        <div class="calc_fancy_div"><b>Base item</b> <?php echo selector('stock_nmnc','name="stock_nmnc_id[]"');?></div>
        <div class="calc_fancy_div"><b>Status</b> <?php echo select_stock_stat(0,0,$headers='onchange="select_control(this)" name="stock_status[]"'); ?></div>
        <div class="calc_fancy_div"><b>Condition</b> <?php echo select_condition('0','0', 'name="stock_condition[]" onchange="select_control(this)"');?></div>
        <div class="calc_fancy_div"><b>Supplier</b> <?php echo selector('customers','name="stock_supplier[]"');?></div>
        <div class="calc_fancy_div"><b>Place</b> <input type="text" size="8" name="stock_place[]"></div>
        <div class="calc_fancy_div"><b>Serial</b> <input class="input_serial" type="text" name="stock_serial[]" size="12"></div>
        <a class="knopka" style="float:right;" onclick="add_insert(this)" href="#">New line &#8595</a>
        <br>
        <div class="calc_fancy_div"><b>Currency</b> <?php echo select_currency2(get_currency_list(), 0, 'name="stock_currency[]" required onchange="select_control(this)"');?></div>
        <div class="calc_fancy_div"><b>Price</b> <input type="number" step="0.01" style="width:65px" name="stock_price[]"></div>
        <div class="calc_fancy_div"><b>Freight</b> <input type="number" step="0.01" style="width:65px" name="stock_freight[]"></div>
        <div class="calc_fancy_div"><b>Warranty</b> <input type="text" size="8" class="datepicker" name="stock_warranty_to[]"></div>
        <div class="calc_fancy_div"><b>Complect</b> <input type="text" size="8" name="stock_compl_id[]"></div>
        <div class="calc_fancy_div"><b>Note</b> <input type="text" name="stock_note[]" maxlength="300" size="20"></div>
        <div class="calc_fancy_div"><b>CCD</b> <input type="text" name="stock_ccd[]" maxlength="300" size="20"></div>
        <a class="knopka" style="float:right;" href="#" onclick="delete_multi_line(this)">Delete</a>
    </div>
</div>
    <div align="center">
        <br><br>
        <input type="submit" class="green_button" value="Add to stock">
        <input type="button" value="Close" onclick="cancel()">
    </div>
</form>
</div>
<!-- END Multi insert -->

<!-- STOCK IMPORT-->
<!--<div id="import" class="hidden" style="text-align:center;">
    <h2>Import from file</h2>
    <div class="block_div"><a href="public/Stock import form.xlsx">Stock import form.xlsx</a></div>
    <form enctype="multipart/form-data" action="stock_import_preview.php" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000">
        Import from: <input required name="userfile" type="file">
        <p>
        <input type="submit" value="Add document" onclick="return check_file(this)">
        <input type="button" value="Close" onclick="cancel()">
        </p>
</form>
<p>
Maximum length: 1000 rows.
</p>
</div>-->
<!--END IMPORT -->
<?php include 'footer.php';?>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_service.js"></script>
<script type="text/javascript" src="java/java_stock_new.js"></script>
<script type="text/javascript" src="java/java_stock_nmnc.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script type="text/javascript" src="java/java_sales_func.js"></script>
<script type="text/javascript" src="java/java_purchase.js"></script>
<script type="text/javascript" src="java/java_stock_complect.js"></script>
<script>
document.addEventListener("DOMContentLoaded", show_stock_new_table('<?php echo$_SESSION['stock_page'];?>'));
function check_all(){
    var inputs=document.getElementsByClassName('service_filter');
    for (var i=0;i<inputs.length;i++){
          if (inputs[i].checked==false){
              $(".service_filter").prop("checked",true);
              return;
          }
    }
    $(".service_filter").prop("checked",false);
}
</script>