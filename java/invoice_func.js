function show_invoice_table(page){
    let statusElem = document.getElementById('main_div_menu');
    let invoice_status = document.getElementById('invoice_status').value;
    let invoice_customer = document.getElementById('invoice_customer').value;
    let invoice_our_comp = document.getElementById('invoice_our_comp').value;
    let invoice_currency = document.getElementById('invoice_currency').value;
    let invoice_bank = document.getElementById('invoice_bank').value;
    let invoice_date_from = document.getElementById('invoice_date_from').value;
    let invoice_date_to = document.getElementById('invoice_date_to').value;
    let invoice_search = document.getElementById('invoice_search').value;
    let formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("invoice_status", invoice_status);
    formData.append("invoice_customer", invoice_customer);
    formData.append("invoice_our_comp", invoice_our_comp);
    formData.append("invoice_currency", invoice_currency);
    formData.append("invoice_bank", invoice_bank);
    formData.append("invoice_date_from", invoice_date_from);
    formData.append("invoice_date_to", invoice_date_to);
    formData.append("invoice_search", invoice_search);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText;
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
	}
    };
    req.open('POST', 'invoices_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = 'Ожидаю ответа сервера...'; 
        
 }
function show_invoice_in_table(page){
    let statusElem = document.getElementById('main_div_menu');
    let invoice_status = document.getElementById('invoice_status').value;
    let invoice_customer = document.getElementById('invoice_customer').value;
    let invoice_our_comp = document.getElementById('invoice_our_comp').value;
    let invoice_currency = document.getElementById('invoice_currency').value;
    let invoice_date_from = document.getElementById('invoice_date_from').value;
    let invoice_date_to = document.getElementById('invoice_date_to').value;
    let invoice_search = document.getElementById('invoice_search').value;
    let formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("invoice_status", invoice_status);
    formData.append("invoice_customer", invoice_customer);
    formData.append("invoice_our_comp", invoice_our_comp);
    formData.append("invoice_currency", invoice_currency);
    formData.append("invoice_date_from", invoice_date_from);
    formData.append("invoice_date_to", invoice_date_to);
    formData.append("invoice_search", invoice_search);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText;
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
	}
    };
    req.open('POST', 'invoices_in_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = 'Ожидаю ответа сервера...'; 
        
 }
function invoice_new(type,num){
    let targ = document.createElement("DIV");
    let formData = new FormData;
    formData.append('invoice_order_type',type);
    if(typeof(num) !== "undefined" && num !== null) formData.append('invoice_order_num',num);    
    targ.classList.add('window_div');
    document.body.appendChild(targ);
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
            }
	}
    };
    req.open('POST', 'ajax/invoice_new_form.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function invoice_new_form(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) {
                //alert(req.responseText);
                let response=JSON.parse(req.responseText);
		if(response.result==='true'){
                    let targ = elem.closest('.window_div');
                    document.body.removeChild(targ);
                    invoice_view_by_id(response['invoice_id']);
                    show_invoice_table(1);
                }
                else alert(response.error);
            }
	}
    };
    req.open('POST', '/scripts/invoice_new.php');  
    req.send(formData);  // отослать запрос
    return false; 
}

function invoice_bank_details(elem){
    show_invoice_table(elem);
    get_our_bank_det(elem);
}
function invoice_add_content(elem){
    let conteiner = elem.closest('.window_internal');
    let tbody = conteiner.querySelector('.invoice_body_table');
    let row = conteiner.querySelector('#invoice_quotation_new_line');
    let new_row=row.cloneNode(true);
    new_row.classList.add('quotation_line');
    new_row.id='';
    tbody.appendChild(new_row);
    invoice_set_numbers(new_row);
}

function invoice_add_text_line(elem){
    let conteiner = elem.closest('.window_internal');
    let tbody = conteiner.querySelector('.invoice_body_table');
    let row = conteiner.querySelector('#invoice_quotation_text_line');
    let new_row=row.cloneNode(true);
    new_row.id='';
    tbody.appendChild(new_row);
    invoice_set_numbers(new_row);
}
function invoice_delete_row(elem){
    let conteiner = elem.closest('.quotation_line');
    let body_div=conteiner.parentNode;
    conteiner.parentNode.removeChild(conteiner);
    invoice_set_numbers(body_div);
    //sales_total(body_div);
}
function invoice_set_numbers(elem){
    let conteiner = elem.closest('.window_internal');
    let targ=conteiner.querySelectorAll('.quotation_line');
    let max = targ.length;
    for (i=0; i<max; i++){
        if (targ[i].querySelector('.po_col_no'))
        targ[i].querySelector('.po_col_no').innerHTML=(i+1);
    }
}
function invoice_row_up(elem){
    let row = elem.closest('.quotation_line');
    if (row.parentNode.firstElementChild === row) return;
    row.parentNode.insertBefore(row, row.previousElementSibling);
    invoice_set_numbers(elem);
}
function invoice_row_down(elem){
    let row = elem.closest('.quotation_line');
    if (row.parentNode.lastElementChild === row) return;
    insertAfter(row, row.nextElementSibling);
    invoice_set_numbers(elem);
}
function invoice_view(invoice_our_comp,invoice_num){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("invoice_our_comp", invoice_our_comp);
    formData.append("invoice_num", invoice_num);
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
                //uploaded_files_show(sales_our_comp,sales_no,'sales',targ);
                cross_docs_load(targ,4,invoice_our_comp,invoice_num)
                
            }
	}
    };
    req.open('POST', 'invoice_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">'; 
}
function invoice_view_by_id(invoice_id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("invoice_id", invoice_id);
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
                //uploaded_files_show(sales_our_comp,sales_no,'sales',targ);
                cross_docs_load(targ,4,invoice_our_comp,invoice_num)
                
            }
	}
    };
    req.open('POST', 'invoice_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">'; 
}
function invoice_change_form(elem,type){
    let container = elem.closest('.window_internal');
    let main_form = container.querySelector('#invoice_main_form');
    let second_form = container.querySelector('#shipment');
    if (! main_form.reportValidity()) return;
    let form1 = new FormData(main_form);
    let form2 = new FormData(second_form);
    // Merging form2 into form1
    for (const elem of form2.entries()) {
        form1.append(elem[0], elem[1]);
    }
    
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
                if (req.responseText === 'true'){
                    alert('Successfully changed.');
                    if (type === 2 ) show_invoice_table();
                    else show_invoice_in_table();
                }
                else alert(req.responseText);
            };
	};
    };
    req.open('POST', '/scripts/invoice_change.php');  
    req.send(form1);
    return false;
}
function invoice_add_payment(elem,id){
    let conteiner = elem.closest('.window_internal');
    let targ = document.createElement("DIV");
    targ.classList.add('window_internal');
    conteiner.appendChild(targ);
    let formData = new FormData();
    formData.append("invoice_id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		targ.innerHTML =req.responseText;
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });                
            }
	}
    };
    req.open('POST', 'ajax/invoice_payment_add.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">'; 
}
function invoice_payment_new(elem,id){
    let targ = elem.closest("FORM");
    let formData = new FormData(targ);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    alert ('Payment added.');
                    invoice_payments_reload(elem,id);
                    invoice_payments_close(elem);
                }
                else alert(req.responseText); 
            }
	}
    };
    req.open('POST', 'scripts/invoice_payment_add.php');  
    req.send(formData);  // отослать запрос
}
function invoice_payments_reload(elem,id){
    let conteiner = elem.closest('.window_div');
    let targ = conteiner.querySelector('#grid_invoice_details');
    let formData = new FormData();
    formData.append("invoice_id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		targ.innerHTML =req.responseText;            
            }
	}
    };
    req.open('POST', 'scripts/invoice_payment_reload.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function invoice_payments_close(elem){
    let targ = elem.closest('.window_internal');
    targ.parentNode.removeChild(targ);
}
function invoice_payment_edit(elem,id){
    let conteiner = elem.closest('.window_internal');
    let targ = document.createElement("DIV");
    targ.classList.add('window_internal');
    targ.style.width="500px";
    conteiner.appendChild(targ);
    let formData = new FormData();
    formData.append("pay_id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		targ.innerHTML =req.responseText;
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });                
            }
	}
    };
    req.open('POST', 'ajax/invoice_payment_change.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function invoice_payment_change(elem,id){
    let targ = elem.closest("FORM");
    let formData = new FormData(targ);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    alert ('Payment changed.');
                    invoice_payments_reload(elem,id);
                    invoice_payments_close(elem);
                }
                else alert(req.responseText); 
            }
	}
    };
    req.open('POST', 'scripts/invoice_payment_change.php');  
    req.send(formData);  // отослать запрос
}