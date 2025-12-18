function show_balance_table(page){
    var container = document.getElementById('main_div_menu');
    var cls = document.getElementById('stock_view').value;
    var manufacturer = document.getElementById('manufacturer').value;
    var search_key = document.getElementById('stock_search').value;
    var formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    if (cls===undefined) formData.append("class", 'All');
    else formData.append("class", cls);
    formData.append("manufacturer", manufacturer);
    formData.append("keyword", search_key);
    
    let req = getXmlHttp();
    req.onreadystatechange = function(){ 
        if (req.readyState == 4) {
            container.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                container.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'stock_balance_display.php');  
    req.send(formData);  // отослать запрос
    container.innerHTML = '<img src="./img/loading.gif">';
}
function stock_nmnc_new_add(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                // если статус 200 (ОК) - выдать ответ пользователю
                if(req.responseText==='true'){
                    alert('Nomenclature added successfully.');
                    window_close(elem);
                    if(window.location.pathname=='/stock_nmnc.php')show_nmnc_table();
                }
                else alert(req.responseText);
            }
        }
    }
    req.open('POST', '/scripts/stock_nmnc_new.php');  
    req.send(formData);  // отослать запрос 
    return false;
}
function nmnc_multiinsert_add_line(){
    var row=document.getElementById('multi_insert_tr');
    var tbody=document.getElementById('multi_insert_tbody');
    var new_row=row.cloneNode(true);
    new_row.id='';
    tbody.appendChild(new_row);
}
function nmnc_multiinsert_copy_line(elem){
    var row=$(elem).closest('TR');
    row.clone(true).insertAfter(row);
}
function nmnc_multiinsert_delete_row(elem){
    var row=$(elem).closest('TR');
    row.remove();
}