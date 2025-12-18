document.addEventListener("DOMContentLoaded", show_price_table(1)); 
function show_price_table (page){
    var filter = document.getElementById('price_search').value;
    var cls = document.getElementById('stock_view').value;
    var statusElem = document.getElementById('main_div_menu');
    var formData = new FormData();
    if (filter==='') formData.append("filter", 'All');
    else formData.append("filter", filter);
    formData.append("page", page);
    formData.append("class", cls);
    var req = getXmlHttp()  
		req.onreadystatechange = function() {  
		if (req.readyState == 4) {
			statusElem.innerHTML = req.statusText;
			if(req.status == 200) { 
                            statusElem.innerHTML =req.responseText;
			}
		}
	};
	req.open('POST', 'price_display.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = '<img src="/img/loading.gif">';
        
 }
function go_to(){
    var num=document.getElementById('go_to').value;
    show_invoice_table (num);
}
function fast_search(){
    var tab = document.getElementById('invoice_table');
    var max = tab.rows.length;
    var search = document.getElementById('invoice_search').value;
    for (i=1; i<max; i++){
        var t = tab.rows[i];
                if (t.cells[1].innerHTML.indexOf(search) > -1) {t.style.display = '';} else
            t.style.display = 'none';
            if (search=='') {t.style.display = ''; continue;}
    }
}
function price_filter(obj){
    value=obj.value;
    show_price_table(1, value);
}

