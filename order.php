<?php
require_once 'functions/db.php';
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/order_func.php';

startSession();
if(check_access('acl_stock', 1)) exit('Access denied.');
do_page_header('For order','For order');
?>
<div id="wrap" onclick="cancel()"></div>
<div id="invis_wrap" onclick="close_menu()"></div>
<div id="window" class="hidden" style="width:90%;height:90%;"></div>
<div id="side_menu">
    <a class="knopka" href="#"  onclick="display('new_order_item')">New item</a>
<!-- FILTERS BLOCK 
    <span class="hidden_conteiner" onclick="display_menu('hidden_div','list_sign')">Show filters <span id="list_sign" class="sign">&#9660</span></span>
    <div class="hidden_div" id="hidden_div">

    </div>
-->
<!-- END FILTERS BLOCK -->
    Keyword: <input type="search" id="stock_search" placeholder = "Enter keyword" onkeydown="stock_search_enter(event)">
    <button class="button" onclick="display_stock()">Filter</button> 
    <button class="button" onclick="reset_filter()">Reset filter</button>
    <div style="float:right; padding:3px;"><label><input id="auto_refresh" type="checkbox" checked="true">Auto refresh</label></div>
</div>
<div id="new_order_item" class="hidden" style="width:900px;">
    <form name="order_add_new" method="POST" action="order_add_new.php" onsubmit="">
        <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
            <tr>
                <td><strong>Type</strong></td>
                <td><?php echo select_order_type();?></td>
            </tr>
            <tr>
                <td><strong>PO/SO №</strong></td>
                <td><input type="text" required name="order_link" maxlength="10"></td>
            </tr>
            <tr>
                <td><strong>Date</strong></td>
                <td><input type="text" required class="datepicker" name="order_date" placeholder="yyyy-mm-dd" maxlength="10" value="<?php echo date('Y-m-d');?>"></td>
            </tr>
            <tr>
                <td><strong>Urgency</strong></td>
                <td><?php echo select_urgency();?></td>
            </tr>
            <tr>
                <td><strong>Note</strong></td>
                <td><textarea name="order_note" maxlength="1000" rows="3" cols="50"></textarea></td>
            </tr>
        </table>
        <p></p>
        <table width="100%">
            <thead>
                <th>P/n</th><th>Description</th><th>Quantity</th><th>Delete</th>
            </thead>
            <tbody id="new_order_table">
            </tbody>
        </table>
        <input type="button" class="button" style="float:right;" onclick="new_order_line()" value="New line">
        <p></p>
        <div align="center" width="100%"><input type="submit" class="button" value="Add"></div>
    </form>
    <table style="display:none;">
        <tr id="first_tr">
            <td><input type="text" name="order_pn[]" maxlength="50" required style="width:100%;"></td>
            <td><input type="text" name="order_descr[]" maxlength="500" required style="width:100%;"></td>
            <td><input type="text" name="order_qnt[]" maxlength="6" required style="width:100%;"></td>
            <td><a class="knopka" href="#" onclick="delete_row(this)">Delete</a></td>
        </tr>
    </table>
</div>

<!-- MAIN DIV-->
<div id="main_div_menu"></div>

<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/jquery.floatThead.js"></script>
<script type="text/javascript" src="java/java_for_order.js"></script>
<script>
document.addEventListener("DOMContentLoaded", show_order_table('<?php echo$_SESSION['order_page'];?>'));
document.getElementById("wrapper").addEventListener("scroll",function(){
    document.getElementById("wrapper").style.height=document.getElementById("multiinsert_table").style.height;
    var translate = "translate(0,"+this.scrollTop+"px)";
    this.querySelector("thead").style.transform = translate;
});
</script>