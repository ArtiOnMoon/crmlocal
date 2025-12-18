function selector_search(elem,dst,condition){
    let chng = new Event('change');
    let search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    let inputs=elem.parentNode.getElementsByTagName('INPUT');
    inputs[0].value='';
    inputs[1].setAttribute('title','');
    if (elem.value===''){
        search_block.style.display='none';
        inputs[0].dispatchEvent(chng);
        return;
    }
    search_block.innerHTML='<img src="./img/loading.gif">';
    search_block.style.display='block';
    var formDataSearch = new FormData();
    formDataSearch.append("data", elem.value);
    formDataSearch.append("condition", condition);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		search_block.innerHTML =req.responseText;
            }
	}
    }
    if (dst==='customers'){
        req.open('POST', 'ajax/selector_customer.php');
        req.send(formDataSearch);  // отослать запрос
    }
    else if (dst==='stock_nmnc'){
        req.open('POST', 'ajax/selector_nmnc.php');
        req.send(formDataSearch);  // отослать запрос
    }
    else if (dst==='vessels'){
        req.open('POST', 'ajax/selector_vessels.php');
        req.send(formDataSearch);  // отослать запрос
    }
}
function selector_blur(elem){
    let chng = new Event('change');
    let search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    search_block.innerHTML='';
    search_block.style.display='none';
    let input_elements=elem.parentNode.getElementsByTagName('INPUT');
    if (input_elements[0].value===''){
        input_elements[1].value='';
        input_elements[0].dispatchEvent(chng);
    }
}
function selector_show(elem,dst,condition){
    event.preventDefault();
    let search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    if (search_block.style.display!="none" && search_block.style.display!=""){
        search_block.style.display='none'
        search_block.innerHTML='';
        return;
    }
    let selector_field=elem.parentNode.getElementsByTagName('INPUT')[1];
    selector_field.focus();
    search_block.innerHTML='<img src="./img/loading.gif">';
    search_block.style.display='block';
    let formDataSearch = new FormData();
    formDataSearch.append("condition", condition);
    formDataSearch.append("data", selector_field.value);
    let req = getXmlHttp(); 
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
                search_block.innerHTML =req.responseText;
            }
	}
    }
    if (dst==='customers'){
        req.open('POST', 'ajax/selector_customer.php');
        req.send(formDataSearch);  // отослать запрос
    }
    else if (dst==='stock_nmnc'){
        req.open('POST', 'ajax/selector_nmnc.php');
        req.send(formDataSearch);  // отослать запрос
    }
    else if (dst==='vessels'){
        req.open('POST', 'ajax/selector_vessels.php');
        req.send(formDataSearch);  // отослать запрос
    }
}
function selector_select_item(event){
    let chng = new Event('change');
    let targ=0;
    let elem=event.target;
    if (elem.classList.contains('selector_results'))return;
    while (elem.className!=='selector'){
        if(elem.classList.contains('selector_result_div')) targ=elem;
        elem=elem.parentNode;
        if (elem===document){
            alert('Eror code 1');
            return;
        }
    }
    if (targ===0){ alert('Error code 2');return;}
    let input_elements=elem.getElementsByTagName('INPUT');
    if (targ.getAttribute('data-id')==='new_customer'){
        new_customer();
        return;
    }
    else if(targ.getAttribute('data-id')==='new_vessel'){
        vessel_new();
        return;
    }
    input_elements[1].value=targ.getAttribute('data-value');
    input_elements[1].setAttribute('title',targ.getAttribute('data-value'));
    input_elements[0].value=targ.getAttribute('data-id');
    input_elements[0].dispatchEvent(chng);
}

//Customers
function selector_cust_select_item(event){
    let chng = new Event('change');
    let targ=0;
    let elem=event.target;
    if (elem.classList.contains('selector_results'))return;
    while (elem.className!=='selector'){
        if(elem.classList.contains('selector_result_div')) targ=elem;
        elem=elem.parentNode;
        if (elem===document){
            alert('Eror code 1');
            return;
        }
    }
    if (targ===0){ alert('Error code 2');return;}
    let input_elements=elem.getElementsByTagName('INPUT');
    if (targ.getAttribute('data-id')==='new_customer'){
        new_customer();
        return;
    }
    input_elements[1].value=targ.getAttribute('data-value');
    input_elements[1].setAttribute('title',targ.getAttribute('data-value'));
    input_elements[0].setAttribute('data-discount',targ.getAttribute('data-discount'));
    input_elements[0].value=targ.getAttribute('data-id');
    input_elements[0].dispatchEvent(chng);
}


//CALC SELECTOR
function calc_selector_search(elem,condition){
    var search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    var inputs=elem.parentNode.getElementsByTagName('INPUT');
    var note_text=elem.parentNode.getElementsByTagName('TEXT')[0];
    //inputs[0].value='';
    inputs[1].setAttribute('title','');
    if (elem.value===''){
        search_block.style.display='none';
        note_text.innerHTML='Nothing selected';
        return;
    }
    search_block.innerHTML='<img src="./img/loading.gif">';
    search_block.style.display='block';
    var formDataSearch = new FormData();
    formDataSearch.append("data", elem.value);
    formDataSearch.append("condition", condition);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		search_block.innerHTML =req.responseText;
            }
	}
    }
    req.open('POST', 'ajax/selector_nmnc.php');
    req.send(formDataSearch);  // отослать запрос
}
function calc_selector_blur(elem){
    var search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    //var note_text=elem.parentNode.getElementsByTagName('TEXT')[0];
    search_block.innerHTML='';
    search_block.style.display='none';
    var input_elements=elem.parentNode.getElementsByTagName('INPUT');
    if (input_elements[0].value==='')input_elements[1].value='';
}
function calc_selector_select_item(event){
    let elem=event.target;
    let targ=event.target;
    while (!targ.classList.contains('selector_result_div')){
        targ=targ.parentNode;
        if (targ===document){
            return;
        }
    }
    while (elem.className!=='selector'){
        elem=elem.parentNode;
        if (elem===document){
            return;
        }
    }
    var input_elements=elem.getElementsByTagName('INPUT');
    input_elements[1].value=targ.getAttribute('data-value');
    input_elements[1].setAttribute('title',targ.getAttribute('data-value'));
    input_elements[0].value=targ.getAttribute('data-id');
}

//NMNC SELECTOR
function nmnc_selector_cat_change(elem){
    var cat=elem.value;
    var selector=elem.parentNode.getElementsByTagName('SELECT')[1];
    var formData = new FormData();
    formData.append("cat", cat);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		selector.innerHTML =req.responseText;
            }
	}
    }
    req.open('POST', 'ajax/nmnc_selector.php');
    req.send(formData); 
}
function nmnc_selector_click(elem){
    var cat=elem.parentNode.getElementsByTagName('SELECT')[0].value;
    var selector=elem.parentNode.getElementsByTagName('SELECT')[1];
    var formData = new FormData();
    formData.append("cat", cat);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		selector.innerHTML =req.responseText;
            }
	}
    }
    req.open('POST', 'ajax/nmnc_selector.php');
    req.send(formData); 
}

//COMBOSELECT
function comboselect_select_change(elem){    
    let input=elem.previousElementSibling;
    if (!input) return;
    input.value=elem[elem.selectedIndex].text;
    input.focus();
}
function comboselect_rates_change(elem){    
    let input=elem.previousElementSibling;
    if (!input) return;
    let price = elem[elem.selectedIndex].getAttribute('data-price');
    input.parentNode.parentNode.parentNode.querySelector('[name="entry_price[]"]').value = price;
    input.value=elem[elem.selectedIndex].text;
    total_calc();
    input.focus();
}
function comboselect_rates_click(elem){
    let tb=elem.closest('TBODY');
    let cat_id=tb.querySelector('.entry_base_id').value;
    for (var i=0; i < elem.options.length; i++){
        if(elem.options[i].getAttribute('data-cat_id')==cat_id)elem.options[i].style.display='initial';
        else elem.options[i].style.display='none';
    }
}

//NMNC_LONG_SELECTOR
function nmnc_long_input(elem){
    let container = elem.parentNode;
    let select = container.lastChild;
    $(select).show();
    alert (select.className);   
}

//SELECTOR_NMNC_LENEAR
function selector_nmnc_blur(elem){
    let delay300=delay(selector_nmnc_blur_inner,300)
    delay300(elem);
}
function selector_nmnc_blur_inner(elem){
    let tr=elem.closest('.selector_nmnc');
    let div=tr.querySelector('.selector_search_div');
    div.innerHTML="";
    div.style.display="none";
}
function selector_nmnc_search(elem){
    let tr=elem.closest('TR');
    let div=tr.querySelector('.selector_search_div');
    div.innerHTML = 'Please wait...';
    div.style.display = 'block';
    div.style.width = elem.clientWidth+'px';
    //очистка при путом вводе
    if (elem.value==""){
        elem.classList.remove('selector_has_nmnc');
        elem.parentNode.querySelector('.selector_nmnc_tosend').value='';
    }
    //
    if (elem.value.length>=2){
        let req = getXmlHttp();
        req.onreadystatechange = function() {  
            if (req.readyState === 4){
		div.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status === 200){ 
                    div.innerHTML =req.responseText;
                }
            }
        };
        let formDataSearch = new FormData();
        formDataSearch.append("data", elem.value);
        req.open('POST', 'ajax/selector_nmnc_search.php');  
	req.send(formDataSearch);  // отослать запрос
       }
    else {
       div.style.display = 'none'; 
       div.removeChild(div.firstChild);
    }
}
function selector_nmnc_selected(event){
    let targ = event.target.closest('DIV');
    let t_id=targ.getAttribute('data-id');
    let t_val=targ.getAttribute('data-value');
    targ.parentNode.parentNode.parentNode.querySelector('.selector_nmnc_tosend').value=t_id;
    targ.parentNode.parentNode.parentNode.querySelector('.selector_nmnc_search_field').value=t_val;
    
}
function selector_nmnc_qte_selected(event){
    let targ = event.target.closest('DIV');
    let conteiner = targ.closest('TR');
    let search_field = conteiner.querySelector('.selector_nmnc_search_field');
    search_field.classList.add('selector_has_nmnc');
    let field = conteiner.querySelector('.selector_nmnc_tosend');
    field.value=targ.getAttribute('data-id');
    field.setAttribute("data-curr",targ.getAttribute('data-curr'));
    conteiner.querySelector('.inp_price').value=targ.getAttribute('data-price');
    conteiner.querySelector('.inp_discount').value=targ.getAttribute('data-discount');
    conteiner.querySelector('.selector_nmnc_search_field').value=targ.getAttribute('data-value');
    qte_total(conteiner);
}

//STOCK_SELECTOR
function stock_selector_main_show(){
    let stock_selected_item = new Promise(function(resolve, reject) {
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
                    let conf_button=targ.querySelector('.selector_cstock_confirm_button');                
                    conf_button.onclick = function() {
                        let list = targ.querySelectorAll('.table_checkbox:checked');
                        if(list.length==0){
                            window_close(conf_button);
                            reject('Nothing selected!');
                        }
                        let result=[];
                        for (let i = 0; i < list.length; ++i) {
                            result.push(list[i].value);
                        }
                        resolve(result);
                        window_close(targ);
                    }               
                }
            }
        };
        req.open('POST', '/ajax/stock_selector_main_window.php');  
        req.send(formData);  // отослать запрос 
        targ.style.display='block';
        targ.innerHTML='Please, wait. Data is loaded...';
    //
    });
    return stock_selected_item;
}
function stock_selector_subwindow_load(elem){
    let container=elem.closest('.window_internal');
    let targ=container.querySelector('.stock_selector_sub_form');
    let formData = new FormData;
    let stock_class=container.querySelector('.stock_selector_class').value;
    let stock_maker=container.querySelector('.stock_selector_maker').value;
    let stock_po=container.querySelector('.stock_selector_po').value;
    let stock_pn=container.querySelector('.stock_selector_pn').value;
    formData.append('stock_class',stock_class);
    formData.append('stock_maker',stock_maker);
    formData.append('stock_po',stock_po);
    formData.append('stock_pn',stock_pn);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                targ.innerHTML=req.responseText;
            }
	}
    };
    req.open('POST', '/ajax/stock_selector_sub_window.php');  
    req.send(formData);  // отослать запрос 
    targ.innerHTML='Please, wait. Data is loaded...';
    
}
function stock_selector_subwindow_action(elem){
    //прописывается в callback-функции
}