function selector_search(elem,dst,condition){
    var search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    var inputs=elem.parentNode.getElementsByTagName('INPUT');
    inputs[0].value='';
    inputs[1].setAttribute('title','');
    if (elem.value===''){
        search_block.style.display='none';
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
    var search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    search_block.innerHTML='';
    search_block.style.display='none';
    var input_elements=elem.parentNode.getElementsByTagName('INPUT');
    if (input_elements[0].value==='')input_elements[1].value='';
}
function selector_show(elem,dst,condition){
    event.preventDefault();
    var search_block=elem.parentNode.getElementsByTagName('DIV')[0];
    if (search_block.style.display!="none" && search_block.style.display!=""){
        search_block.style.display='none'
        search_block.innerHTML='';
        return;
    }
    var selector_field=elem.parentNode.getElementsByTagName('INPUT')[1];
    selector_field.focus();
    search_block.innerHTML='<img src="./img/loading.gif">';
    search_block.style.display='block';
    var formDataSearch = new FormData();
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
function selector_select_item(event){
    elem=event.target;
    if (elem.tagName!=='TD')return;
    while (elem.className!=='selector'){
        elem=elem.parentNode;
        if (elem===document){
            alert('Eror.');
            return;
        }
    }
    var input_elements=elem.getElementsByTagName('INPUT');
    input_elements[1].value=event.target.innerHTML;
    input_elements[1].setAttribute('title',event.target.innerHTML);
    input_elements[0].value=event.target.getAttribute('data-id');
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
    var elem=event.target;
    var td_elem=event.target;
    while (td_elem.tagName!=='TD'){
        td_elem=td_elem.parentNode;
        if (td_elem===document){
            return;
        }
    }
    while (elem.className!=='selector'){
        elem=elem.parentNode;
        if (elem===document){
            return;
        }
    }
    let note_text=elem.getElementsByTagName('TEXT')[0];
    var input_elements=elem.getElementsByTagName('INPUT');
    input_elements[1].value=td_elem.innerHTML;
    input_elements[1].setAttribute('title',event.target.innerHTML);
    input_elements[0].value=td_elem.getAttribute('data-id');
    note_text.innerHTML=td_elem.innerHTML;
}
function total_calc(){
    var tbody=document.getElementById('entries_table_body');
    var total=0;
    for (var i=1; i< tbody.rows.length; i++){
        let row = tbody.rows[i];
        let qty = row.querySelector('[name="entry_qty[]"]').value;
        let price = row.querySelector('[name="entry_price[]"]').value;
        let amount=qty*price;
        row.querySelector('.amount_input').value=amount.toFixed(2);
        total+=amount;
    }
    document.getElementById('total').value=total.toFixed(2);
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

//NMNC_LONG_SELECTOR
function nmnc_long_input(elem){
    let container = elem.parentNode;
    let select = container.lastChild;
    $(select).show();
    alert (select.className);   
}