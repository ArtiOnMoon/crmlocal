<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="icon" href="img/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/tabs.css">
    <link rel="stylesheet" type="text/css" href="css/selector.css">
    <link rel="stylesheet" type="text/css" href="css/calculation.css">
    <link rel="stylesheet" type="text/css" href="css/new_design.css">
    <link rel="stylesheet" type="text/css" href="java/jquery-ui-1.12.1/jquery-ui.min.css">
    <!--<<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>-->
    <!--<script type="text/javascript" src="js/materialize.min.js"></script> -->
    <script src="java/jquery-3.1.1.min.js"></script>
    <script src="java/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="java/selector.js"></script>
    <script type="text/javascript" src="java/java_func.js"></script>
    <script type="text/javascript" src="java/java_service.js"></script>
    <script type="text/javascript" src="java/java_equipment_func.js"></script>
    <script type="text/javascript" src="java/java_customers.js"></script>
    <script type="text/javascript" src="java/java_purchase.js"></script>
    <script type="text/javascript" src="java/java_vessels.js"></script>
    <script type="text/javascript" src="java/selector.js"></script>
    <script type="text/javascript" src="java/java_sales_func.js"></script>
    <script type="text/javascript" src="java/java_stock_nmnc.js"></script>
    <script type="text/javascript" src="java/java_stock_new.js"></script>
    <script type="text/javascript" src="java/invoice_func.js"></script>
    <script type="text/javascript" src="java/java_adm.js"></script>
    <title><?php echo $page_title; ?></title>
</head>
<body>
<header id="header">
    <div style="display:grid; grid-template-columns:auto 1fr auto auto;">
        <div id="logo_div">
            <!--<a href="index.php"><img  class="logo_image" src="A-Z Marine LOGO NEW.svg"></a>-->
            <a href="index.php"><img  class="logo_image" src="A-Z Marine LOGO NEW.svg"></a>
        </div>
        <?php
            if (!isset($_SESSION['valid_user'])){
                display_login_form();
                exit();
            }
        ?>
        <div id="title_div">
            <h2><?php echo $page_title ?></h2>
        </div> 
        <div id="auth">
            <b><?php echo $_SESSION['full_name']; ?></b><p>
            <a class="knopka" href="logout.php">Logout</a>
        </div>
        <div id="message">
            <!-- <a style="pointer-events: none;" href="messages.php" class="knopka">Messages (<?php //echo unread_messages() ?>)</a><p> -->
            <!--<a href="/questions.php" class="knopka">Questions</a> | -->
            <a href="/tasks.php" class="knopka" style="margin-bottom: 5px;">Tasks<?php echo check_tasks(); ?></a> <br>
            <a href="/doc_control.php" class="knopka">Documents (<?php echo document_control(); ?>) </a>
        </div>
    </div>
    <div id="main_menu">
        <table id="main_menu_table">
            <thead align="center">
                <th><a class="knopka2" href="service.php">Service</a></th>
                <th><a class="knopka2" href="graph.php">Graph</a></th>
                <th><a class="knopka2" href="sales.php">Sales</a></th>
                <th><a class="knopka2" href="purchase.php">Purchase</a></th>
                <th><a class="knopka2" href="adm.php">Administrative</a></th>
                <th class="text-center drop_menu"><a class="knopka2" href="stock_new.php">Stock &#9660;</a>
                    <div class="drop1">
                        <table class="drop_table">
                            <tr><td><a class="knopka2" href="stock_new.php">Stock</a></td></tr>
                            <tr><td><a class="knopka2" href="stock_nmnc.php">Nomenclature</a></td></tr>
                            <tr><td><a class="knopka2" href="stock_complects.php">Complects</a></td></tr>
                            <tr><td><a class="knopka2" href="stock_transfers.php">Transfers</a></td></tr>
                        </table>
                    </div>
                </th>
                <!--<th><a class="knopka2" href="order.php">For order</a></th> -->
                <th><a class="knopka2" href="customers.php">Customers</a></th>
                <th><a class="knopka2" href="vessels.php">Vessels</a></th>
                <th class="text-center drop_menu"><a class="knopka2" href="#">Databases &#9660;</a>
                    <div class="drop1">
                        <table class="drop_table">
                            <tr><td><a class="knopka2" href="equipment.php">Vessels equipment</a></td></tr>
                            <tr><td><a class="knopka2" href="service_rates.php">Service rates</a></td></tr>
                            <tr><td><a class="knopka2" href="our_companies.php">Our companies</a></td></tr>
                            <tr><td><a class="knopka2" href="manufacturers.php">Manufacturers</a></td></tr>
                            <tr><td><a class="knopka2" href="stock_list.php">Stocks list</a></td></tr>
                        </table>
                    </div>
                    </th>
                    <!--<th class="text-center"><a class="knopka2" href="documents.php">Documets and forms</a></th>-->
                    <th class="text-center drop_menu"><a class="knopka2" href="#">Invoices &#9660;</a>
                    <div class="drop1">
                        <table class="drop_table">
                            <tr><td><a class="knopka2" href="invoices.php">Invoices OUT</a></td></tr>
                            <tr><td><a class="knopka2" href="invoices_in.php">Invoices IN</a></td></tr>
                        </table>
                    </div>
                    </th>
               <!--<th class="text-center"><a class="knopka2" href="invoices.php">Invoices</a></th>-->
                </thead>
            </table>
    </div>
</header>
