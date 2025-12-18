function transfers_new(){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData;
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
    req.open('POST', '/ajax/transfers_new.php');  
    req.send(formData);  // отослать запрос 
    targ.style.display='block';
    targ.innerHTML='Please, wait. Data is loaded...';
}
function transfers_view(id){
   let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData;
    formData.append('transfer_id',id);
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
    req.open('POST', 'transfers_view.php');  
    req.send(formData);  // отослать запрос 
    targ.style.display='block';
    targ.innerHTML='Please, wait. Data is loaded...'; 
}
async function transfers_stock_select(elem,existed_id){
    let targ = elem.closest('.window_div').querySelector('.stock_selector_sub_form');
    let existed = targ.querySelectorAll('input[name="tc_stock_id[]"]');
    let existed_date = targ.querySelectorAll('input[name="tc_delivered_date[]"]');
    let list_of_existed=[];
    let list_of_existed_date=[];
    for (let i = 0; i < existed.length; ++i) {
        list_of_existed.push(existed[i].value);
        list_of_existed_date.push(existed_date[i].value);
    }
    let result = await stock_selector_main_show();
    let formData = new FormData;
    if(typeof(existed_id) !== "undefined" && existed_id !== null)formData.append('existed_id',existed_id);
    formData.append('list',JSON.stringify(result));
    formData.append('list_of_existed',JSON.stringify(list_of_existed));
    formData.append('list_of_existed_date',JSON.stringify(list_of_existed_date));
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
    req.open('POST', '/scripts/transfers_selected_list.php');  
    req.send(formData);  // отослать запрос 
    targ.innerHTML='Please, wait. Data is loaded...';
}
function transfers_new_form_submit_outer(elem){
    let form=elem.closest('FORM');
    if (form.reportValidity()) {
        return transfers_new_form_submit(form);
    }
}
function transfers_new_form_submit(elem){
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
    req.open('POST', 'scripts/transfers_new.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function show_transfers_table(page){
    let statusElem = document.getElementById('main_div_menu');
    let req = getXmlHttp();
    let formData = new FormData();
    let from_stock = document.getElementById('from_stock').value;
    let to_stock = document.getElementById('to_stock').value;
    let ship_date_start = document.getElementById('ship_date_start').value;
    let ship_date_end = document.getElementById('ship_date_end').value;
    let receipt_date_start = document.getElementById('receipt_date_start').value;
    let receipt_date_end = document.getElementById('receipt_date_end').value;
    formData.append("from_stock", from_stock);
    formData.append("to_stock", to_stock);
    formData.append("ship_date_start", ship_date_start);
    formData.append("ship_date_end", ship_date_end);
    formData.append("receipt_date_start", receipt_date_start);
    formData.append("receipt_date_end", receipt_date_end);
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'transfers_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="./img/loading.gif">';
 }