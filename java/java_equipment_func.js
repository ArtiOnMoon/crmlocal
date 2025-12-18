function delete_row(elem){
    if (this !== window)elem=this;
    var tr = elem.parentNode.parentNode;
    var table=tr.parentNode.parentNode;
    table.deleteRow(tr.rowIndex);
    }
function equipment_new_line(){
    let t=document.getElementById('equipment_content');
    let line=document.getElementById('t_row');
    let new_line = line.cloneNode(true);
    t.appendChild(new_line);
    $(new_line).find('input.datepicker')
        .attr("id", "")
        .removeClass('hasDatepicker')
        .removeData('datepicker')
        .unbind()
        .datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
}
function add_new_equipment(vessel_id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("vessel_id",vessel_id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                targ.innerHTML = req.responseText;
                targ.style.display='block';
                $(".datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', '/ajax/vessel_equipment_new.php');  
    req.send(formData);  // отослать запрос
}
function vessel_equipment_send_form(targ){
    if (!confirm("Insert to the Equipment database?")) return false;
    let t=targ.querySelector('#equipment_content');
    let length = t.rows.length;
    if (length < 1){ alert('At least 1 equipment required.'); return false;}
    let formData = new FormData(targ);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                if(req.responseText==='true'){
                    alert ('Added successfully.');
                    window_close(targ);
                }
                else {alert(req.responseText);}
            }
	}
    };
    req.open('POST', '/scripts/equipment_new.php');
    req.send(formData);
    return false;
}
function show_equipment_table(page){
    let statusElem = document.getElementById('main_div_menu') 
    let formData = new FormData();
    formData.append("page", page);
    let keyword= document.getElementById('keyword').value;
    if (keyword!='')formData.append("keyword", keyword);
    let category= document.getElementById('category').value;
    if (category!='All')formData.append("category", category);
    let manuf_id= document.getElementById('manufacturer').value;
    if (manuf_id!='All')formData.append("manuf_id", manuf_id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
                statusElem.innerHTML =req.responseText;
            }
	}
    };
    req.open('POST', 'equipment_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
 }
function reset_filters(){
    document.getElementById('category').selectedIndex=0;
    document.getElementById('manufacturer').selectedIndex=0;
    document.getElementById('keyword').value="";
    show_equipment_table(1);
}
function add_equipment(key,value){
    document.getElementById('wrap').style.display='block';
    document.getElementById('new_equipment').style.display='block';
    document.getElementById('equipment_value').value=value;
    document.getElementById('equipment_key').value=key;
}
function vessel_equipment_view(id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    targ.style.display='block';
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) { 
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
    req.open('POST', 'equipment_view.php');  
    req.send(formData);  // отослать запрос 
}
function vessel_equipment_change(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) { 
                if(req.responseText=='true'){
                    alert('Successfully changed.');
                    if (window.location.pathname=='/equipment.php')show_equipment_table(1);
                    else if (window.location.pathname=='/vessels.php')show_vessel_table(1);   
                }
		else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/equipment_change.php');  
    req.send(formData);  // отослать запрос 
    return false;
}