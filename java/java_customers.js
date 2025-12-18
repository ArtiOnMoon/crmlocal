function cancel_customers(){
    document.getElementById('new_company').style.display="none";
    document.getElementById('wrap').style.display="none";
};
function add_new_customer() {
    var statusElem = document.getElementById('vote_status') 
    var formData = new FormData(document.forms.new_company);
    var req = getXmlHttp();
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
    req.open('POST', 'add_customer.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="./img/loading.gif">';    
}
function add_new_customer_ajax(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                // если статус 200 (ОК) - выдать ответ пользователю
                if(req.responseText==='true'){
                    alert('Customer added successfully.');
                    window_close(elem);
                }
                else alert(req.responseText);
            }
        }
    }
    req.open('POST', 'add_customer.php');  
    req.send(formData);  // отослать запрос 
    return false;
}
function show_customers_table (page){
    var statusElem = document.getElementById('main_div_menu')
    var search=document.getElementById('search').value;
    var country=document.getElementById('cust_filter_country').value;
    var exclude=document.getElementById('cust_exclude').checked;
    var is_mnfr=document.getElementById('is_mnfr').checked;
    var is_mngr=document.getElementById('is_mngr').checked;
    var is_sppl=document.getElementById('is_sppl').checked;
    var is_agnt=document.getElementById('is_agnt').checked;
    var is_ownr=document.getElementById('is_ownr').checked;
    var is_fchk=document.getElementById('is_fchk').checked;
    var is_optr=document.getElementById('is_optr').checked;
    var is_serv=document.getElementById('is_serv').checked;
        
    var formData = new FormData();
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    
    if (country!='All'){
        formData.append("country", country);
        if (exclude)formData.append("exclude_country", exclude);
    }
    if (is_mnfr)formData.append("is_mnfr", 1);
    if (is_mngr)formData.append("is_mngr", 1);
    if (is_sppl)formData.append("is_sppl", 1);
    if (is_agnt)formData.append("is_agnt", 1);
    if (is_ownr)formData.append("is_ownr", 1);
    if (is_serv)formData.append("is_serv", 1);
    if (is_optr)formData.append("is_optr", 1);
    if (is_fchk)formData.append("is_fchk", 1);
    formData.append("page", page);
    formData.append("search", search);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) {
                statusElem.innerHTML =req.responseText;
            }
	}
    }
    req.open('POST', 'customers_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
 }
function cust_edit (id){
    if (id==1 || id==='')return;
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    var formData = new FormData();
    formData.append("id", id);
    var req = getXmlHttp()  
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		targ.innerHTML =req.responseText;
                targ.style.display='block';
            }
	}
    };
    req.open('POST', 'customer_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
 }
function add_vessel(id){
    var formData = new FormData(document.forms.new_vessel_form);
    var req = getXmlHttp()  
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    cust_edit(id);
                    }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', 'vessel_add.php');  
    req.send(formData);  // отослать запрос
    return false;
} 
function change_customer(page){
    let formData = new FormData(document.forms.customer_form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
		if(req.responseText==='true'){
                    let targ = document.forms.customer_form.closest('.window_div');
                    document.body.removeChild(targ);
                    show_customers_table(page);
                    }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', 'change_customer.php');  
    req.send(formData);  // отослать запрос
    return false;
 }
function go_to(){
    var num=document.getElementById('go_to').value;
    show_customers_table (num);
}
// Show customer window
function click_catch(e){
    var target=e.target;
    if (target.tagName == 'A') {
        if (target.getAttribute('data-id')) cust_edit (target.getAttribute('data-id'));
    }
}
function check_delete(){
if (confirm("Delete this record?"))
    return true;
    else return false;
}
function edit_contact_display(id){
    var div=document.getElementById('edit_contact');
    div.style.display="block";
    var formData = new FormData();
    formData.append("id", id);
    var req = getXmlHttp()  
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            div.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                div.innerHTML =req.responseText;
            }
        }
    }
    req.open('POST', 'customers_edit_contact.php');  
    req.send(formData);
    div.innerHTML = 'Ожидаю ответа сервера...';
}
function edit_contact(cust_id,elem){
    var formData = new FormData(document.forms.edit_contact_form);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                if (req.responseText=='true'){
                    window_close(elem);
                    cust_edit(cust_id)  
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', 'customers_change_contact.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function new_contact(cust_id,elem){
    var formData = new FormData(document.forms.new_contacts_form);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                if (req.responseText=='true'){
                    cancel('new_contacts');
                    window_close(elem);
                    cust_edit(cust_id)  
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', 'new_contact.php');  
    req.send(formData);  // отослать запрос
    return false;
} 
function send_email(){
    if ($( ".table_checkbox:checked" ).length==0){
        alert ('Nothing selected');
        return;
    }
    let href='mailto:team@az-marine.com?bcc=';
    let list = document.getElementsByClassName('table_checkbox');
    let max = list.length;
    for (var i=0; i<max; i++){
        if (list[i].checked){
            href+=list[i].getAttribute('data-email')+',';
        }
    }
    location.href=href;
}
function delete_contact_row(elem){
    elem.parentNode.parentNode.remove();
}
function add_contact_row(){
    var new_row=document.getElementById('contact_row').cloneNode(true);
    new_row.id='';
    document.getElementById('contacts_tbody').appendChild(new_row);    
}
function add_branch(cust_id){
    var formData = new FormData(document.forms.new_branch_form);
    var req = getXmlHttp()
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    cust_edit(cust_id);
                    window.location.hash="tab2";
                    }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', 'customers_new_branch.php');  
    req.send(formData);  // отослать запрос   
    return false;
}
function invoicing_address_check(elem){
    if (elem.checked)
    {
        document.getElementById('inv_addr_1').disabled=true;
        document.getElementById('inv_addr_2').disabled=true;
    }
    else
    {
        document.getElementById('inv_addr_1').disabled=false;
        document.getElementById('inv_addr_2').disabled=false;
    }
}
