function show_order_table(page){
    var statusElem = document.getElementById('main_div_menu');
    var formData = new FormData();
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("page", page);
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
    req.open('POST', 'order_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="./img/loading.gif">';
}
function new_order_line(){
    var tb=document.getElementById('new_order_table');
    var new_row=document.getElementById('first_tr').cloneNode(true);
    new_row.id='';
    tb.appendChild(new_row);
}
function delete_row(elem){
    elem.parentNode.parentNode.remove();
}