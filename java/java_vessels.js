function show_vessel_table (page=1){
    var statusElem = document.getElementById('main_div_menu');
    var formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    var search=document.getElementById('vessel_search').value;
    if (search!='')formData.append("search", search);
    var owner=document.getElementById('owner').value;
    if (owner!='All')formData.append("owner", owner);
    var operator=document.getElementById('operator').value;
    if (operator!='All')formData.append("operator", operator);
    var agent=document.getElementById('agent').value;
    if (agent!='All')formData.append("agent", agent);
    var req = getXmlHttp()  
		req.onreadystatechange = function() {  
		if (req.readyState == 4) {
			statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
			if(req.status == 200) { 
				statusElem.innerHTML =req.responseText;
			}
		}
	};
	req.open('POST', 'vessels_display.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = 'Ожидаю ответа сервера...' 
        
 }
function go_to(){
    var num=document.getElementById('go_to').value;
    show_vessel_table (num);
}
function vessel_reset_filters(){
    document.getElementById('agent').selectedIndex=0;
    document.getElementById('owner').selectedIndex=0;
    document.getElementById('operator').selectedIndex=0;
    $("#side_menu input").val("");
    $("#hidden_div input").val("All");
    show_vessel_table(1);
}
function vessel_view_add(elem){
    let targ=elem.closest('.vessel_conteiner').querySelector('.selector_tosend_field');
    vessel_view(targ.value); 
}
function vessel_view(id){
    if (id==1)return;
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		targ.innerHTML=req.responseText;
                targ.style.display='block';
                uploaded_files_show('',id,'vessels',targ)
            }
	}
    };
    req.open('POST', 'vessel_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function vessel_new(){
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
    req.open('POST', '/ajax/vessel_add_new.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function vessel_change(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
		if(req.responseText==='true'){
                    alert('Successfully changed');
                    window_close(elem);
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/vessel_change.php');  
    req.send(formData);  // отослать запрос
    return false;
 }