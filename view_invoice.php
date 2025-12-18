<?php
require_once 'functions/fns.php';
require_once 'functions/invoice_func.php';
$invoice_id=(int)$_GET['invoice_id'];
if(check_access('acl_invoices', 1)) exit('Access denied.');
do_page_header('View invoice');
echo'<div id="main_div_menu">';

$db =  db_connect();
$query= 'select * from invoices, users where invoice_id = "'.$invoice_id.'" and invoice_modified=username';
$result=$db->query($query);
echo $db->error;
if ($result-> num_rows==1){
$row=$result->fetch_assoc();
?>
<h1> Invoice № <?php echo $row['invoice_num'];?></h1>
<div style="display:inline-block; width:100%;">
<form action="invoice_change.php" method="POST">
<div style="display:inline-block; width:50%">
<table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
    <tr>
        <td><strong>Invoice number</strong></td>
        <td><input type="text" maxlength="15" name="invoice_num" value="<?php echo $row['invoice_num'];?>"></td>
    </tr>
    <tr>
        <td><strong>Status</strong></td>
        <td><?php echo select_invoice_status($row['status']);?></td>
    </tr>
    <tr>
        <td><strong>Type</strong></td>
        <td><?php echo select_invoice_type($row['invoice_type']);?></td>
    </tr>
    <tr>
        <td><strong>Invoice date</strong></td>
        <td><input type="text" class="datepicker" name="invoice_date" placeholder="yyyy-mm-dd" value="<?php echo $row['invoice_date'];?>"></td>
    </tr>
    <tr>
        <td><strong>Payment terms</strong></td>
        <td><input type="text" name="inv_pay_terms" value="<?php echo $row['inv_pay_terms'];?>"></td>
    </tr>
    <tr>
        <td><strong>Statement reference</strong><td>
            <textarea cols="100" rows="3" name="statement_ref" style="resize: none;"><?php echo $row['statement_ref']; ?></textarea></td>
    </tr>
    <tr>
        <td><strong>Company</strong></td>
        <td><?php echo select_customer($row['customer_id']);?></td>
    </tr>
    <tr>
        <td><strong>Currency</strong></td>
        <td><?php echo select_currency(htmlspecialchars_decode($row['currency']));?></td>
    </tr>
    <tr>
        <td><strong>Invoice total</strong></td>
        <td><input type="text" name="invoice_total" maxlength=11 value="<?php echo $row['invoice_total'];?>"></td>
    </tr>
    <tr>
        <td><strong>Received</strong></td>
        <td><input type="text" name="invoice_received" maxlength=11 value="<?php echo $row['invoice_received'];?>"></td>
    </tr>
    <tr>
        <td><strong>Balance</strong></td>
        <td><strong><?php echo ($row['invoice_total']-$row['invoice_received']);?></strong></td>
    </tr>
    <tr>
        <td><strong>Note</strong></td><td>
            <textarea cols="100" rows="5" name="invoice_note" style="resize: none;"><?php echo $row['invoice_note']; ?></textarea></td>
    </tr>
    <tr>
        <td><span style="font-style: italic">Last modified by:</span></td>
        <td><span style="font-style: italic"><?php echo $row['full_name']; ?></span></td>
    </tr>
    <tr>
        <td></td>
        <td><span style="font-style: italic">at <?php echo $row['invoice_modified_date']; ?></span></td>
    </tr>
    <tr>
       <td colspan="2" align="center"><label><font color="red"><strong>Delete</strong></font><input type="checkbox" name="inv_deleted" value="1"
       <?php if ($row['inv_deleted']==1) echo ' checked';?>></label></td>
    </tr>
</table>
<input type="hidden" name="invoice_id" value="<?php echo $_GET['invoice_id']?>">
<div align="center" width="100%" style="padding: 10px">
    <input type="submit" value="Apply changes">
</div>
</form>
</div>

<!-- FILES -->    
<div style="position:absolute; right:0px; top:0">
<h2 align="center">Uploaded files</h2>
<?php
$dir='./invoice_files/'.$row['invoice_creation_year'].'/'.$invoice_id.'/';
if (!is_dir($dir))
{
    mkdir($dir,0777,true);
}
$list=array_slice(scandir($dir),2);
?>
<form action="upload_invoice.php" method="POST" multipart="" enctype="multipart/form-data">
<table width="500px" border="0" cellspacing = "0" cellpadding="2px">
    </thead>
    <th width="100px">File</th>
    <th width="100px">Download</th>
    <th width="100px">Delete</th>
    </thead>
<?php
foreach ($list as $file){
?>
    <tr><td><?php echo $file; ?></td><td><a class="knopka" href="#" onclick="download_file('<?php echo $file; ?>')">Download file</a></td>
        <td><a class="knopka" href="#" onclick="delete_file('<?php echo $file; ?>')">Delete file</a></td></tr>
<?php
}
?>
</table>
<br>
        <input type="file" name="invoice_files[]" multiple>
        <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
        <input type="hidden" name="invoice_creation_year" value="<?php echo $row['invoice_creation_year'];?>">
        <input type="submit" value="Add files">
</form>
<form id="blankform" action="invoice_upl_downl.php" method="POST">
    <input type="hidden" name="file_name" id="file_name">
    <input type="hidden" name="file_id" id="file_id" value="<?php echo $invoice_id;?>">
    <input type="hidden" name="file_action" id="file_action">
    <input type="hidden" name="invoice_creation_year" value="<?php echo $row['invoice_creation_year'];?>">
</form>
</div>


</div>
<script src="/java/java_func.js"></script>
<script>
function download_file(file){
document.getElementById('file_name').value=file;
document.getElementById('file_action').value='download';
document.forms.blankform.submit();
}
function delete_file(file){
if (!confirm("Delete this file?")) return false;
document.getElementById('file_name').value=file;
document.getElementById('file_action').value='delete';
document.forms.blankform.submit();
return true;
}
function change_content(){
    var data=new Array;
    var content=new Array;
    var table = document.getElementById('purchase_content');
    var tr=table.getElementsByTagName('tr');
    if (tr.length>1){
        for (var i = 1; i < tr.length; i++) { 
            input=tr[i].getElementsByTagName('input');
            select=tr[i].getElementsByTagName('select');
            data[0]=input[0].value;
            data[1]=input[1].value;
            data[2]=input[2].value;
            data[3]=select[0].value;
            content[i-1]=JSON.parse(JSON.stringify(data));
         }
        content =JSON.stringify(content);
    }
    else content='NULL';
    document.getElementById('content').value =content;
    return true;
}
</script>
<?php
};


