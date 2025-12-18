<?php
require_once 'functions/fns.php';
require_once 'functions/service.php';
startSession();
security();
$rate_cat_id=clean($_GET['rate_cat_id']);
$query= 'select * from service_rates_cat where rate_cat_id="'.$rate_cat_id.'"';
$db =  db_connect();
do_page_header('Change rate category', 'Справочники');
echo '<div id="main_div_menu">';
$result=$db->query($query);
if ($result->num_rows===0){
    exit('Not found');
}
$row=$result->fetch_assoc();
?>
<h1><?php echo $rate_cat_id; ?></h1>
<form name="rate_cat_form" action="change_rate_cat.php" method="POST" width="100%">
    <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td width="100"><b>Category</b></td>
            <td><input type="text" name="rate_cat_name" maxlength="500" required value="<?php echo $row['rate_cat_name']; ?>"></td>
        </tr>
        <tr>
            <td colspan="2"><label>Delete<input type="checkbox" name="delete" value="1"></label></td>
        </tr>
    </table> 
    <input type="hidden" name="rate_cat_id" value="<?php echo $rate_cat_id;?>">
    <div align="center" width="100%" style="padding: 10px">
        <input type="submit" value="Change">
    </div>
</form>
</div>