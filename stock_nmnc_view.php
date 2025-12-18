<?php
require_once 'functions/stock_fns.php';
require_once 'functions/fns.php';

startSession();
security ();
if(check_access('acl_stock', 2)) exit('Access denied.');
$id=$_POST['id'];

if (isset($_POST['id'])) $id=$_POST['id'];
$db =  db_connect();
$query= 'select stock_nmnc.*, users.full_name from stock_nmnc LEFT JOIN users ON stock_nmnc.stnmc_modified_by=users.uid where stnmc_id = "'.$id.'"';
$result=$db->query($query);
if ($result-> num_rows!==1) exit('Nothing found.');
$row=$result->fetch_assoc();
if ($row['stnmc_deleted']) $delete='checked';
?>

<div class="window_internal" style="width:1080px;height:400px;">
<div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this)">&#10006;</a></div>
<?php echo '<h1>'.$id.'</h1>';?>
<form onsubmit="return stock_nmnc_change(this)">
<div class="block_div2">
<table width="800px">
    <tr>
        <td class="fancy_td"><b>Category</b></td>
        <td class="fancy_td"><?php echo select_stock_class($row['stnmc_type'],0,'name="stnmc_type"');?></td>
        <td class="fancy_td"><b>Type/model</b></td>
        <td class="fancy_td"><input  type="text" maxlength="30" id="stnmc_type_model" name="stnmc_type_model" value="<?php echo $row['stnmc_type_model'];?>"></td>
    </tr>
    <tr>
        <td class="fancy_td"><b>Manufacturer</b></td>
        <td class="fancy_td"><?php echo select_manufacturer($row['stnmc_manuf'],'name="stnmc_manuf"');?></td>
        <td class="fancy_td"><b>P/N</b></td>
        <td class="fancy_td"><input type="text" maxlength="30" id="spare_pn" name="stnmc_pn" value="<?php echo $row['stnmc_pn'];?>"></td>
    </tr>
    <tr>
        <td class="fancy_td"><b>Description</b></td>
        <td class="fancy_td"><input type="text" maxlength="250" size="50" name="stnmc_descr" value="<?php echo $row['stnmc_descr'];?>"></td>
        <td class="fancy_td"><b>Spare part</b></td>
        <td class="fancy_td"><input type="checkbox"  name="stnmc_is_spare" value="1" <?php if ($row['stnmc_is_spare']==='1')echo 'checked'; ?>></td>
    </tr>
    <tr>
        <td class="fancy_td"><b>Country of origin</b></td>
        <td class="fancy_td"><?php echo select_country($row['stnmc_origin'], 'name="stnmc_origin"',2);?>
        <td class="fancy_td"><b>Commodity code</b></td>
        <td class="fancy_td"><input type="text" size="20" name="stnmc_commod_code" value="<?php echo $row['stnmc_commod_code'];?>"></td>
    </tr>
    <tr>
        <td class="fancy_td"><b>Currency</b></td>
        <td class="fancy_td"><?php echo select_currency2(get_currency_list(), $row['stnmc_currency'], 'name="stnmc_currency"');?></td>
        <td class="fancy_td"><b>List price</b></td>
        <td class="fancy_td"><input type="number" step="0.01" maxlength="10" style="width:5em" name="stnmc_price" value="<?php echo $row['stnmc_price'];?>"></td>
    </tr>
    <tr>
        <td class="fancy_td"><b>Discount (%)</b></td>
        <td class="fancy_td"><input type="text" maxlength="4"  size="5" name="stnmc_discount" value="<?php echo $row['stnmc_discount'];?>"></td>
        <td class="fancy_td"><b>Russia multiplier</b></td>
        <td class="fancy_td"><input type="number" step="0.001" maxlength="4"  size="5" name="stnmc_russia_mult" value="<?php echo $row['stnmc_russia_mult'];?>"></td>
    </tr>
    <tr>
        <td class="fancy_td"><b>Note</b></td>
        <td class="fancy_td" colspan="3"><textarea name="stnmc_note" maxlength="200" rows="2" style="resize: none;width:100%;"><?php echo $row['stnmc_note']; ?></textarea></td>
    </tr>
    <tr>
        <td colspan="2"><em>Last modified <?php echo $row['stnmc_modified_date'];?>
            <br> by <?php echo $row['full_name'];?></em>
        </td>
        <td class="fancy_td"><b>DELETE position</b></td>
        <td class="fancy_td"><input type="checkbox" name="stnmc_deleted" value="1" <?php echo $delete ?>>delete</td>
    </tr>
</table>
    <div class="stock_avaliable_div">
        <div style="text-align: center"><h3>Avaliable on stock</h3></div>
        <table width="100%">
            <thead>
                <th>Stock</th><th>Count</th>
            </thead>
        <?php
        $query='SELECT count(stnmc_id) as value,stockl_name,stockl_id FROM stock_nmnc 
                LEFT JOIN stock_new ON stnmc_id=stock_nmnc_id 
                JOIN stock_list ON stock_stock_id=stockl_id 
                WHERE stnmc_id='.$id.' AND stock_status=1 
                GROUP BY stockl_id';
        $result=$db->query($query);
        while($row=$result->fetch_assoc()){
            echo'<tr><td>',$row['stockl_name'],'</td><td>',$row['value'],'</td></tr>';
        }        
        ?>
        </table>
    </div>
</div>
    <div align="center">
        <input class="green_button" type="submit" value="Save"> 
        <input type="button" value="Close" onclick="window_close(this)">
    </div>
    <input type="hidden" name="stnmc_id" value="<?php echo $id;?>">
</form>
</div>