function show_stock_table (page){
    var statusElem = document.getElementById('main_div_menu');
    var cls = document.getElementById('stock_view').value;
    var stat = document.getElementById('stock_status').value;
    var cond = document.getElementById('cond').value;
    var key = document.getElementById('stock_search').value;
    var supplier = document.getElementById('supplier').value;
    var manufacturer = document.getElementById('manufacturer').value;
    var on_balance = document.getElementById('on_bal_select').value;
    var stock = document.getElementById('select_stock').value;
    var formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    if (cls===undefined) formData.append("class", 'All');
    else formData.append("class", cls);
    if (on_balance!=="All") formData.append("on balance", on_balance);
    if (stat===undefined) formData.append("stat", 'All');
    else formData.append("stat", stat);
    formData.append("cond", cond);
    formData.append("keyword", key);
    formData.append("supplier", supplier);
    formData.append("manufacturer", manufacturer);
    formData.append("stock", stock);
    var req = getXmlHttp()  
	req.onreadystatechange = function() {  
            if (req.readyState == 4) {
		statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                    statusElem.innerHTML =req.responseText;
                    $('#stock_table').floatThead({
                        scrollContainer: function($table){
                        return $table.closest('#table_wrap');
                        }
                    });
		}
            }
	};
	req.open('POST', 'stock_display.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = '<img src="./img/loading.gif">';
 }
function show_stock_new_table (page){
    var statusElem = document.getElementById('main_div_menu');
    var cls = document.getElementById('stock_view').value;
    var stat = document.getElementById('stock_status').value;
    var cond = document.getElementById('cond').value;
    var key = document.getElementById('stock_search').value;
    var supplier = document.getElementById('supplier').value;
    var manufacturer = document.getElementById('manufacturer').value;
    var on_balance = document.getElementById('on_bal_select').value;
    var stock = document.getElementById('select_stock').value;
    var formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    if (cls===undefined) formData.append("class", 'All');
    else formData.append("class", cls);
    if (on_balance!=="All") formData.append("on balance", on_balance);
    if (stat===undefined) formData.append("stat", 'All');
    else formData.append("stat", stat);
    formData.append("cond", cond);
    formData.append("keyword", key);
    formData.append("supplier", supplier);
    formData.append("manufacturer", manufacturer);
    formData.append("stock", stock);
    var req = getXmlHttp()  
	req.onreadystatechange = function() {  
            if (req.readyState == 4) {
		statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                    statusElem.innerHTML =req.responseText;
                    $('#stock_table').floatThead({
                        scrollContainer: function($table){
                        return $table.closest('#table_wrap');
                        }
                    });
		}
            }
	};
	req.open('POST', 'stock_new_display.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = '<img src="./img/loading.gif">';
 }
function stock_edit (id){
    var statusElem = document.getElementById('stock_edit_div');
    document.getElementById('wrap').style.display='block';
    statusElem.style.display='block';
    var formData = new FormData();
    formData.append("id", id);
    var req = getXmlHttp()  
		req.onreadystatechange = function() {  
		if (req.readyState == 4) {
			statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
			if(req.status == 200) { 
				statusElem.innerHTML =req.responseText;
			}
		}
	};
	req.open('POST', 'view_stock_item.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = '<img src="./img/loading.gif">';
 }
function change_stock_item (page){
    var formData = new FormData(document.forms.change_stock_form);
    var req = getXmlHttp()  
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    cancel();
                    show_stock_table(page);
                    }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', 'change_stock.php');  
    req.send(formData);  // отослать запрос
    return false;
 }
function reset_filter(){
    document.getElementById('stock_view').selectedIndex=0;
    document.getElementById('stock_status').selectedIndex=0;
    document.getElementById('cond').selectedIndex=0;
    document.getElementById('stock_search').value='';
    document.getElementById('supplier').selectedIndex=0;
    document.getElementById('manufacturer').selectedIndex=0;
    document.getElementById('select_stock').selectedIndex=0;
    $("#side_menu input").val("");
    $("#hidden_div input").val("All");
    show_stock_table(1);
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
function display_stock(){
   show_stock_table(1);
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
//STOCK MULTI INSERT
function add_insert(elem){
    var row=$(elem).closest('TR');
    var new_row=row.clone(false).insertAfter(row);
    new_row.find('input.datepicker')
        .attr("id", "")
        .removeClass('hasDatepicker')
        .removeData('datepicker')
        .unbind()
        .datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
    var field=$(new_row).get(0).getElementsByTagName("INPUT")[6];
    field.value='';
    field.focus();

}
function enter_catch(event){
  if (event.keyCode === 13) {
      add_insert(event.target);
      event.preventDefault();
  }    
}
function multiple_stock_insert(){
    if (!confirm("Insert to the stock?")) return false;
    var t=document.getElementById('multiinsert_table');
    var length = t.rows.length;
    if (length<=1){ alert('Warning 1.'); return;}
    var data_input=new Array;
    var data_select=new Array;
    var content=new Array;
    for (i=1;i<length;i++){
        input=t.rows[i].getElementsByTagName('input');
        select=t.rows[i].getElementsByTagName('select');
        for(var j=0; j<input.length; j++){
            data_input[j] = input[j].value;
        }
        for(j=0; j<select.length; j++){
            data_select[j] = select[j].value;
        }
        content[i-1]=JSON.stringify(data_input.concat(data_select));
    }
    content=JSON.stringify(content);
    document.getElementById('multiinsert_content').value=content;
}
function check_submit(){
    if (!confirm("Insert to the stock?")) return false;
    return true;
}

//function stock_edit2(){
//    var data=[];
//    $('#stock_table input:checked').each(function(){
//       data.push(this.value);
//    });
//    var field=document.getElementById('stock_edit_test');
//    if (data.length===0){alert('Nothing selected');return;}
//    else field.value=data;
//    document.getElementById('wrap').style.display="block";
//    document.getElementById('stock_edit').style.display="block";
//}

function stock_edit2(){
    let data=[];
    document.querySelectorAll('#stock_table input:checked').forEach(function(elem){
        data.push(elem.value);
    });
//    $('#stock_table input:checked').each(function(){
//       data.push(this.value);
//    });
    let field=document.getElementById('stock_edit_test');
    if (data.length===0){alert('Nothing selected');return;}
    else field.value=data;
//    document.getElementById('wrap').style.display="block";
//    document.getElementById('stock_edit').style.display="block";
}
function stock_edit_form(page){
    var formData = new FormData(document.forms.stock_edit_form);
    var req = getXmlHttp()  
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
		if(req.responseText=='true'){
                    cancel();
                    show_stock_table(page);
                    }
                else alert('Error. ' + req.responseText);
            }
	}
    };
    req.open('POST', 'stock_edit.php');  
    req.send(formData);  // отослать запрос
    return false;
}
//STOCK AUTO REFRESH
var timer=600000;
var user_last_activity=Date.now();
function reset_timer(){
    user_last_activity=Date.now();
};
document.onmousemove=reset_timer;
document.onclick=reset_timer;
document.onscroll=reset_timer;
setTimeout(function run() {
    if (document.getElementById('auto_refresh').checked) {
        if ((Date.now()-user_last_activity)>timer)show_stock_table();
        setTimeout(run, timer);
    }
    else setTimeout(run, timer);
}, timer);
