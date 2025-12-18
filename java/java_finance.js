document.addEventListener("DOMContentLoaded", show_finance_table(1));
function show_finance_table(page){
    var container = document.getElementById('main_div_menu');
    var our_comp = document.getElementById('our_comp').value;
    var customer = document.getElementById('customer').value;
    var fin_type = document.getElementById('fin_type').value;
    var finance_search = document.getElementById('finance_search').value;
    var formData = new FormData();
    formData.append("page", page);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    formData.append("our_comp", our_comp);
    formData.append("fin_type", fin_type);
    formData.append("customer", customer);
    formData.append("keyword", finance_search);
    
    var req = getXmlHttp();
    req.onreadystatechange = function(){ 
            if (req.readyState == 4) {
		container.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                    container.innerHTML =req.responseText;
		}
            }
	};
    req.open('POST', 'finance_display.php');  
    req.send(formData);  // отослать запрос
    container.innerHTML = '<img src="./img/loading.gif">';
}
function number_control(elem){
    var target=document.getElementById('new_fin_number');
    if (elem.checked)target.disabled=true;
    else target.disabled=false;
}
function pay_in_out_control(elem){
    var pay_in=document.getElementById('new_pay_in');
    var pay_out=document.getElementById('new_pay_out');
    if (elem.selectedIndex===1){
        pay_in.disabled=true;
        pay_out.disabled=false;
    }
    else {
        pay_in.disabled=false;
        pay_out.disabled=true;
    }
}