function adm_dispay(page){
    let statusElem = document.getElementById('main_div_menu');
    let comp = document.getElementById('adm_our_company');
    let status = document.getElementById('adm_status');
    let incharge = document.getElementById('adm_incharge');
    let customer = document.getElementById('adm_customer');
    let search = document.getElementById('adm_search');
    let formData = new FormData();
    formData.append("page", page);
    formData.append("status", status.value);
    formData.append("incharge", incharge.value);
    formData.append("comp_id", comp.value);
    formData.append("customer", customer.value);
    formData.append("search", search.value);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            statusElem.innerHTML = req.statusText; // показать статус (Not Found, ОК..)
            if(req.status === 200) { 
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'adm_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
}

function adm_new(){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
//    let comp=document.getElementById('service_our_company').value;
    let formData = new FormData;
//    formData.append("service_our_comp",comp);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                targ.innerHTML=req.responseText;
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', '/ajax/adm/adm_new_form.php');  
    req.send(formData);  // отослать запрос 
    targ.style.display='block';
    targ.innerHTML='Please, wait. Data is loaded...'; 
}

function adm_save(elem){
    let container = elem.closest('.window_internal');
    let form = container.querySelector('#adm_main_form');
    if (!form.reportValidity()) {return false;}
    let formData = new FormData(form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                let response=JSON.parse(req.responseText);
		if(response.result==='true'){
                    adm_dispay(1);
                    alert('Saved.');
                }
                else {
                    alert (response.error);
                }
            }
	}
    };
    req.open('POST', '/scripts/adm/adm_save.php');  
    req.send(formData);
}

function adm_update(elem){
    let container = elem.closest('.window_internal');
    let form = container.querySelector('#adm_main_form');
    if (!form.reportValidity()) {return false;}
    let formData = new FormData(form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                let response=JSON.parse(req.responseText);
		if(response.result==='true'){
                    if(window.location.pathname === '/adm.php'){ adm_dispay(1);}
                    alert('Saved.');
                }
                else {
                    alert (response.error);
                }
            }
	}
    };
    req.open('POST', '/scripts/adm/adm_update.php');  
    req.send(formData);
}

function adm_submit_log(elem,id){
    let container = elem.closest('.adm_logs');
    let text = container.querySelector('#adm_log_field');
    let formData = new FormData;
    formData.append('text',text.value);
    formData.append('id',id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                let response=JSON.parse(req.responseText);
                if(response.result==='true'){
                    text.value = '';
                    adm_refresh_logs(elem, id);
                    alert('Added successfully.');
                } else alert(response.error);                
            }
	}
    };
    req.open('POST', '/scripts/adm/adm_add_log.php');  
    req.send(formData);
}

function adm_refresh_logs(elem, id){
    let container = elem.closest('.adm_logs');
    let targ = container.querySelector('.adm_log_container');
    let formData = new FormData;
    formData.append('id',id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                targ.innerHTML = req.responseText;             
            }
	}
    };
    req.open('POST', '/scripts/adm/adm_log_ajax_update.php');  
    req.send(formData);
}