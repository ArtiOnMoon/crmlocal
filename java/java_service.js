function add_new_service(){
    let statusElem = document.getElementById('service_status'); 
    let formData = new FormData(document.forms.new_service_form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                 // если статус 200 (ОК) - выдать ответ пользователю
               	statusElem.innerHTML =req.responseText;
                    if (statusElem.innerHTML == '<font color="green">Success</font>') location.reload();}
            }
	}
    req.open('POST', 'new_service.php');  
    req.send(formData);
    statusElem.innerHTML = 'Ожидаю ответа сервера...';
}
function service_new_form(elem){
    let container = elem.closest('.window_internal');
    let form = container.querySelector('#service_new_form');
    let formData = new FormData(form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                alert(req.responseText);
                if (req.responseText == 'Service add successfully') {
                    show_service_table(1);
                    window_close(elem);
                }
            }
        }
    };
    req.open('POST', '/scripts/service_new.php');  
    req.send(formData);
    return false;
}
function add_new_vessel(){
    var statusElem = document.getElementById('vessel_status') 
    if ((document.forms.new_vessel_form.new_vessel_contacts.value === '')
        || (document.forms.new_vessel_form.new_flag.value === '')
        || (document.forms.new_vessel_form.new_captain.value === '')
        || (document.forms.new_vessel_form.new_vessel_name.value === '')
        )
    {
        statusElem.innerHTML='<font color="red">you must fill all fields</font>';
        return;
    }
    var formData = new FormData(document.forms.new_vessel_form);
    var req = getXmlHttp()  
		req.onreadystatechange = function() {  
		if (req.readyState == 4) {
			statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
			if(req.status == 200) { 
                 // если статус 200 (ОК) - выдать ответ пользователю
				statusElem.innerHTML =req.responseText;
                                if (statusElem.innerHTML == '<font color="green">Success</font>') location.reload();
			}
		}
	}
	req.open('POST', 'new_vessel.php');  
	req.send(formData);
	statusElem.innerHTML = '<img src="./img/loading.gif">';
}
function add_new_vessel_ajax(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                // если статус 200 (ОК) - выдать ответ пользователю
                if(req.responseText==='true'){
                    alert('Vessel added successfully.');
                    window_close(elem);
                }
                else alert(req.responseText);
            }
        }
    }
    req.open('POST', 'vessel_add.php');  
    req.send(formData);  // отослать запрос 
    return false;
}
function invoice_func(){
    add=document.getElementById('address');
    inv=document.getElementById('invoice_text');
    chk=document.getElementById('inv_checkbox');
    inv.value = add.value;
    //if (document.getElementById('inv_checkbox').checked == '1') {inv.setAttribute('disabled', 'disabled');}
    if (chk.checked == '1')  {inv.disabled=true;}
    else {inv.disabled=false;}
}
function srv_reset_filters(){
    document.getElementById('customer').selectedIndex=0;
    document.getElementById('agent').selectedIndex=0;
    document.getElementById('vessel').selectedIndex=0;
    document.getElementById('srv_agent').selectedIndex=0;
    $("#side_menu input").val("");
    $("#hidden_div input").val("All");
    show_service_table();
}
function show_service_table (page){
    let statusElem = document.getElementById('main_div_menu');
    let formData = new FormData();
    let search=document.getElementById('service_search').value;
    let service_search_by_equipment=document.getElementById('service_search_by_equipment').value;
    //users_list
    let selected_users=[];
    $(".user_multiselect:checked").each(function(){
        selected_users.push(this.value);
    });
    if(selected_users.length>0){
       users_list=JSON.stringify(selected_users);
       formData.append("users_list", users_list); 
    }
    //End users_list
    let our_company=document.getElementById('service_our_company').value;
    if (our_company!=='All') formData.append("our_company", our_company);
    let status_1=document.getElementById('status_1').checked;
    let status_2=document.getElementById('status_2').checked;
    let status_3=document.getElementById('status_3').checked;
    let status_5=document.getElementById('status_5').checked;
    let status_6=document.getElementById('status_6').checked;
    let status_7=document.getElementById('status_7').checked;
    let status_8=document.getElementById('status_8').checked;
    let status_9=document.getElementById('status_9').checked;
    if (status_1)formData.append("status_1", 1);
    if (status_2)formData.append("status_2", 1);
    if (status_3)formData.append("status_3", 1);
    if (status_5)formData.append("status_5", 1);
    if (status_6)formData.append("status_6", 1);
    if (status_7)formData.append("status_7", 1);
    if (status_8)formData.append("status_8", 1);
    if (status_9)formData.append("status_9", 1);
    let date_start=document.getElementById('date_start').value;
    let date_end=document.getElementById('date_end').value;
    formData.append("date_start", date_start);
    formData.append("date_end", date_end);
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    if (search !== '') formData.append("search", search);
    if (service_search_by_equipment !== '') formData.append("service_search_by_equipment", service_search_by_equipment);
    let vessel = document.getElementById('vessel').value;
    let agent = document.getElementById('agent').value;
    let customer = document.getElementById('customer').value;
    let srv_agent = document.getElementById('srv_agent').value;
    if (agent !== 'All') formData.append("agent", agent);
    if (customer !== 'All') formData.append("customer", customer);
    if (vessel !== 'All') formData.append("vessel", vessel);
    if (srv_agent !== 'All') formData.append("srv_agent", srv_agent);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            statusElem.innerHTML = req.statusText; // показать статус (Not Found, ОК..)
            if(req.status === 200) { 
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'service_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
 }
function service_close(elem){
    window_close(elem);
    //show_service_table();
}
function display_service(){
   var elem = document.getElementById('service_view').value;
   show_service_table(1,elem);
}
function fast_search(){
   var tab = document.getElementById('service_table');
   var search = document.getElementById('service_search').value;
   if (search=='') {rebuild_service_table(); exit;}
    for (i=1; i<tab.rows.length; i++){
        var t = tab.rows[i];
                if ((t.cells[2].innerHTML.indexOf(search) > -1) || (t.cells[4].innerHTML.indexOf(search) > -1) || (t.cells[9].innerHTML.indexOf(search) > -1)) {t.style.display = '';} else
            t.style.display = 'none';
    }
}
function radio_change(elem,id1,id2){
    let targ = elem.closest('.calc_div_border');
    let div1=targ.querySelector('#'+id1);
    let div2=targ.querySelector('#'+id2);
    if (elem.value=='0') {
        div1.classList.remove('disabledbutton');
        div2.classList.add('disabledbutton');
    }
    else if (elem.value=='1') {
        div1.classList.add('disabledbutton');
        div2.classList.remove('disabledbutton');
    }
}
function rates_currency_filter (){
    elem=document.getElementById('currency_select');
    currency=elem[elem.selectedIndex].value;
    $(".rates_select option").each(function(){
        if (this.getAttribute('data-currency')!==currency)this.style.display='none';
        else this.style.display='inherit';
    });
}

function service_no_checked(elem){
    service_no=document.getElementById('service_no')
    if (elem.checked===true)service_no.disabled=true;
    else service_no.disabled=false;
}
function reset_rates(){
    if (!confirm('Reset rates?'))return 0;
    let targ = document.getElementById('entries_table');
    let service_our_comp=document.getElementById('service_our_comp').value;
    let formData = new FormData();
    formData.append('service_our_comp',service_our_comp);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) {
                targ.innerHTML = req.responseText;
                total_calc();
            }
	}
    };
    req.open('POST', '/ajax/service_calc_reset.php');  
    req.send(formData); // отослать запрос
}
function service_view_save(form){
    var formData = new FormData(form);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
		if(req.responseText==='true'){
                    show_service_table();
                    alert('Successfully saved.')
                    }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/service_change.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function select_agent_contact(elem){
    let conteiner=elem.closest('.selector');
    let id= conteiner.querySelector('.selector_tosend_field').value;
    let sel=elem.parentNode.parentNode.querySelector("select[id='contact_id']");
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		sel.innerHTML =req.responseText;
            }
	}
    };
    req.open('POST', 'ajax/service_agent_select.php');  
    req.send(formData);  // отослать запрос
}
function invoice_name(elem){
    if (elem.checked){
        document.getElementById('invoice_num').disabled=true;
    }
    else{
        document.getElementById('invoice_num').disabled=false;
    }
}
function entry_freeline(elem){
    let t_body = elem.closest('TBODY');
    let line = document.getElementById('entry_freeline_tr').cloneNode(true);
    line.removeAttribute('ID');
    t_body.appendChild(line);
}
function entry_header(elem){
    let line = document.getElementById('entry_header_tr').cloneNode(true);
    line.removeAttribute('ID');
    let tr=elem.closest('TBODY');
    insertAfter(line,tr);
    set_numbers();
}
function entry_header_calc(elem){
    let line = document.getElementById('entry_header_calc_tr').cloneNode(true);
    line.removeAttribute('ID');
    let tr=elem.closest('TR');
    tr.parentNode.insertBefore(line,tr);
    set_numbers();
}
function entry_rate(elem){
    let t_body = elem.closest('TBODY');
    let line = document.getElementById('entry_rate_tr').cloneNode(true);
    line.removeAttribute('ID');
    t_body.appendChild(line);
}
function entry_spare(elem){
    let t_body = elem.closest('TBODY');
    let line = document.getElementById('entry_spare_tr').cloneNode(true);
    line.removeAttribute('ID');
    t_body.appendChild(line);
}
function calc_add_line(elem,val){
    let t_body = document.getElementById('entries_table_body');
    let line = document.getElementById('entry_rate_tr').cloneNode(true);
    line.removeAttribute('ID');
    t_body.insertBefore(line,elem.parentNode.parentNode);
}
function set_numbers(){
    $(".number_td").each(function(index){
        this.innerHTML='<strong>' + (index+1) + '</strong>';
    });
}
function sevice_delete_row(elem){
    var tr = elem.closest('TR');
    table=tr.closest('TABLE');
    table.deleteRow(tr.rowIndex);
    set_numbers();
    total_calc();
}
function delete_rate_cat(elem){
    var targ = elem.closest('TBODY');
    table=targ.parentNode;
    table.removeChild(targ);
    set_numbers();
    total_calc();
}
function total_calc(){
    let tbody=document.getElementById('entries_table');
    let total=0;
    for (var i=2; i< tbody.rows.length-1; i++){
        let row = tbody.rows[i];
        let qty = row.querySelector('.qty_input').value;
        let price = row.querySelector('.price_input').value;
        let discount=row.querySelector('.discount_input').value;
        let amount=qty*price*(1-(discount*0.01));
        row.querySelector('.amount_input').value=amount.toFixed(2);
        total+=amount;
    }
    document.getElementById('total').value=total.toFixed(2);
}
function user_select(elem){
    let targ = elem.parentNode.querySelector('#user_select3');
    if (targ.style.display!="block"){
        targ.style.display="block";
    }else {
        targ.style.display="none";
    }
}
function user_select_save(elem){
    let targ = elem.parentNode.parentNode.parentNode;
    let inp = targ.parentNode.querySelector("#user_select_input");
    let inp_string = "";
    let users=targ.querySelectorAll('input[type="checkbox"]');
    for (let i=0;i<users.length;i++){
        if (users[i].checked) {
            inp_string += users[i].getAttribute('data-uid') + ' ';
        }
    }
    inp.value=inp_string;
}
function user_select_close(elem){
    let targ = elem.closest('.calc_fancy_div');
    targ.querySelector('#user_select3').style.display="none";
}
var targ;
function service_add_equipment(elem){
    let conteiner = elem.closest('.window_div');
    targ=elem.previousSibling.querySelector("select[name='sfd_equip_id[]']");
    let wind = conteiner.querySelector("#service_add_eq_div");
    wind.style.display="block";
}
function service_add_equipment_save(){
    let formData = new FormData(document.forms.service_add_eqipment_form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
		if(req.responseText==='true'){
                    alert('Successfully saved.')
                    document.getElementById("service_add_eq_div").style.display='none';
                    selector_equip_long(targ);
                    }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/service_add_equipment.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function sfd_insert_row(elem){
    let row = document.getElementById('sfd_row_for_insert').cloneNode(true);
    row.removeAttribute('ID');
    let container = elem.closest('.window_internal');
    let table = container.querySelector('#service_required_table');
    table.appendChild(row);
}
function sfd_insert_row_new(elem){
    let row = document.getElementById('sfd_row_for_insert_new').cloneNode(true);
    row.removeAttribute('ID');
    elem.parentNode.parentNode.parentNode.insertBefore(row,elem.parentNode.parentNode);
}
function service_fault_selector(elem){
    let targ = elem.closest('tr');
    let equip_container = targ.querySelector('.selector_equip_container');
    let selector = targ.querySelector('select[name="sfd_type[]"]');
    let category = equip_container.getElementsByTagName('select')[0];
    equip_container.classList.remove('disabledbutton');
    if (selector.value == '3'){
        category.value=2;
        selector_equip_long(category);
    }
    else if (selector.value == '4'){
        category.value=3;
        selector_equip_long(category);
    }
    else if (selector.value == '2'){
        equip_container.classList.add('disabledbutton');
    }
}
function delete_equip_row(elem){
    if(confirm("Delete this record?")){
        elem.parentNode.parentNode.parentNode.removeChild(elem.parentNode.parentNode);
    }   
}
function selector_equip_long(elem){
    let wind_div = elem.closest('.window_div');
    let container = elem.parentNode;
    let category = container.childNodes[0].value;
    let manufacturer = container.childNodes[1].value;
    let selector = container.childNodes[2];
    let new_category =wind_div.querySelector('#new_equipment_cat');
    let new_manuf = wind_div.querySelector('#new_equipment_manuf');
    new_category.selectedIndex=container.childNodes[0].selectedIndex - 1;
    new_manuf.selectedIndex=container.childNodes[1].selectedIndex - 1;
    if (category=='' || manufacturer=='')selector.innerHTML='<option value="0"></option>';
    else {    
        let formData = new FormData();
        formData.append("category",category);
        formData.append("manufacturer",manufacturer);
        let req = getXmlHttp();
        req.onreadystatechange = function() {  
            if (req.readyState == 4) {
                if(req.status == 200) { 
                    selector.innerHTML =req.responseText;
                }
            }
        }
        req.open('POST', '/ajax/selector_equip_long.php');  
        req.send(formData);
    }
}
function service_view_form_submit(elem){   
    let container = elem.closest('.window_internal');
    let main_form = container.querySelector('#service_info_tab');
    let second_form = container.querySelector('#calculation_tab');
    if (! main_form.reportValidity()) return;
    let form1 = new FormData(main_form);
    let form2 = new FormData(second_form);
    // Merging form2 into form1
    for (const elem of form2.entries()) {
        form1.append(elem[0], elem[1]);
    }
//    console.log(form1.entries());
    let req = getXmlHttp();
        req.onreadystatechange = function() {  
            if (req.readyState === 4) {
                if(req.status === 200) { 
                    if(req.responseText==='true'){
                        show_service_table();
                        alert('Successfully saved.');
                    }
                    else alert(req.responseText);
                }
            }
        };
    req.open('POST', '/scripts/service_change.php');    
    req.send(form1);  // отослать запрос
    return false;
}