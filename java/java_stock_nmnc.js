function show_nmnc_table(page){
    let container = document.getElementById('main_div_menu');
    let cls = document.getElementById('stock_view').value;
    let manufacturer = document.getElementById('manufacturer').value;
    let search_key = document.getElementById('stock_search').value;
    let nmnc_stock_for_sort = document.getElementById('nmnc_stock_for_sort').value;
    let nmnc_hide_0 = document.getElementById('nmnc_hide_0').checked;
    let formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    if (cls===undefined) formData.append("class", 'All');
    else formData.append("class", cls);
    formData.append("manufacturer", manufacturer);
    formData.append("keyword", search_key);
    formData.append("nmnc_stock_for_sort", nmnc_stock_for_sort);
    formData.append("nmnc_hide_0", nmnc_hide_0);
    
    let req = getXmlHttp();
    req.onreadystatechange = function(){
        if (req.readyState == 4) {
            container.innerHTML = req.statusText;// показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                container.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'stock_nmnc_display.php');  
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
function stock_nmnc_change(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                // если статус 200 (ОК) - выдать ответ пользователю
                let result = JSON.parse(req.responseText);
                if(result.result===true){
                    alert('Nomenclature changed successfully.');
                    window_close(elem);
                    if(window.location.pathname==='/stock_nmnc.php')show_nmnc_table();
                }
                else alert(result.error);
            }
        }
    }
    req.open('POST', '/scripts/stock_nmnc_change.php');  
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