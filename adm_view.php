<?php
require_once 'functions/main.php';
require_once 'functions/db.php';
require_once 'functions/auth.php';
require_once 'functions/selector.php';
require_once 'classes/Adm.php';
require_once 'classes/Order_name_engine.php';

?>
<div class="window_internal" style="width:1280px;height:80%;background: #EEE;">
    <link rel="stylesheet" type="text/css" href="css/adm.css">
    <div class="nd_main_grid">
<?php

$db = db_connect();

$user_list = get_user_list();
$our_comp = get_our_companies_list();

//print_r ($user_list);
$order = clean($_POST['order']);
$on = new Order_name_engine();
$on -> init($db);

$adm = new Adm();

try{
    $on -> resolve_order($order);
    $adm -> resolve_by_order($db, $on->num, $on->comp_id);
} catch (Exception $ex) {
    echo $ex->getMessage(); exit('</div>');
}

?>      <div class="nd_header">
            <div><h2><?php echo $on->order;?> <a onclick="copy_to_clipboard(this, '<?php echo $on->order;?>')"><img src="./icons_/copy.svg"></a></h2></div>
            <div class="close_button_div"><a class="close_button" href="#" onclick="window_close(this)">&#10006;</a></div>
        </div>
        <div class="nd_subheader nd_tabs">
            <div class="nd_tab nd_tab_active" onclick="switchTab(this)" data-tab="adm_main_form">Main</div>
            <div class="nd_tab" onclick="switchTab(this)" data-tab="uploaded_files">Uploaded files</div>
        </div>
        <form id="adm_main_form" onsubmit="return adm_main_form_submit()" name="adm_main_form" class="nd_body adm_grid nd_tabdiv">
            <input type="hidden" name="id" value="<?php echo $adm->id;?>">
            <input type="hidden" name="number" value="<?php echo $adm->number;?>">
                <div class="nd_block adm_date">
                    <label class="nd_label">Date</label>
                    <input class="nd_input datepicker" type="text" required name="date" value="<?php echo $adm->date;?>">
                </div>
                <div class="nd_block adm_status">
                    <label class="nd_label">Status</label>
                    <?php echo select_from_list($adm->status_list, $adm->status, 'required class="nd_select" readonly name="status"'); ?>
                </div>
                <div class="nd_block adm_title">
                    <label class="nd_label">Title</label>
                    <input class="nd_input" required type="text" required name="title" value="<?php echo $adm->title;?>">
                </div>
                <div class="nd_block adm_comp">
                    <label class="nd_label">Company</label>
                    <?php echo select_from_list($our_comp,  $adm->comp_id, 'required class="nd_select" name="comp_id"'); ?>
                </div>
                <div class="nd_block adm_note">
                    <label class="nd_label">Note</label>
                    <textarea class="nd_textarea" name="note"><?php echo $adm->note; ?></textarea>
                </div>
                <div class="nd_block adm_incharge">
                    <label class="nd_label">Incharge</label>
                    <?php echo select_from_list($user_list, $adm->incharge, 'required class="nd_select" name="incharge"'); ?>
                </div>
                <div class="nd_block adm_customer customer_conteiner">
                    <label class="nd_label">Customer</label>
                    <?php echo selector_customer('name="customer" class="nd_input"',$adm->customer);?>
                    <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </div>
                <div class="nd_block adm_links align_center">
                    <label class="nd_label">Links</label>
                        <div class="related_orders_wrapper">
                            <input type="hidden" class="related_orders_number" value="<?php echo $on->order;?>">
                            <div class="related_orders_conteiner"></div>
                            <table width="100%" class="related_orders_add_block">
                                <tr>
                                    <td><input type="text" maxlength="12" size="10" class="related_orders_number2" placeholder="XX-XXX-XXXXX"></td>
                                    <td><a class="knopka3" href="#" onclick="related_orders_add(this)">Add link</a></td>                                    
                                </tr>
                            </table>                            
                        </div>
                </div>
                <div class="nd_block adm_logs">
                    <div><label class="nd_label">Log</label></div>
                    <div class="adm_log_container">
                        <?php //  strart retrieving log data
                        $query2 = 'SELECT administrative_logs.*, full_name FROM administrative_logs '
                                . 'LEFt JOIN users on uid=user '
                                . 'WHERE order_id ="'.$adm->id.'" ORDER BY id DESC';
                        if(!$result2 = $db->query($query2)){
                            echo $db->error,$query2;}
                        while($row = $result2->fetch_assoc()){
                            echo '<div class="adm_log_div">';
                            echo '<div class="adm_log_div_header"><strong>'.$row['full_name'].'</strong><br>'.$row['timestamp'].'</strong></div>';
                            echo '<div class="adm_log_div_text">'.$row['text'].'</div></div>';
                        } ?>
                    </div>
                    <div><textarea id="adm_log_field" class="nd_textarea2" placeholder="Input text here"></textarea></div>
                    <div><input class="nd_button" type="button" onclick="adm_submit_log(this,<?php echo $adm->id;?>)" value="Add log"></div>
                </div>
        </form>
        <div id="uploaded_files" class="nd_body nd_tabdiv nd_tabdiv_inactive"></div>
        <div class="nd_footer nd_block adm_footer">
            <button class="nd_button_green" onclick="adm_update(this)">Save</button>
            <button class="nd_button" onclick="window_close(this)">Close</button>
        </div>
    </div>
</div>