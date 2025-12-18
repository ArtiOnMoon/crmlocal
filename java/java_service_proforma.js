function change_server_window(page){
    var formData = new FormData(document.forms.service_window_form);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
		if(req.responseText==='true'){
                    cancel();
                    show_service_table(page);
                    }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/service_change.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function add_rate() {
    var table=document.getElementById('rates_table');
    var tbody = table.getElementsByTagName("TBODY")[0];
    var rate = document.getElementById('select_service_rate');
    var row = document.createElement("TR");
    
    var td1 = document.createElement("TD");
    var new_rate = rate.cloneNode(true);
    new_rate.removeAttribute('id');
    new_rate.removeAttribute('name');
    new_rate.removeAttribute('onchange');
    new_rate.onchange=rate_calc;
    td1.appendChild(new_rate);
    var inp1 = document.createElement("INPUT"); //Q-ty
    inp1.onchange=calculate;
    inp1.required=true;
    inp1.setAttribute("size","10");
    inp1.setAttribute("maxlength","10");
    var td2 = document.createElement("TD");
    td2.appendChild (inp1);
    var inp2 = document.createElement("INPUT"); //Unit price
    var td3 = document.createElement("TD");
    inp2.required=true;
    inp2.setAttribute("size","10");
    inp2.setAttribute("maxlength","10");
    inp2.onchange=calculate;
    td3.appendChild (inp2);
    var inp3 = document.createElement("INPUT"); //Amount
    inp3.setAttribute("readonly", true);
    inp3.setAttribute("size","10");
    var td4 = document.createElement("TD");
    td4.appendChild (inp3);
    
    var inp4 = document.createElement("BUTTON");
    inp4.innerHTML='Delete';
    inp4.type='button';
    inp4.onclick=delete_row;
    var td5 = document.createElement("TD");
    td5.appendChild (inp4);
      
    row.appendChild(td1); //Select rate
    row.appendChild(td2); //Q-ty
    row.appendChild(td3); //Unit price
    row.appendChild(td4); //Amount
    row.appendChild(td5); //Delete

    tbody.appendChild(row); // вставка в конец таблицы
    changed();
}
function add_spare() {
    var table=document.getElementById('spares_table');
    var tbody = table.getElementsByTagName("TBODY")[0];
    var row = document.createElement("TR");
    var stock=document.getElementById('stock_view');
    var stock_select=stock.cloneNode(true);
    stock_select.removeAttribute('id');
    stock_select.removeAttribute('name');
    stock_select.removeAttribute('onchange');
    var td1 = document.createElement("TD"); // TYPE
    td1.appendChild(stock_select);
    var inp2 = document.createElement("INPUT"); //P/N
    var td2 = document.createElement("TD");
    inp2.style.width='100%';
    inp2.required=true;
    inp2.onkeyup=live_search;
    td2.appendChild (inp2);
    var inp4 = document.createElement("INPUT"); //DESCRIPTION
    inp4.onkeyup=live_search;
    inp4.style.width='100%';
    var td4 = document.createElement("TD");
    td4.appendChild (inp4);
    var inp5 = document.createElement("INPUT"); //Quantity
    inp5.style.width='100%';
    inp5.required=true;
    inp5.onchange=calculate;
    var td5 = document.createElement("TD");
    td5.appendChild (inp5);
    var inp6 = document.createElement("INPUT"); //Unit Price
    inp6.style.width='100%';
    inp6.required=true;
    inp6.onchange=calculate;
    var td6 = document.createElement("TD");
    td6.appendChild (inp6);
    var inp7 = document.createElement("INPUT"); //Amount
    inp7.style.width='100%';
    inp7.setAttribute("readonly", true);
    var td7 = document.createElement("TD");
    td7.appendChild (inp7);
    
    var inp8 = document.createElement("BUTTON");
    inp8.innerHTML='Delete';
    inp8.type='button';
    inp8.onclick=delete_row;
    var td8 = document.createElement("TD");
    td8.appendChild (inp8);
    var div = document.createElement("DIV");
    div.className='search';
    td2.appendChild (div);
     
    row.appendChild(td1); //class
    row.appendChild(td2); //PN
    row.appendChild(td4); //Description
    row.appendChild(td5); //Quantity
    row.appendChild(td6); //Unit Price
    row.appendChild(td7); //Amount
    row.appendChild(td8); //Delete

    tbody.appendChild(row); // вставка в конец таблицы
    changed();
}
function rate_calc(target){
    if (this !== window)target=this;
    while (target.tagName !== 'TR') {
        target = target.parentNode;
    }
    var row=target.getElementsByTagName('td');
    var price= row[0].firstChild.options[row[0].firstChild.selectedIndex].getAttribute('data-price');
    row[1].firstChild.value=1;
    row[2].firstChild.value=price;
    changed();
    calculate();
}
function spare_calc(target){
    if (this !== window)target=this;
    while (target.tagName !== 'TR') {
        target = target.parentNode;
    }
    var row=target.getElementsByTagName('td');
    var price= row[4].firstChild.value;
    row[4].firstChild.value=price;
    changed();
    calculate();
}
function delete_row(elem){
    if (this !== window)elem=this;
    var tr = elem.parentNode.parentNode;
    table=tr.parentNode.parentNode;
    table.deleteRow(tr.rowIndex);
    changed();
    calculate();
    }
function data_selected(obj){
    var tr=obj.parentNode.parentNode.parentNode.parentNode.parentNode;
    var div=tr.getElementsByTagName('div')[0];
    var input1=tr.getElementsByTagName('input');
    var input2=obj.getElementsByTagName('td');
    input1[0].value=input2[0].innerHTML; // PN
    input1[1].value=input2[1].innerHTML; // Description
    input1[3].value=input2[3].innerHTML; // price
    div.style.display = 'none';
    div.removeChild(div.firstChild);
    }
function live_search(target){
    if (this !== window)target=this;
    var tr=target.parentNode.parentNode;
    var div=tr.getElementsByTagName('div')[0];
    target.onfocus=function(){
        div.style.display = 'none'; 
        div.removeChild(div.firstChild);
    }
    div.innerHTML = 'Ожидаю ответа сервера...';
    div.style.display = 'block';
    var type=tr.getElementsByTagName('select')[0];
    if (target.value.length>=2){
        var req = getXmlHttp()   
            req.onreadystatechange = function() {  
            if (req.readyState == 4) {
		div.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                	div.innerHTML =req.responseText;
                    }
		}
            }
        var formDataSearch = new FormData();
        formDataSearch.append("data", target.value);
        formDataSearch.append("type", type.value);
        formDataSearch.append("index", target.parentNode.cellIndex);
        req.open('POST', '/scripts/invoice_proforma_search.php');  
	req.send(formDataSearch);  // отослать запрос
       }
    else {
       div.style.display = 'none'; 
       div.removeChild(div.firstChild);
    }
}
function calculate(){
    var t_rate=document.getElementById('rates_table');
    var t_spare=document.getElementById('spares_table');
    var rate_total=0;
    var spare_total=0;
    var total=0;
    for (i=1; i<t_rate.rows.length; i++){
        var t = t_rate.rows[i];
        var mid_total=+t.cells[1].firstChild.value*+t.cells[2].firstChild.value;
        t.cells[3].firstChild.value=mid_total;
        rate_total =rate_total + mid_total;
    }
    for (i=1; i<t_spare.rows.length; i++){
        var t = t_spare.rows[i];
        var mid_total=+t.cells[3].firstChild.value*+t.cells[4].firstChild.value;
        t.cells[5].firstChild.value=mid_total;
        spare_total =spare_total + mid_total;
    }
    var total=rate_total+spare_total;
    document.getElementById('total').innerHTML=total;
    document.getElementById('total_to_send').value=total;
    changed();
}
function changed(){
    var elem=document.getElementById('saved');
    elem.innerHTML='';
    document.getElementById('pdf_button').className="button_disabled";
}
function save_invoice(){
    var data=new Array;
    var rates=new Array;
    var spare=new Array;
    var input;
    var select;
    var t_rates = document.getElementById('rates_table');
    var t_spare = document.getElementById('spares_table');
    var tr=t_rates.getElementsByTagName('tr');
    var tr2=t_spare.getElementsByTagName('tr');
    if (tr.length>1){
        for (var i = 1; i < tr.length; i++) { 
            input=tr[i].getElementsByTagName('input');
            select=tr[i].getElementsByTagName('select');
            data[0]=input[0].value;
            data[1]=input[1].value;
            data[2]=select[0].value;
            rates[i-1]=JSON.parse(JSON.stringify(data));
         }
        rates =JSON.stringify(rates);
    }
    else rates='NULL';
    
    if (tr2.length>1){
        for (var i = 1; i < tr2.length; i++) { 
            input=tr2[i].getElementsByTagName('input');
            select=tr2[i].getElementsByTagName('select');
            data[0]=input[0].value;
            data[1]=input[1].value;
            data[2]=input[2].value;
            data[3]=input[3].value;
            data[4]=select[0].value;
            spare[i-1]=JSON.parse(JSON.stringify(data));
        }
        spare =JSON.stringify(spare);
    }
    else spare='NULL';
    document.getElementById('rates').value = rates;
    document.getElementById('spare').value = spare;
}
function save_proforma(){
    var data=new Array;
    var rates=new Array;
    var spare=new Array;
    var input;
    var select;
    var t_rates = document.getElementById('rates_table');
    var t_spare = document.getElementById('spares_table');
    var tr=t_rates.getElementsByTagName('tr');
    var tr2=t_spare.getElementsByTagName('tr');
    if (tr.length>1){
        for (var i = 1; i < tr.length; i++) { 
            input=tr[i].getElementsByTagName('input');
            select=tr[i].getElementsByTagName('select');
            data[0]=input[0].value;
            data[1]=input[1].value;
            data[2]=select[0].value;
            rates[i-1]=JSON.parse(JSON.stringify(data));
         }
        rates =JSON.stringify(rates);
    }
    else rates='NULL';
    
    if (tr2.length>1){
        for (var i = 1; i < tr2.length; i++) { 
            input=tr2[i].getElementsByTagName('input');
            select=tr2[i].getElementsByTagName('select');
            data[0]=input[0].value;
            data[1]=input[1].value;
            data[2]=input[2].value;
            data[3]=input[3].value;
            data[4]=select[0].value;
            spare[i-1]=JSON.parse(JSON.stringify(data));
        }
        spare =JSON.stringify(spare);
    }
    else spare='NULL';
    document.getElementById('rates').value = rates;
    document.getElementById('spare').value = spare;
}
