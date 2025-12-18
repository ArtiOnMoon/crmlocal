<?php
require_once '../functions/main.php';
require_once '../functions/db.php';
require_once '../functions/auth.php';
require_once '../classes/Order_name_engine.php';

//1 - service 2 - sales 3 - PO 4 - invoice

$db =  db_connect();

$on = new Order_name_engine();
$on->init($db);

//$comp_id=clean($_POST['comp_id']);
$number=clean($_POST['number']);
//$type=clean($_POST['type']);

//if ($type<4)$number=(int)$number;
////проверка для Credit Note
//If ($type==="4"){
//    $result=$db->query('SELECT invoice_is_cn FROM invoices WHERE invoice_num="'.$number.'" AND invoice_our_comp="'.$comp_id.'"');
//    $inv_type=$result->fetch_assoc();
//    if ($inv_type['invoice_is_cn']==='0')$type=4;
//    else $type=5;
//}
//
//echo  $comp_id,' ',$number,' ',$type;

//$query= 'SELECT id,type2 as type,comp2 as comp_id, num2 as number FROM cross_docs WHERE comp1="'.$comp_id.'" AND type1="'.$type.'" AND num1="'.$number.'"'
//        . 'UNION '
//        . 'SELECT id,type1 as type,comp1 as comp_id, num1 as number FROM cross_docs WHERE comp2="'.$comp_id.'" AND type2="'.$type.'" AND num2="'.$number.'"';

$query= 'SELECT id, num2 as number FROM cross_docs WHERE num1="'.$number.'"'
        . 'UNION '
        . 'SELECT id, num1 as number FROM cross_docs WHERE num2="'.$number.'"';
if(!$result=$db->query($query)){
    echo $db->error;
    exit();
}

if ($result->num_rows===0)exit();
echo '<table class="align_left" width="100%">';
while($row=$result->fetch_assoc()){
//    $type=cross_docs_get_type($row['type']);
    ?>
    <tr style="border-bottom:1px solid #EEE;">
        <!--<td><?php // echo $type;?></td>-->
        <td><a href="#" onclick="view_link('<?php echo $row['number']; ?>')"><?php echo $row['number'];?></a></td>
        <td class="align_right"><a class="knopka2" href="#" onclick="related_docs_delete(this,<?php echo $row['id'];?>)"><img class="line_image" src="/icons_/del.png"></a></td>
    </tr>
    <?php
}
 echo '</table>';