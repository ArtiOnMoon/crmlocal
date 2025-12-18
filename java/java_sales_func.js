function sale_cancel(){
    document.getElementById('new_sale').style.display="none";
    document.getElementById('wrap').style.display="none";
};
function add_new_sale2(fm){
    var table = document.getElementById('sale_content');
    var data='';
    var input;
    var tr=table.getElementsByTagName('tr');
    if (tr.length>1){
        for (var i = 1; i < tr.length; i++) { 
            if (i>1) {data=data+',';}
            input=tr[i].getElementsByTagName('input');
            //type error
            data=data+input[0].value;       
        }
    }
    fm.elements['content'].value=data;
    
}
function live_search(elem,sales_our_comp){
    let tr=elem.closest('.sales_quotation_line');
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
        formDataSearch.append("sales_our_comp", sales_our_comp);
        req.open('POST', 'ajax/sales_search.php');  
	req.send(formDataSearch);  // отослать запрос
       }
    else {
       div.style.display = 'none'; 
       div.removeChild(div.firstChild);
    }
}
function livesearch_selected(event){
    let targ = event.target.closest('DIV');
    let tr=targ.closest('TR');
    //let discount=tr.closest('.window_div').querySelector('.customer_conteiner').querySelector('.selector_tosend_field').getAttribute('data-discount');
    let t_id=targ.getAttribute('data-id');
    let t_val=targ.getAttribute('data-value');
    //let t_price=targ.getAttribute('data-price');
    //let t_currency=targ.getAttribute('data-currency');
    targ.parentNode.parentNode.parentNode.querySelector('.inp_base_id').value=t_id;
    targ.parentNode.parentNode.parentNode.querySelector('.inp_text').value=t_val;
    //tr.querySelector('.inp_discount').value=discount;
    //tr.querySelector('.inp_currency').value=t_currency;
    //tr.querySelector('.inp_price').value=t_price;
    sales_total(tr);
}
function sales_inp_blur(elem){
    let delay300=delay(sales_inp_blur_inner,300)
    delay300(elem);
}
function sales_inp_blur_inner(elem){
    let tr=elem.closest('.sales_quotation_line');
    let div=tr.querySelector('.selector_search_div');
    div.innerHTML="";
    div.style.display="none";
}
function delete_row(){
    table=document.getElementById('sale_content');
    var tr = this.parentNode.parentNode; 
    table.deleteRow(tr.rowIndex);
    document.getElementById('confirm_button').style.background="white";
}
function show_sales_table (page){
    let statusElem = document.getElementById('main_div_menu');
    let formData = new FormData();
    if (page !==undefined)formData.append("page", page);
    let stat=document.getElementById('sales_status').value;
    let our_comp=document.getElementById('sales_our_comp').value;
    let search=document.getElementById('sales_search').value;
    let date_start=document.getElementById('date_start').value;
    let date_end=document.getElementById('date_end').value;
    let sales_customer=document.getElementById('sales_customer').value;
    let sales_content=document.getElementById('sales_content').value;
    let sales_vessel=document.getElementById('sales_vessel').value;
    formData.append("stat", stat);
    formData.append("sales_our_comp", our_comp);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("search", search);
    formData.append("date_start", date_start);
    formData.append("date_end", date_end);
    formData.append("sales_customer", sales_customer);
    formData.append("sales_content", sales_content);
    formData.append("sales_vessel", sales_vessel);
    statusElem.innerHTML = 'Step 1';
    let req = getXmlHttp(); 
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                statusElem.innerHTML = req.responseText;
            }
        }
    };
    req.open('POST', 'sales_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = 'Loading...';
}
function fast_search(){
    var tab = document.getElementById('sales_table');
    var max = tab.rows.length;
    var search = document.getElementById('stock_search').value;
    for (var i=1; i<max; i++){
        var t = tab.rows[i];
                if ((t.cells[3].innerHTML.indexOf(search) > -1) || (t.cells[5].innerHTML.indexOf(search) > -1) || (t.cells[4].innerHTML.indexOf(search) > -1)) {t.style.display = '';} else
            t.style.display = 'none';
            if (search=='') {t.style.display = ''; continue;}
    }
}
function sales_row_up(elem){
    let row = elem.closest('.sales_quotation_line');
    if (row.parentNode.firstElementChild === row) return;
    row.parentNode.insertBefore(row, row.previousElementSibling);
    sales_set_numbers(elem);
}
function sales_row_down(elem){
    let row = elem.closest('.sales_quotation_line');
    if (row.parentNode.lastElementChild === row) return;
    insertAfter(row, row.nextElementSibling);
    sales_set_numbers(elem);
}
function sales_copy_line(elem){
    table_copy_line(elem);
    sales_set_numbers(elem);
    sales_total(elem);
}
function sales_delete_row(elem){
    let conteiner = elem.closest('.sales_quotation_line');
    let body_div=conteiner.parentNode;
    conteiner.parentNode.removeChild(conteiner);
    sales_set_numbers(body_div);
    sales_total(body_div);
}
function sales_set_numbers(elem){
    let conteiner = elem.closest('.window_internal');
    let targ=conteiner.querySelectorAll('.sales_quotation_line');
    let max = targ.length;
    for (i=0; i<max; i++){
        targ[i].querySelector('.sales_quotation_index').innerHTML=(i+1);
    }
}
function sales_total(elem){
    let conteiner = elem.closest('.window_internal');
    let total = conteiner.querySelector('#total_field');
    let vat_field=conteiner.querySelector('#total_vat_field');
    let total_cfm_field = conteiner.querySelector('#total_cfm_field');
    let vat_cfm=conteiner.querySelector('#total_cfm_vat');
    let targ=conteiner.querySelectorAll('.sales_quotation_line');
    let total_num=0;
    let total_vat=0;
    let total_cfm=0;
    let total_cfm_vat=0;
    for (let i=0; i<(targ.length); i++){
        let qty = targ[i].querySelector('.inp_qty').value;
        let cfm_qty = targ[i].querySelector('.inp_cfm_qty').value;
        let price = targ[i].querySelector('.inp_price').value;
        let rate = targ[i].querySelector('.inp_currency_rate').value;
        let vat = targ[i].querySelector('.inp_vat').value;
        let discount = targ[i].querySelector('.inp_discount').value;
        let amount=qty*price*rate*(1 - discount/100);
        let cfm_amount=cfm_qty*price*rate*(1 - discount/100);
        let vat_buf=amount - amount/(1 + vat/100);
        targ[i].querySelector('.inp_amount').value=amount.toFixed(2);
        targ[i].querySelector('.inp_cfm_amount').value=cfm_amount.toFixed(2);
        total_num+=amount;
        total_vat+=vat_buf;
        total_cfm+=cfm_amount;
        total_cfm_vat+=cfm_amount - cfm_amount/(1 + vat/100);
    }
    total.value=total_num.toFixed(2);
    vat_field.value=total_vat.toFixed(2);
    total_cfm_field.value=total_cfm.toFixed(2);
    vat_cfm.value=total_cfm_vat.toFixed(2);
}
//New_sales_functions
function sales_livesearch_price(elem){
    //finding TR
    var tr=elem;
    while (tr.tagName!=='TR'){
        tr=tr.parentNode;
        if (tr==document){
            alert ('Error, code 1');
            return;
        }
    }
    var div=tr.getElementsByTagName('div')[0];
    var type=tr.getElementsByTagName('select')[0];
    if (elem.value.length>=2){
        var req = getXmlHttp()   
            req.onreadystatechange = function() {  
            if (req.readyState == 4) {
		div.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                	div.innerHTML =req.responseText;
                    }
		}
            }
        div.style.display='block';
        var formDataSearch = new FormData();
        formDataSearch.append("data", elem.value);
        formDataSearch.append("type", type[type.selectedIndex].value);
        formDataSearch.append("index", elem.getAttribute('data-field'));
        req.open('POST', 'sales_search.php');  
	req.send(formDataSearch);  // отослать запрос
       }
    else {
       div.style.display = 'none'; 
       div.innerHTML='';
    }
}
function sales_livesearch_stock(elem){
    //finding TR
    var tr=elem;
    while (tr.tagName!=='TR'){
        tr=tr.parentNode;
        if (tr==document){
            alert ('Error, code 1');
            return;
        }
    }
    var div=tr.getElementsByTagName('div')[1];
    div.style.right='0px';
    var type=tr.getElementsByTagName('select')[0];
    var req = getXmlHttp()   
    req.onreadystatechange = function() {  
            if (req.readyState == 4) {
		div.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                	div.innerHTML =req.responseText;
                    }
		}
            }
    div.style.display='block';
    var formDataSearch = new FormData();
    formDataSearch.append("data", elem.value);
    formDataSearch.append("type", type[type.selectedIndex].value);
    req.open('POST', 'sales_search_stock.php');  
    req.send(formDataSearch);  // отослать запрос
}
function sales_select_currency(elem){
    document.getElementById('sales_currency').value=elem[elem.selectedIndex].getAttribute('data-currency');
}
function add_content(elem){
    let conteiner = elem.closest('.window_internal');
    let tbody = conteiner.querySelector('#sale_content').getElementsByTagName("TBODY")[0];
    let row = conteiner.querySelector('#sale_content_tr');
    new_row=row.cloneNode(true);
    new_row.id='';
    tbody.appendChild(new_row);
    sales_set_numbers(elem);
}
function sales_quotation_add_content(elem){
    let conteiner = elem.closest('.window_internal');
    let tbody = conteiner.querySelector('.sales_quotation_conteiner');
    let row = conteiner.querySelector('#sales_quotation_new_line');
    let new_row=row.cloneNode(true);
    new_row.id='';
    tbody.appendChild(new_row);
    sales_set_numbers(elem);
}
function add_expenses(){
    var tbody = document.getElementById('sale_exp_table').getElementsByTagName("TBODY")[0];
    var row = document.getElementById('sale_expenses_tr');
    new_row=row.cloneNode(true);
    new_row.id='';
    tbody.appendChild(new_row);
}
function data_selected_price(obj){
    var tr=obj.parentNode.parentNode.parentNode.parentNode.parentNode;
    var div=tr.getElementsByTagName('div')[0];
    var input1=tr.getElementsByTagName('input');
    var input2=obj.getElementsByTagName('td');
    input1[0].value=input2[1].innerHTML;
    input1[1].value=input2[2].innerHTML;
    input1[2].value=input2[3].innerHTML;
    div.style.display = 'none';
    div.innerHTML='';
    total();
    } 
function table_data_selected(event, flag){
    alert ('test');
    return;
    var elem=event.target;
    while (elem.tagName !='TABLE'){
        if (elem.tagName=='TR'){
            if (flag=='price') data_selected_price(elem);
            else if (flag=='stock') data_selected_stock(elem);        
            return;
        }
        elem=elem.parentNode;
    }
}
function data_selected_stock(obj){
    var tr=obj.parentNode.parentNode.parentNode.parentNode.parentNode;
    var div=tr.getElementsByTagName('div')[1];
    var input1=tr.getElementsByTagName('input');
    var input2=obj.getElementsByTagName('td');
    input1[3].value=input2[3].innerHTML;
    input1[4].value=input2[0].innerHTML;
    div.style.display = 'none';
    div.innerHTML='';
    total();
}

function sales_display_over(elem){
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
    req.open('POST', '/ajax/sales_show_div.php');  
    req.send(formData);  // отослать запрос
    div.innerHTML = 'Ожидаю ответа сервера...';
}
function close_search_div(elem){
    var div=elem.parentNode.parentNode;
    div.style.display='none';   
}
function hide_stock_liveserach(elem){
    //finding TR
    var tr=elem;
    while (tr.tagName!=='TR'){
        tr=tr.parentNode;
        if (tr==document){
            alert ('Error, code 1');
            return;
        }
    }
    var div=tr.getElementsByTagName('div')[1];
    div.style.display="none";
}
function sales_shipped_to_func(elem){
    let conteiner = elem.closest('.window_internal');
    let conteiner1 = conteiner.querySelector('.shipped_to_conteiner1');
    let conteiner2 = conteiner.querySelector('.shipped_to_conteiner2');
    if (elem.value==='0'){
        conteiner1.classList.add('disabled');
        conteiner2.classList.add('disabled');
    }else if(elem.value==='1'){
        conteiner1.classList.remove('disabled');
        conteiner2.classList.add('disabled');
    }else{
        conteiner1.classList.add('disabled');
        conteiner2.classList.remove('disabled');
    }
}
function sales_shipped_from_func(elem){
    let conteiner = elem.closest('.window_internal');
    let conteiner1 = conteiner.querySelector('.shipped_from_conteiner1');
    let conteiner2 = conteiner.querySelector('.shipped_from_conteiner2');
    if (elem.value==='0'){
        conteiner1.classList.add('disabled');
        conteiner2.classList.add('disabled');
    }else if(elem.value==='1'){
        conteiner1.classList.remove('disabled');
        conteiner2.classList.add('disabled');
    }else{
        conteiner1.classList.add('disabled');
        conteiner2.classList.remove('disabled');
    }
}
function sales_new(){
    let our_comp = document.getElementById('sales_our_comp');
    if (our_comp.value === ''){
        alert ('No company selected');
        return;
    }
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("sales_our_comp", our_comp.value);
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
    req.open('POST', 'ajax/sales_new_form.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function sales_new_form(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) {
                let response=JSON.parse(req.responseText);
		if(response.result==='true'){
                    let targ = elem.closest('.window_div');
                    document.body.removeChild(targ);
                    show_sales_table(1);
                    view_link(response['order']);
                }
                else alert(response.error);
            }
	}
    };
    req.open('POST', '/scripts/sales_new.php');  
    req.send(formData);  // отослать запрос
    return false;    
}
function sales_view_form_submit(targ){
    let container = targ.closest('.window_internal');
    let main_form = container.querySelector('#general');
    let second_form = container.querySelector('#package');
    if (! main_form.reportValidity()) return;
    let form1 = new FormData(container.querySelector('#general'));
    let form2 = new FormData(container.querySelector('#package'));
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
                        alert('Successfully saved.');
                    }
                    else alert(req.responseText);
                }
            }
        };
    req.open('POST', 'change_sales.php');  
    req.send(form1);  // отослать запрос
    return false;
}
function sales_close(elem){
    window_close(elem);
    show_sales_table();
}
function sales_second_row_switch(elem){
        let conteiner = elem.closest('.sales_quotation_line');
        let switcher = conteiner.querySelector('.sales_quotation_switch');
        let second_line = conteiner.querySelector('.sales_quotation_second_line');
        let sublink_div = conteiner.querySelector('.sales_quotation_sublink');
        if (switcher.value=='0'){
            switcher.value='1';
            sublink_div.classList.add('disnone');
            second_line.classList.remove('disnone');
        }
        else{
            switcher.value='0';
            sublink_div.classList.remove('disnone');
            second_line.classList.add('disnone');
        }
    }
function sales_second_row_hide(elem){
    let conteiner = elem.closest('.sales_quotation_line');
    let switcher = conteiner.querySelector('.sales_quotation_switch');
    let second_line = conteiner.querySelector('.sales_quotation_second_line');
    let sublink_div = conteiner.querySelector('.sales_quotation_sublink');
    switcher.value='0';
    sublink_div.classList.remove('disnone');
    second_line.classList.add('disnone');
}
function sales_add_package_line(elem){
    let conteiner = elem.closest('.window_internal');
    let row = conteiner.querySelector('#package_new_row');
    new_row=row.cloneNode(true);
    new_row.id='';
    let tbody = conteiner.querySelector('#sales_package_table').getElementsByTagName("TBODY")[0];
    tbody.appendChild(new_row);
}
function stnmc_view_add(elem){
    let conteiner = elem.closest('TD');
    let target = conteiner.querySelector('.inp_base_id');
    nmnc_view(target.value);
}
function stnmc_view_add2(elem){
    let conteiner = elem.closest('TD');
    let target = conteiner.querySelector('.selector_nmnc_tosend');
    nmnc_view(target.value);
}
function sales_copy(id,elem){
    if (!confirm("Are you sure?")) return false;
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText==='true'){
                    window_close(elem);
                    show_sales_table();
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', 'scripts/sales_order_copy.php');  
    req.send(formData);  // отослать запрос
}
function sales_convert_to_invoice(id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("sale_id", id);
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
    req.open('POST', 'ajax/invoice_from_sales.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}