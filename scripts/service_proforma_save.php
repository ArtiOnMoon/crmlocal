<?php
require_once '../functions/db.php';
require_once '../functions/main.php';
require_once '../functions/auth.php';
startSession();
security ();

if (!access_check([],[],2)) exit ('Access denied');
$id=clean($_POST['id']);
if (clean($_POST['proforma_exist'])==='1')$query= 'update service_proforma set ';
else $query= 'insert service_proforma set proforma_id="'.$id.'", ';
$db =  db_connect();
$db->autocommit(false);
$query.= 'comp_id="'.clean($_POST['new_customer']).'", '
        . 'po="'.clean($_POST['po']).'", '
        . 'currency="'.clean($_POST['currency']).'", '
        . 'proforma_our_company="'.clean($_POST['our_company']).'", '
        . 'our_ref="'.clean($_POST['our_ref']).'", '
        . 'your_ref="'.clean($_POST['your_ref']).'", '
        . 'proforma_total="'.(double)clean($_POST['proforma_total']).'", '
        . 'proforma_note="'.clean($_POST['proforma_note']).'", '
        . 'pay_terms="'.clean($_POST['pay_terms']).'"';
if (clean($_POST['proforma_exist'])==='1')$query.= ' where proforma_id="'.$id.'"';
if(!$db->query($query)){
    echo $db->error;
    $db->rollback();
    $db->close();
    exit();    
}

//проверка и вставка rates

//проверка существования проформы
if ($_POST['proforma_exist']=='1') {
    //очистка rates
    if(!$db->query('delete from service_proforma_rates where spr_proforma_id="'.$id.'"')){
        echo 'Unable to clear previous rates.<br>Error: ',$db->error;;
        $db->rollback();
        $db->close();
        exit();            
    }
}
if ($_POST['rates']!=='NULL'){
    $rates= json_decode($_POST['rates']);
    foreach ($rates as $rate){
        $query='insert into service_proforma_rates set '
                . 'spr_proforma_id = "'.$id.'",'
                . 'spr_rate_id = "'.mysqli_real_escape_string($db,$rate[2]).'",'
                . 'spr_rate_qnt = "'.mysqli_real_escape_string($db,$rate[0]).'",'
                . 'spr_rate_price = "'.mysqli_real_escape_string($db,$rate[1]).'"';
        if(!$db->query($query)){
            echo 'Unable to insert new rate.<br>Error: ',$db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}

//проверка и вставка spares

if ($_POST['proforma_exist']=='1') {
    //очистка items
    if(!$db->query('delete from service_proforma_items where spi_proforma_id="'.$id.'"')){
        echo 'Unable to clear previous spares.<br>Error: ',$db->error;
        $db->rollback();
        $db->close();
        exit();            
    }
}
if ($_POST['spare']!=='NULL'){
    $spares= json_decode($_POST['spare']);
    foreach ($spares as $sp){
        $query='insert into service_proforma_items set '
                . 'spi_proforma_id = "'.$id.'",'
                . 'spi_pn = "'.mysqli_real_escape_string($db,$sp[0]).'",'
                . 'spi_descr = "'.mysqli_real_escape_string($db,$sp[1]).'",'
                . 'spi_qnt = "'.mysqli_real_escape_string($db,$sp[2]).'",'
                . 'spi_price = "'.mysqli_real_escape_string($db,$sp[3]).'",'
                . 'spi_type = "'.mysqli_real_escape_string($db,$sp[4]).'"';
        if(!$db->query($query)){
            echo 'Unable to insert new item.<br>Error: ',$db->error;
            $db->rollback();
            $db->close();
            exit();
        }
    }
}

//Все прошло успешо
$db->commit();
$db->close();
header('Location: /service_proforma.php?saved=1&service_id='.$id);