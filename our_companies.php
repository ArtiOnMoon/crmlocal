<?php
require_once 'functions/fns.php';
startSession();

if(check_access('acl_cust', 1)) {exit('Access denied.');}
$page_title = 'Our companies';
include 'header.php';
?>

<div id="wrap" onclick="cancel()"></div>
<div id="side_menu">
    <a class="knopka" href="#"  onclick="display('new_company')">New company</a>
    <span style="float: right;"> Fast search: <input type="search" id="service_search" placeholder = "Enter Description, PO, technician" oninput="fast_search()"></span>  
</div>
<div id="main_div_menu">
<?php

if($result->num_rows===0)exit('No companies'); 
   ?>
    <table id="service_rates" class="sortable" width="100%" rules="columns"  border="1px" cellspacing = "0" cellpadding="2px">
        <thead>
            <th>ID</th>
            <th>Name</th>
            <th>Full Name</th>
            <th>Address</th>
            <th>Invoicing address</th>
            <th>E-mail</th>
        </thead>
        <tbody>
<?php
$db =  db_connect();
$query='select * from our_companies where our_deleted=0';
$result=$db->query($query);
    while($row = $result->fetch_assoc()){
        echo '<tr>'
        . '<td class="num">'.view_our_company_link($row['id'],$row['id']).'</td>'
        . '<td>'.view_our_company_link($row['id'],$row['our_name']).'</td>'
        . '<td>'.$row['our_full_name'].'</td>'
        . '<td>'.$row['our_fact_addr'].' '.$row['our_fact_addr2'].'</td>'
        . '<td>'.$row['our_inv_addr'].' '.$row['our_inv_addr2'].'</td>'
        . '<td>'.$row['our_mail'].'</td>'
        . '</tr>';
}
?> 
        </tbody>
    </table>
</div>
<div id="new_company" class="hidden" style="width: 500px; height:500px;">
    <h1 align="center">New company</h1>
    <form name="company_form" action="/scripts/our_companies_add.php" method="POST">
    <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Name</b></td>
            <td><input type="text" maxlength="250" name="our_name" required style="width:95%;"></td>
        </tr>
        <tr>
            <td width="100"><b>Full name</b></td>
            <td><input type="text" maxlength="250" style="width:95%;" name="our_full_name" required></td>
        </tr>
        <tr>
            <td width="100"><b>Our VAT</b></td>
            <td><input type="text" maxlength="250" style="width:95%;" name="our_vat" required></td>
        </tr>
        <tr>
            <td width="100"><b>Address</b></td>
            <td><input type='text' style="width:95%;" maxlength="300" name="our_addr" required><br />
                <input type='text' style="width:95%;" maxlength="300" name="our_addr2" required><br />
            </td>
        </tr>
        <tr>
            <td width="100"><b>Invoicing address</b></td>
            <td><input type='text' style="width:95%;" maxlength="300" name="our_inv_addr" required><br />
                <input type='text' style="width:95%;" maxlength="300" name="our_inv_addr2" required><br />
            </td>
        </tr>
        <tr>
            <td width="100"><b>E-mail</b></td>
            <td><input type="text" maxlength="100" style="width:95%;" name="our_mail" required></td>
        </tr>
    </table> 
    <div align="right" width="100%" style="padding: 10px">
        <input type="submit" value="Add rate">
        <input type="button" value="Close" onclick="cancel()"> 
    </div>
    </form>
</div>

<script type="text/javascript" src="java/java_func.js"></script>