function po_new(){
    let targ = document.createElement("DIV");
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
    req.open('POST', 'ajax/purchase_new_form.php');  
    req.send();  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
    return false;
}
function po_add_content(elem){
    let conteiner = elem.closest('.window_internal');
    let tbody = conteiner.querySelector('.po_quotation_table');
    let row = conteiner.querySelector('#po_quotation_new_line');
    let new_row=row.cloneNode(true);
    new_row.id='';
    tbody.appendChild(new_row);
    po_set_numbers(new_row);
}
function po_delete_row(elem){
    let conteiner = elem.closest('.po_quotation_line');
    let body_div=conteiner.parentNode;
    conteiner.parentNode.removeChild(conteiner);
    po_set_numbers(body_div);
    qte_total(body_div);
    //sales_total(body_div);
}
function po_set_numbers(elem){
    let conteiner = elem.closest('.window_internal');
    let targ=conteiner.querySelectorAll('.po_quotation_line');
    let max = targ.length;
    for (i=0; i<max; i++){
        targ[i].querySelector('.po_col_no').innerHTML=(i+1);
    }
}
function live_search(elem){
    let tr=elem.closest('.quotation_line');
    let div=tr.querySelector('.selector_search_div');
    div.innerHTML = 'Please wait...';
    div.style.display = 'block';
    div.style.width = elem.clientWidth+'px';
    if (elem.value.length>=2){
        var req = getXmlHttp();
        req.onreadystatechange = function() {  
            if (req.readyState === 4){
		div.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status === 200){ 
                    div.innerHTML =req.responseText;
                }
            }
        };
        var formDataSearch = new FormData();
        formDataSearch.append("data", elem.value);
        formDataSearch.append("index", elem.parentNode.cellIndex);
        req.open('POST', 'ajax/sales_search.php');  
	req.send(formDataSearch);  // отослать запрос
       }
    else {
       div.style.display = 'none'; 
       div.removeChild(div.firstChild);
    }
}
function sales_inp_blur_inner(elem){
    let tr=elem.closest('.quotation_line');
    let div=tr.querySelector('.selector_search_div');
    div.innerHTML="";
    div.style.display="none";
}
function show_purchase_table (page){
    let statusElem = document.getElementById('main_div_menu');
    let status = document.getElementById('purchase_status').value;
    let search = document.getElementById('purchase_search').value;
    let po_our_comp  = document.getElementById('po_our_comp').value;
    let po_supplier = document.getElementById('po_supplier').value;
    let po_date_from  = document.getElementById('po_date_from').value;
    let po_date_to  = document.getElementById('po_date_to').value;
    let po_content=document.getElementById('po_content').value;
    let formData = new FormData();
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("page", page);
    formData.append("status", status);
    formData.append("keyword", search);
    formData.append("po_our_comp", po_our_comp);
    formData.append("po_supplier", po_supplier);
    formData.append("po_date_from", po_date_from);
    formData.append("po_date_to", po_date_to);
    formData.append("po_content", po_content);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'purchase_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
 }
function po_row_up(elem){
    let row = elem.closest('.po_quotation_line');
    if (row.parentNode.firstElementChild === row) return;
    row.parentNode.insertBefore(row, row.previousElementSibling);
    po_set_numbers(elem);
}
function po_row_down(elem){
    let row = elem.closest('.po_quotation_line');
    if (row.parentNode.lastElementChild === row) return;
    insertAfter(row, row.nextElementSibling);
    po_set_numbers(elem);
}
function stnmc_view_add(elem){
    let conteiner = elem.closest('TD');
    let target = conteiner.querySelector('.selector_nmnc_tosend');
    nmnc_view(target.value);
}
function add_new_purchase(elem){
    let container = elem.closest('.window_internal');
    let form = container.querySelector('#po_main_form');
    if (! form.reportValidity()) return;
    let formData = new FormData(form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
                let result =JSON.parse(req.responseText);
                if (result.result=='true'){
                    window_close(elem);
                    view_link(result.po_no);
                }
                else alert(result.error);
            };
	};
    };
    req.open('POST', '/scripts/purchase_new.php');  
    req.send(formData);
    return false;
}
function purchase_change(elem){
    let container = elem.closest('.window_internal');
    let targ = container.querySelector('#po_main_form');
    let po_note = container.querySelector('#po_note').value;
    let po_print_note = container.querySelector('#po_print_note').value;
    let formData = new FormData(targ);
    formData.append("po_note", po_note);
    formData.append("po_print_note", po_print_note);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
                if (req.responseText==='true'){
                    alert('Successfully changed.');
                    show_purchase_table();
                }
                else alert(req.responseText);
            };
	};
    };
    req.open('POST', '/scripts/purchase_change.php');  
    req.send(formData);
}
function purchase_convert_to_invoice(po_no){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("po_no", po_no);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) { 
		targ.innerHTML = req.responseText;
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
    req.open('POST', 'ajax/purchase_convert_to_invoice.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function purchase_convert_to_stock(po_id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("po_id", po_id);
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
    req.open('POST', 'ajax/stock_multiinsert_from_po.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function purchase_display_over(elem){
    var div=document.createElement("DIV");
    div.classList.add('float_div');
    elem.appendChild(div);
    elem.onmouseleave=function(){
        div.remove();
    };
    var formData = new FormData();
    formData.append("id", elem.getAttribute('data-id'));
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                div.innerHTML = req.responseText;
            }
        }
    };
    req.open('POST', '/ajax/purchase_float_div.php');  
    req.send(formData);  // отослать запрос
    div.innerHTML = 'Ожидаю ответа сервера...';
}