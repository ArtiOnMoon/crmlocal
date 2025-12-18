<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
startSession();
if(check_access('acl_cust', 1)) exit('Access denied.');

$page_title = 'Customers';
include 'header.php';
?>

<div id="invis_wrap" onclick="close_menu()"></div>
<div id="side_menu"><div id="flex_menu_container">
    <a class="flex_element knopka" href="#"  onclick="new_customer()">New customer</a>
<!-- FILTERS BLOCK -->
    <span class="hidden_conteiner flex_element"  style="width:100px"><label onclick="display_menu('hidden_div','list_sign')">Show filters <span id="list_sign" class="sign">&#9660</span></label>
    <div class="hidden_div" id="hidden_div"  style="margin-left:-5px;margin-top:5px; position:fixed; vertical-align: top;">
        <table>
            <tr style="vertical-align: top;">
                <td>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_mnfr">Manufacturer</label><br>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_sppl">Supplier</label><br>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_serv">Service</label><br>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_ownr">Owner</label><br>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_mngr">Manager</label><br>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_agnt">Agent</label><br>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_optr">Operator</label><br>
                    <label><input type="checkbox" class="customer_filter" onchange="show_customers_table()" id="is_fchk">For checking</label><br>
                    <button onclick="check_all()">Check\uncheck all</button>
                </td>
                <td>
                    Select country<br>
                    <?php select_country('','id="cust_filter_country" onchange="show_customers_table()"',1);?>
                    <br>
                    <label><input type="checkbox" id="cust_exclude" onchange="show_customers_table()">Exclude</label>
                </td>
            </tr>
        </table>
    </div>
    </span>
<!-- END FILTERS BLOCK -->
    <span class="flex_element">Fast search: <input type="search" id="search" placeholder = "Enter company name" oninput="show_customers_table()"></span>
<!--    <a class="flex_element knopka" href="#"  onclick="display('advanced_search')" >Advanced serch</a>-->
    <label style="position: absolute; right:10px;"><input id="auto_refresh" type="checkbox" checked="true">Auto refresh</label>
</div>
</div>
<div id="wrap" onclick="cancel()"></div>

<!--<div id="advanced_search" class="hidden" style="width:600px;height:400px">
    <form name="advanced_search" action="customer_search.php" method="POST">
        <h2 align="center">Advanced search</h2>
            <table width="100%" border="1px" cellspacing = "0" cellpadding="2px">
                <tr>
                    <td><input type="checkbox" value="inp1" onchange="activate(this)"></td>
                    <td><b>Company name</b></td>
                    <td><input type="text" name="name" id="inp1" disabled></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp2" onchange="activate(this)"></td>
                    <td><b>Country</b></td>
                    <td><?php select_country('','name="country" disabled id="inp2"');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp3" onchange="activate(this)"></td>
                    <td><b>Category</b></td>
                    <td><?php select_customer_type('', 'name="customer_type" id="inp3" disabled'); ?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp4" onchange="activate(this)"></td>
                    <td><b>E-mail</b></td>
                    <td><input type="text" name="email" id="inp4" disabled></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp5" onchange="activate(this)"></td>
                    <td><b>Website</b></td>
                    <td><input type="text" name="site" id="inp5" disabled></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp6" onchange="activate(this)"></td>
                    <td><b>Phone</b></td>
                    <td><input type="text" name="phone" id="inp6" disabled></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp7" onchange="activate(this)"></td>
                    <td><b>Address</b></td>
                    <td><input type="text" name="addr" id="inp7" disabled></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp8" onchange="activate(this)"></td>
                    <td><b>Bank details</b></td>
                    <td><input type="text" name="bank" id="inp8" disabled></td>
                </tr>
                <tr>
                    <td><input type="checkbox" value="inp9" onchange="activate(this)"></td>
                    <td><b>Note</b></td>
                    <td><input type="text" name="note" id="inp9" disabled></td>
                </tr>
            </table>
        <div align="center" width="100%" style="padding: 10px">
        <input type="submit" value="Submit" class="button"> 
        <input type="button" value="Close" onclick="cancel()" class="button">
    </div>
    </form>
</div>-->

<main id="main_div_menu"></main>

<?php include 'footer.php';?>

<link rel="stylesheet" type="text/css" href="css/tabs.css">
<script type="text/javascript" src="java/java_func.js"></script>
<script type="text/javascript" src="java/java_customers.js"></script>
<script>
document.addEventListener("DOMContentLoaded", show_customers_table('<?php echo $_SESSION['cust_page'];?>'));

var timer=600000;
var user_last_activity=Date.now();
function reset_timer(){
    user_last_activity=Date.now();
};
document.onmousemove=reset_timer;
document.onclick=reset_timer;
document.onscroll=reset_timer;
setTimeout(function run() {
    if (document.getElementById('auto_refresh').checked) {
        if ((Date.now()-user_last_activity)>timer)show_customers_table();
        setTimeout(run, timer);
    }
    else setTimeout(run, timer);
}, timer);
function check_all(){
    var inputs=document.getElementsByClassName('customer_filter');
    for (var i=0;i<inputs.length;i++){
          if (inputs[i].checked==false){
              $(".customer_filter").prop("checked",true);
              show_customers_table();
              return;
          }
    }
    $(".customer_filter").prop("checked",false);
    show_customers_table();
}
</script>
