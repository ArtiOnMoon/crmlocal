<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
startSession();
security();
$id=clean($_GET['id']);
$query= 'select * from our_companies where id="'.$id.'"';
$db =  db_connect();

$page_title = 'Our companies';
include 'header.php';

if(check_access('acl_cust', 1)) exit('Access denied.');
?>
<style>
#grid{
    display:grid;
    grid-template-rows: 1fr 1fr;
    grid-template-columns: 1fr 1fr;
}
</style>
<div id="main_div_menu">
    <div id="wrap" onclick="cancel()"></div>
    <div id="window" class="hidden" style="width:40%;height:60%;"></div>
<?php
$result=$db->query($query);
if ($result->num_rows===0){
    exit('Not found');
}
$row=$result->fetch_assoc();
if ($row['our_deleted']==='1') exit('Record was deleted.');
?>
<h1><?php echo $id; ?></h1>
<div id="grid">
    <form action="/scripts/our_companies_change.php" method="POST">
    <table width="100%" border="0px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Company name</b></td>
            <td><input type="text" name="our_name" maxlength="250" required value="<?php echo $row['our_name']; ?>"></td>
        </tr>
        <tr>
            <td width="100"><b>Full name</b></td>
            <td><input type="text" name="our_full_name" maxlength="250" required value="<?php echo $row['our_full_name']; ?>"></td>
        </tr>
        <tr>
            <td width="100"><b>VAT</b></td>
            <td><input type="text" name="our_vat" maxlength="20" required value="<?php echo $row['our_vat']; ?>"></td>
        </tr>
        <tr>
            <td width="100"><b>E-mail</b></td>
            <td><input type="text" name="our_mail" maxlength="100" required value="<?php echo $row['our_mail']; ?>"></td>
        </tr>
        <tr>
            <td width="100"><b>Fact address</b></td>
            <td>
                <input type="text" name="our_fact_addr" maxlength="300" value="<?php echo $row['our_fact_addr']; ?>"><br />
                <input type="text" name="our_fact_addr2" maxlength="300" value="<?php echo $row['our_fact_addr2']; ?>">
            </td>
        </tr>
        <tr>
            <td width="100"><b>Invoicing address</b></td>
            <td>
                <input type="text" name="our_inv_addr" maxlength="300" value="<?php echo $row['our_inv_addr']; ?>"><br />
                <input type="text" name="our_inv_addr2" maxlength="300" value="<?php echo $row['our_inv_addr2']; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2"><label>Delete<input type="checkbox" name="delete" value="1"></label></td>
        </tr>
    </table> 
    <input type="hidden" name="id" value="<?php echo $id;?>">
    <div align="center" width="100%" style="padding: 10px">
        <input type="submit" value="Change">
    </div>
</form>
    <table width="100%" border="0px" cellspacing = "0" cellpadding="2px">
        <thead>
        <th>Name</th><th>Bank details</th><th></th>
        </thead>
<?php
$query='SELECT * FROM our_details WHERE our_details.our_comp_id="'.$id.'"';
$result=$db->query($query);
WHILE ($row=$result->fetch_assoc()){
    echo '<tr><td>'.$row['name'].'</td><td>'.$row['our_bank_details'].'</td><td><button class="button" onclick="display_edit('.$row['id'].')">Edit</button></td></tr>';
}
?>
        <tr align="center" onclick="display('new_details')"><td rowspan="3"><input type="button" value="New bank details"></td></tr>
    </table>
</div>
</div>

<div id="new_details" class="hidden" style="width: 500px; height:500px;">
    <h1 align="center">New bank details</h1>
    <form action="scripts/our_comp_details_add.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id;?>">
        <table width="100%" class="fancy_table" id="t_new_bank_details">
            <tr>
                <td class="fancy_td"><strong>Name</strong></td>
                <td class="fancy_td" colspan="2"><input type="text" name="name" maxlength="20"></td>
                <td></td>
            </tr>
            <tr>
                <td class="fancy_td"><strong>Payment comment</strong></td>
                <td class="fancy_td" colspan="2"><textarea name="pay_comment"></textarea></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3" align="center"><strong>Bank details</strong></td>
            </tr>
            <tr>
                <td class="fancy_td"><strong>Parametr</strong></td>
                <td class="fancy_td"><strong>Value</strong></td>
                <td class="fancy_td"><strong>Delete</strong></td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td colspan="2" align="center"><a href="#" onclick="new_bank_det_add_line()">Add line</a></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input class="button" type="submit" value="Apply changes"></td>
            </tr>
        </table>
    </form>
</div>
<script type="text/javascript" src="java/java_func.js"></script>
<script>
function display_edit(id){
    var container=document.getElementById('window');
    document.getElementById('wrap').style.display='block';
    container.style.display='block';
    var formData = new FormData();
    formData.append("id", id);
    var req = getXmlHttp();
    req.onreadystatechange = function(){ 
            if (req.readyState == 4) {
		container.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                    container.innerHTML =req.responseText;
		}
            }
	};
    req.open('POST', 'ajax/our_comp_edit.php');  
    req.send(formData);  // отослать запрос
    container.innerHTML = '<img src="./img/loading.gif">';
}
function new_bank_det_add_line(){
    var t=document.getElementById('t_new_bank_details');
    var tbody = t.getElementsByTagName("TBODY")[0];
    var row = document.createElement("TR");
    var td1 = document.createElement("TD");
    var td2 = document.createElement("TD");
    var td3 = document.createElement("TD");
    var inp1 = document.createElement("INPUT");
    inp1.setAttribute('Name','param_name[]')
    var inp2 = document.createElement("INPUT");
    inp2.setAttribute('Name','param_value[]')
    var inp3 = document.createElement("A");
    inp3.innerText='Delete';
    inp3.setAttribute('href','#')
    inp3.onclick=delete_row;
    td1.appendChild (inp1);
    td2.appendChild (inp2);
    td3.appendChild (inp3);
    td1.classList.add("fancy_td");
    td2.classList.add("fancy_td");
    td3.classList.add("fancy_td");
    row.appendChild(td1); 
    row.appendChild(td2);
    row.appendChild(td3);
    tbody.appendChild(row);
}
function edit_bank_det_add_line(){
    var t=document.getElementById('t_edit_bank_details');
    var tbody = t.getElementsByTagName("TBODY")[0];
    var row = document.createElement("TR");
    var td1 = document.createElement("TD");
    var td2 = document.createElement("TD");
    var td3 = document.createElement("TD");
    var inp1 = document.createElement("INPUT");
    inp1.setAttribute('Name','param_name[]')
    var inp2 = document.createElement("INPUT");
    inp2.setAttribute('Name','param_value[]')
    var inp3 = document.createElement("A");
    inp3.innerText='Delete';
    inp3.setAttribute('href','#')
    inp3.onclick=delete_row;
    td1.appendChild (inp1);
    td2.appendChild (inp2);
    td3.appendChild (inp3);
    td1.classList.add("fancy_td");
    td2.classList.add("fancy_td");
    td3.classList.add("fancy_td");
    row.appendChild(td1); 
    row.appendChild(td2);
    row.appendChild(td3);
    tbody.appendChild(row);
}
function delete_row(){
    this.parentNode.parentNode.parentNode.deleteRow(this.parentNode.parentNode.rowIndex);
}
</script>