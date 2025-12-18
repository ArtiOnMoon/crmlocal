function show_complects_table(page){
    let container = document.getElementById('main_div_menu');
    let compl_cat = document.getElementById('compl_cat').value;
    let manufacturer = document.getElementById('manufacturer').value;
    let complect_search = document.getElementById('complect_search').value;
    let stock_statuses =[];
    let statuses_collection = document.querySelectorAll('.stock_status_selector_check:checked');
    for (let i = 0; i < statuses_collection.length; i++) {
        stock_statuses.push(statuses_collection[i].value);
    }
    let formData = new FormData();
    formData.append("page", page);
    formData.append("compl_cat", compl_cat);
    formData.append("manufacturer", manufacturer);
    formData.append("keyword", complect_search);
    formData.append("stock_statuses", JSON.stringify(stock_statuses));
    let req = getXmlHttp();
    req.onreadystatechange = function(){ 
            if (req.readyState == 4) {
		container.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                    container.innerHTML =req.responseText;
		}
            }
	};
    req.open('POST', 'stock_complects_display.php');  
    req.send(formData);  // отослать запрос
    container.innerHTML = '<img src="./img/loading.gif">';
}
function change_stock_complect(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
		if(req.responseText==='true'){
                    alert('Successfully changed');
                    if(window.location.pathname=='/stock_complects.php')show_complects_table(1);
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/stock_complect_change.php');  
    req.send(formData);  // отослать запрос
    return false;
 }
function complect_new(){
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
            }
	}
    };
    req.open('POST', '/ajax/complect_new_form.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function stock_complect_add(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                // если статус 200 (ОК) - выдать ответ пользователю
                if(req.responseText==='true'){
                    alert('Added successfully.');
                    window_close(elem);
                    if(window.location.pathname=='/stock_complects.php')show_complects_table(1);
                }
                else alert(req.responseText);
            }
        }
    }
    req.open('POST', '/scripts/stock_complect_add.php');  
    req.send(formData);  // отослать запрос 
    return false;
}
function complect_view_add(elem){
    complect_view(elem.closest('.complect_conteiner').querySelector('input').value);
}