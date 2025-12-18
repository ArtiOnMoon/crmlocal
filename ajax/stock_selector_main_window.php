<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../functions/selector.php';
require_once '../functions/stock_fns.php';
?>
<style>
    .selector_stock_container{
        width:1024px;
        height:90%;
        display:grid;
        grid-template-rows: auto 1fr auto;
        overflow:hidden;
    }
    .selector_stock_header{
        grid-row: 1;
        grid-column: 1;
    }
    .selector_stock_body{
        grid-row: 2;
        grid-column: 1;
    }
    .selector_stock_footer{
        border-top:1px solid black;
        grid-row: 3;
        grid-column: 1;
        padding: 10px;
        text-align: center;
    }
    .selector_stock_menu{
        background: #EEE;
        padding:10px;
    }
</style>
<div class="window_internal selector_stock_container">
    <div class="selector_stock_header">
        <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this);">&#10006;</a></div>
        <h2 align="center">Select from stock</h2>
        <div class="selector_stock_menu">
            <b>Class</b> <?php echo select_stock_class(0,1,'class="stock_selector_class"');?>
            <b>Manufacturer</b> <?php echo select_manufacturer(0,'class="stock_selector_maker"',1);?>
            <b>P/N</b> <input type="search" class="stock_selector_pn">
            <b>PO</b> <input type="search" class="stock_selector_po">
            <a class="knopka" href="#" onclick="stock_selector_subwindow_load(this)">Show results</a>
        </div>
    </div>
    <div class="stock_selector_sub_form selector_stock_body">

    </div>  
    <div class="selector_stock_footer">
        <a class="knopka green_button selector_cstock_confirm_button" href="#">Confirm selection</a>
        <a class="knopka" href="#" onclick="window_close(this);">Cancel</a> 
    </div>
</div>