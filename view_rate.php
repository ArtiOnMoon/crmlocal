<?php
require_once 'functions/main.php';
require_once 'functions/service.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
startSession();
security();
$rate_id=clean($_GET['rate_id']);
$query= 'select * from service_rates where rate_id="'.$rate_id.'"';
$db =  db_connect();
do_page_header('Change rate', 'Справочники');
echo '<div id="main_div_menu">';
$result=$db->query($query);
if ($result->num_rows===0){
    exit('Not found');
}
$row=$result->fetch_assoc();
?>
<h1><?php echo $rate_id; ?></h1>
<form name="rate_form" action="change_rate.php" method="POST">
    <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Category</b></td>
            <td><?php echo select_service_rates_cat($row['rate_cat']); ?></td>
        </tr>
        <tr>
            <td width="100"><b>Description</b></td>
            <td><textarea required rows="3" cols="50" maxlength="500" name="rate_name"><?php echo $row['rate_name']; ?></textarea></td>
        </tr>
        <tr>
            <td width="100"><b>Price</b></td>
            <td><input type="text" name="rate_price" maxlength="8" required value="<?php echo $row['rate_price']; ?>"></td>
        </tr>
        <tr>
            <td width="100"><b>Currency</b></td>
            <td><?php echo select_currency2(get_currency_list(),$row['rate_currency']); ?></td>
        </tr>
        <tr>
            <td colspan="2"><label>Delete<input type="checkbox" name="delete" value="1"></label></td>
        </tr>
    </table> 
    <input type="hidden" name="rate_id" value="<?php echo $rate_id;?>">
    <div align="center" width="100%" style="padding: 10px">
        <input type="submit" value="Change">
    </div>
</form>
</div>