<?php
require_once '../../functions/main.php';
require_once '../../functions/db.php';
require_once '../../functions/auth.php';
require_once '../../functions/selector.php';
require_once '../../classes/Adm.php';
require_once '../../classes/Order_name_engine.php';

?>
<div class="window_internal" style="width:1280px;height:80%;background: #EEE;">
    <link rel="stylesheet" type="text/css" href="css/adm.css">
    <div class="nd_main_grid">
<?php

$db = db_connect();

$user_list = get_user_list();
$our_comp = get_our_companies_list();

$adm = new Adm();

?>      <div class="nd_header">
            <div><h2>New order</h2></div>
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
                    <input class="nd_input datepicker" type="text" name="date" required value="<?php echo $adm->date;?>">
                </div>
                <div class="nd_block adm_status">
                    <label class="nd_label">Status</label>
                    <?php echo select_from_list($adm->status_list, $adm->status, 'required class="nd_select" readonly name="status"'); ?>
                </div>
                <div class="nd_block adm_title">
                    <label class="nd_label">Title</label>
                    <input class="nd_input" required type="text" name="title" required value="<?php echo $adm->title;?>">
                </div>
                <div class="nd_block adm_comp">
                    <label class="nd_label">Company</label>
                    <?php echo select_from_list($our_comp,  $adm->comp_id, 'class="nd_select" name="comp_id"'); ?>
                </div>
                <div class="nd_block adm_note">
                    <label class="nd_label">Note</label>
                    <textarea class="nd_textarea" name="note"><?php echo $adm->note; ?></textarea>
                </div>
                <div class="nd_block adm_incharge">
                    <label class="nd_label">Incharge</label>
                    <?php echo select_from_list($user_list, $_SESSION['uid'], 'class="nd_select" name="incharge"'); ?>
                </div>
                <div class="nd_block adm_customer">
                    <label class="nd_label">Customer</label>
                    <?php echo selector_customer('name="customer" class="nd_input"',$adm->customer);?>
                    <img title="View customer" class="line_image" align="middle" src='/icons_/ex_link.png' onclick="customer_view_add(this)">
                </div>
                <div class="nd_block adm_links align_center">
                    <label class="nd_label">Links</label>
                        You must save the order to be able to add links.
                </div>
                <div class="nd_block adm_logs">
                    <div><label class="nd_label">Log</label></div>
                    <div class="adm_log_container">
                         You must save the order to be able to add logs.
                    </div>
                    <div><textarea id="adm_log_field" disabled class="nd_textarea2" placeholder="Input text here"></textarea></div>
                    <div><input class="nd_button" disabled type="button" onclick="adm_submit_log(this,<?php echo $adm->id;?>)" value="Add log"></div>
                </div>
        </form>
        <div id="uploaded_files" class="nd_body nd_tabdiv nd_tabdiv_inactive"></div>
        <div class="nd_footer adm_footer nd_block">
            <button class="nd_button_green" onclick="adm_save(this)">Save</button>
            <button class="nd_button" onclick="window_close(this)">Close</button>
        </div>
    </div>
</div>