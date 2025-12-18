function show_contracts_table (page){
    let statusElem = document.getElementById('main_div_menu') 
    let formData = new FormData();
    //let search=document.getElementById('service_search').value;
    let our_company=document.getElementById('contracts_our_company').value;
    let contract_customer=document.getElementById('contract_customer').value;
    let contract_search=document.getElementById('contract_search').value;
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("our_company", our_company);
    formData.append("contract_customer", contract_customer);
    formData.append("contract_search", contract_search);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'contracts_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
 }
function contract_view(contract_id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("contract_id", contract_id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		targ.innerHTML =req.responseText;
                targ.style.display='block';
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });
                //cross_docs_load(targ,2,sales_our_comp,sales_no);
                uploaded_files_show(0,contract_id,'contracts',targ);
            }
	}
    };
    req.open('POST', 'contract_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function contracts_new(){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		targ.innerHTML =req.responseText;
                targ.style.display='block';
                $(".datepicker" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        firstDay:1,
                        dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', 'ajax/contracts_new_form.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function contracts_new_submit(elem){
    let form=elem.closest('FORM');
    let formData = new FormData(form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) {
                let response=JSON.parse(req.responseText);
		if(response.result==='true'){
                    window_close(elem);
                    show_contracts_table(1);;
                }
                else alert(req.responseText);
            }
	}
    };
    if (form.reportValidity()){
        req.open('POST', 'scripts/contracts_new.php');  
        req.send(formData);  // отослать запрос
    }
}
function contracts_change(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
		if(req.responseText==='true'){
                    alert('Successfully saved.');
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/contracts_change.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function contracts_view_form_submit(targ){
    let form=targ.closest('.window_internal').querySelector('.contracts_view_form');
    if (form.reportValidity()) {
        return contracts_change(form);
    }
}