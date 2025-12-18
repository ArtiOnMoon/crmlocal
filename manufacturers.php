<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
startSession();
//if(check_access('acl_cust', 1)) exit('Access denied.');
$page_title = 'MANUFACTURERS';
include 'header.php';
?>

<div id="wrap" onclick="cancel()"></div>
<div id="side_menu">
    <div id="flex_menu_container">
        <a class="flex_element knopka" href="#"  onclick="display('new_manufacturer')">New manufacturer</a>
    </div>
</div>
<div class="hidden" id="new_manufacturer" style="width:300px;height: 150px;">
    <h2>New manufacturer</h2>
    <form method="POST" action="/scripts/manufacturer_add.php">
    <table width="100%">
        <tr>
            <td><strong>Short name</strong></td><td><input type="text" name="mnf_short_name"></td>
        </tr>
        <tr>
            <td><strong>Full name</strong></td><td><input type="text" name="mnf_full_name"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" class="green_button" value="Save">
                <input type="button" value="Cancel" onclick="cancel()">
            </td>
        </tr>
    </table>
    </form>
</div>
<div class="hidden" id="mnf_edit"></div>

<main id="main_div_menu">
    <table width="100%">
        <thead><th>Manufacturer ID</th><th>Manufacturer short name</th><th>Manufacturer full name</th></thead>
<?php
$db=db_connect();
$query='SELECT * FROM manufacturers ORDER BY mnf_short_name';
if(!$result=$db->query($query)) exit($db->error);
if ($result->num_rows === 0)    exit('<tr><td>No result</td></tr>');
while ($row = $result->fetch_assoc()) {
    echo '<tr><td><a href="#" onclick="mnf_change(',$row['mnf_id'],')">',$row['mnf_id'],'</a>',
            '</td><td>',$row['mnf_short_name'],
            '</td><td>',$row['mnf_full_name'],
            '</td></tr>';
}
?>
    </table>
</main>

<?php include 'footer.php';?>

<link rel="stylesheet" type="text/css" href="css/tabs.css">
<script type="text/javascript" src="java/java_func.js"></script>
<script>
    function mnf_change(elem){
        let targ=document.getElementById('mnf_edit');
        targ.style.display='block';
        var formData = new FormData();
        formData.append("id",elem);;
        let req = getXmlHttp();
        req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                //alert(t.innerHTML);
                targ.innerHTML = req.responseText;
            }
	}
        };
        req.open('POST', '/ajax/manufacturer_edit.php');  
        req.send(formData);  // отослать запрос
        targ.innerHTML = '<img src="/img/loading.gif">';
    }
</script>