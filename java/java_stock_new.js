function show_stock_new_table (page){
    let statusElem = document.getElementById('main_div_menu');
    let stock_statuses =[];
    let statuses_collection = document.querySelectorAll('.stock_status_selector_check:checked');
    for (let i = 0; i < statuses_collection.length; i++) {
        stock_statuses.push(statuses_collection[i].value);
    }
    let search=document.getElementById('stock_gobal_search').value;
    let cls = document.getElementById('stock_view');
    if(typeof(cls) !== "undefined" && cls !== null) cls = cls.value; else cls='All';
    let stat = document.getElementById('stock_status');
    if(typeof(stat) !== "undefined" && stat !== null) stat = stat.value; else stat='All';
    let cond = document.getElementById('cond');
    if(typeof(cond) !== "undefined" && cond !== null) cond = cond.value; else cond='0';
    let manufacturer = document.getElementById('manufacturer');
    if(typeof(manufacturer) !== "undefined" && manufacturer !== null) manufacturer = manufacturer.value; else manufacturer='All';
    let stock = document.getElementById('select_stock');
    if(typeof(stock) !== "undefined" && stock !== null) stock = stock.value; else stock='All';
    let our_company=document.getElementById('our_company');
    if(typeof(our_company) !== "undefined" && our_company !== null) our_company = our_company.value; else our_company='All';
    let pn_or_type=document.getElementById('pn_or_type');
    if(typeof(pn_or_type) !== "undefined" && pn_or_type !== null) pn_or_type = pn_or_type.value; else pn_or_type='';
    let serial=document.getElementById('serial');
    if(typeof(serial) !== "undefined" && serial !== null) serial = serial.value; else serial='';
    let description=document.getElementById('description');
    if(typeof(description) !== "undefined" && description !== null) description = description.value; else description='';
    let type_model=document.getElementById('type_model');
    if(typeof(type_model) !== "undefined" && type_model !== null) type_model = type_model.value; else type_model='';
    let stock_id=document.getElementById('stock_id');
    if(typeof(stock_id) !== "undefined" && stock_id !== null) stock_id = stock_id.value; else stock_id='';
    let place=document.getElementById('place');
    if(typeof(place) !== "undefined" && place !== null) place = place.value; else place='';
    let note=document.getElementById('note');
    if(typeof(note) !== "undefined" && note !== null) note = note.value; else note='';
    let po=document.getElementById('po');
    if(typeof(po) !== "undefined" && po !== null) po = po.value; else po='';
    let so=document.getElementById('so');
    if(typeof(so) !== "undefined" && so !== null) so = so.value; else so='';
    let ccd=document.getElementById('ccd');
    if(typeof(ccd) !== "undefined" && ccd !== null) ccd = ccd.value; else ccd='';
    //DATE receipt
    let rdate_start=document.getElementById('rdate_start');
    if(typeof(rdate_start) !== "undefined" && rdate_start !== null) rdate_start = rdate_start.value; else rdate_start='';
    let rdate_end=document.getElementById('rdate_end');
    if(typeof(rdate_end) !== "undefined" && rdate_end !== null) rdate_end = rdate_end.value; else rdate_end='';
    //DATE of sale
    let sdate_start=document.getElementById('sdate_start');
    if(typeof(sdate_start) !== "undefined" && sdate_start !== null) sdate_start = sdate_start.value; else sdate_start='';
    let sdate_end=document.getElementById('sdate_end');
    if(typeof(sdate_end) !== "undefined" && sdate_end !== null) sdate_end = sdate_end.value; else sdate_end='';
    let formData = new FormData();
    formData.append("stock_statuses", JSON.stringify(stock_statuses));
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("class", cls);
    formData.append("stat", stat);
    formData.append("cond", cond);
    formData.append("manufacturer", manufacturer);
    formData.append("stock", stock);
    formData.append("our_company", our_company);
    formData.append("pn_or_type", pn_or_type);
    formData.append("serial", serial);
    formData.append("description", description);
    formData.append("type_model", type_model);
    formData.append("stock_id", stock_id);
    formData.append("place", place);
    formData.append("note", note);
    formData.append("po", po);
    formData.append("so", so);
    formData.append("ccd", ccd);
    formData.append("search", search);
    formData.append("rdate_start", rdate_start);
    formData.append("rdate_end", rdate_end);
    formData.append("sdate_start", sdate_start);
    formData.append("sdate_end", sdate_end);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'stock_new_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="./img/loading.gif">';
 }
function reset_filter(){
    document.getElementById('stock_id').value='';
    //document.getElementById('our_company').selectedIndex=0;
    //document.getElementById('stock_status').selectedIndex=0;
    document.getElementById('stock_view').selectedIndex=0;
    document.getElementById('manufacturer').selectedIndex=0;
    document.getElementById('pn_or_type').value='';
    document.getElementById('type_model').value='';
    document.getElementById('description').value='';
    document.getElementById('serial').value='';
    document.getElementById('select_stock').selectedIndex=0;
    document.getElementById('place').value='';
    document.getElementById('cond').selectedIndex=0;
    document.getElementById('po').value='';
    document.getElementById('so').value='';
    document.getElementById('stock_gobal_search').value='';
    //document.getElementById('note').value='';
    //document.getElementById('ccd').value='';
    document.getElementById('rdate_start').value='';
    document.getElementById('rdate_end').value='';
    document.getElementById('sdate_start').value='';
    document.getElementById('sdate_end').value='';
    $(".stock_status_selector_check").prop("checked",false);
    show_stock_new_table(1);
}
function stock_new(){
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
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', '/ajax/stock_new_form.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function stock_edit (id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                targ.innerHTML =req.responseText;
                targ.style.display='block';
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', 'stock_view.php');  
    req.send(formData);  // отослать запрос
 }
function complect_view(elem){
    let conteiner = elem.closest('.complect_conteiner');
    let targ = conteiner.querySelector('.complect_field').value;
    stock_edit(targ);
}
function change_stock_item (page,elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    alert('Successfully saved.');
                    if(window.location.pathname=='/stock_new.php')show_stock_new_table(page);                    
                    if(window.location.pathname=='/stock_complects.php')show_complects_table(1);
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', './scripts/stock_change.php');  
    req.send(formData);  // отослать запрос
    return false;
 }
function stock_cancel(){
    document.getElementById('new_stock_item').style.display="none";
    document.getElementById('stock_full_search').style.display="none";
    document.getElementById('wrap').style.display="none";
};
function rebuild_stock_table(){
   var sel = document.getElementById('stock_view').value;
   var stat = document.getElementById('stock_status').value;
   show_stock_table (1, stat, sel);
}
function add_new_stock_item(){
    var statusElem = document.getElementById('new_stock_status') 
    var formData = new FormData(document.forms.new_stock_item_form);
    var req = getXmlHttp()  
		req.onreadystatechange = function() {  
		if (req.readyState == 4) {
			statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
			if(req.status == 200) { 
                 // если статус 200 (ОК) - выдать ответ пользователю
				statusElem.innerHTML =req.responseText;
                                if (req.responseText === '<font color="green">Success</font>')
                                { location.reload(); }
                        }
		}
	};
	req.open('POST', 'add_stock_item.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = 'Ожидаю ответа сервера...' 
}
function stock_search_enter(event){
    if (event.keyCode === 13) {
      show_stock_table();
      event.preventDefault();
    }
}
function go_to(){
    var num=document.getElementById('go_to').value;
    show_stock_table (num);
}
function delete_row(elem){
 while (elem.tagName != 'TABLE') {
    if (elem.tagName == 'TR') {
      if (elem.rowIndex==1)return; else elem.parentNode.removeChild(elem);
    }
    elem = elem.parentNode;
  }
 return;
}
function delete_multi_line(elem){
    let targ=elem.closest('.multiinsert_line');
    if (targ.parentNode.firstElementChild===targ) return;
    targ.parentNode.removeChild(targ);
}
function complect_disable_control(elem,field_id){
    var field=document.getElementById(field_id);
    if(elem.value==='2')field.disabled=false;
    else field.disabled=true;
}
function stock_complect_search(elem, field_id){
    var div=document.getElementById(field_id);
    elem.onfocus=function(){
        div.style.display = 'none'; 
        div.innerHTML = '';
    }
    if (elem.value.length>=2){
        div.innerHTML = 'Ожидаю ответа сервера...';
        div.style.display = 'block';
        var req = getXmlHttp();
            req.onreadystatechange = function() {  
            if (req.readyState == 4) {
		div.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                	div.innerHTML =req.responseText;
                    }
		}
            }
        var formDataSearch = new FormData();
        formDataSearch.append("value", elem.value);
        formDataSearch.append("field", elem.id);
        formDataSearch.append("div_id", field_id);
        req.open('POST', 'stock_complect_search.php');  
	req.send(formDataSearch);  // отослать запрос
       }
    else {
       div.style.display = 'none';
       div.innerHTML = '';
    }
}
function data_selected (val, field_id,div_id){
    var div=document.getElementById(div_id);
    document.getElementById(field_id).value=val;
    div.style.display = 'none';
    div.innerHTML = '';
}
function selector_nmnc_long(elem){
    let container = elem.parentNode;
    let category = container.childNodes[0].value;
    let manufacturer = container.childNodes[1].value;
    let selector = container.childNodes[2];
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
        req.open('POST', '/ajax/selector_nmnc_long.php');  
        req.send(formData);
    }
}
function stock_check_all(elem){
    let conteiner = elem.closest('.stock_selector_window');
    let checkboxes = conteiner.getElementsByClassName('stock_status_selector_check');
    for (let i=0;i<checkboxes.length;i++){
        if (checkboxes[i].checked===false){
              $(".stock_status_selector_check").prop("checked",true);
              return;
          }
    }
    $(".stock_status_selector_check").prop("checked",false);
}
function stock_show_add_transfer(elem,id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                targ.innerHTML =req.responseText;
                targ.style.display='block';
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', 'ajax/stock_transfer_new.php');  
    req.send(formData);  // отослать запрос
}
function stock_transfer_submit(elem){
    let form=elem.closest("FORM");
    let formData = new FormData(form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                alert(req.responseText);
            }
	}
    };
    req.open('POST', 'scripts/stock_transfer_add.php');
    req.send(formData);  // отослать запрос
}
function stock_transfer_change(elem){
    let form=elem.closest("FORM");
    let formData = new FormData(form);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                alert(req.responseText);
            }
	}
    };
    req.open('POST', 'scripts/stock_transfer_change.php');
    req.send(formData);  // отослать запрос
}
function stock_transfer_edit(id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                targ.innerHTML =req.responseText;
                targ.style.display='block';
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', 'stock_transfer_view.php');  
    req.send(formData);  // отослать запрос
}
//STOCK MULTI INSERT
function stock_multi_insert(){
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
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', '/ajax/stock_multiinsert.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function add_insert(elem){
    let row=elem.closest('.multiinsert_line');
    let new_row=row.cloneNode(true);
    insertAfter(new_row,row);
    let field=new_row.querySelector('.input_serial');
    field.value='';
    field.focus();
    let datepicker = new_row.querySelector('.datepicker');
    $(datepicker)
        .attr("id", "")
        .removeClass('hasDatepicker')
        .removeData('datepicker')
        .unbind()
        .datepicker();
}
function enter_catch(event){
  if (event.keyCode === 13) {
      add_insert(event.target);
      event.preventDefault();
  }    
}
function multiple_stock_insert(elem){
    if (!confirm("Insert to the stock?")) return false;
    let container = elem.closest('.window_internal');
    let form = container.querySelector('#stock_multi_insert');
    let form_header = container.querySelector('.multiinsert_header_form');
    if(!form_header.reportValidity())return;
    let formData = new FormData(form);
    
    let stock_our_company=container.querySelector('#stock_our_company').value;
    let date_receipt=container.querySelector('#date_receipt').value;
    let stock=container.querySelector('#stock').value;
//    let stock_po_comp=container.querySelector('#stock_po_comp').value;
    let stock_po=container.querySelector('#stock_po').value;
//    let stock_so_comp=container.querySelector('#stock_so_comp').value;
    let stock_so=container.querySelector('#stock_so').value;
    formData.append("stock_our_company", stock_our_company);
    formData.append("date_receipt", date_receipt);
    formData.append("stock", stock);
//    formData.append("stock_po_comp", stock_po_comp);
    formData.append("stock_po", stock_po);
//    formData.append("stock_so_comp", stock_so_comp);
    formData.append("stock_so", stock_so);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                if (req.responseText==='true'){
                    alert('Added successfully');
                    window_close(elem);
                    if(window.location.pathname=='/stock_new.php')show_stock_new_table(page); 
                }
                else (alert(req.responseText));
            }
	}
    };
    req.open('POST', 'scripts/stock_multi_insert.php');  
    req.send(formData);  // отослать запрос
}
function check_submit(){
    if (!confirm("Insert to the stock?")) return false;
    return true;
}
function stock_edit2(){
    let data=[];
    let table=document.getElementById('stock_table');
    let inputs = table.querySelectorAll('.table_checkbox:checked');
    for (let i=0; i<inputs.length;i++){
        data.push(inputs[i].value);
    }
    if (data.length===0){
        alert('Nothing selected');
        return;
    }
    //Подгрузка формы
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
                let field=document.getElementById('stock_edit_test');
                field.value=data;
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', 'ajax/stock_edit_form.php');  
    req.send(formData);  // отослать запрос
    
//    document.getElementById('wrap').style.display="block";
//    document.getElementById('stock_edit').style.display="block";
}
function stock_edit_form(elem,page){
    let formData = new FormData(document.forms.stock_edit_form);
    let req = getXmlHttp()  
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    window_close(elem);
                    if (window.location.pathname.includes('/stock_new.php'))show_stock_new_table(page);
                    if (window.location.pathname.includes('/stock_transfers.php'))show_transfer_table(page);
                    }
                else alert('Error. ' + req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/stock_new_edit2.php');  
    req.send(formData);  // отослать запрос
    return false;
}

function show_transfer_table(page){
    let statusElem = document.getElementById('main_div_menu');
    let req = getXmlHttp();
    let formData = new FormData();
    let from_stock = document.getElementById('from_stock').value;
    let to_stock = document.getElementById('to_stock').value;
    let sold = document.getElementById('stock_transfers_sold').value;
    let ship_date_start = document.getElementById('ship_date_start').value;
    let ship_date_end = document.getElementById('ship_date_end').value;
    let receipt_date_start = document.getElementById('receipt_date_start').value;
    let receipt_date_end = document.getElementById('receipt_date_end').value;
    let search = document.getElementById('stock_transfers_search').value;
    formData.append("from_stock", from_stock);
    formData.append("to_stock", to_stock);
    formData.append("ship_date_start", ship_date_start);
    formData.append("ship_date_end", ship_date_end);
    formData.append("receipt_date_start", receipt_date_start);
    formData.append("receipt_date_end", receipt_date_end);
    formData.append("page", page);
    formData.append("sold", sold);
    formData.append("search", search);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'stock_transfer_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="./img/loading.gif">';
 }