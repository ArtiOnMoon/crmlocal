document.addEventListener("DOMContentLoaded", show_rates_table());
function show_rates_table (){
    let statusElem = document.getElementById('main_div_menu') 
    let formData = new FormData();
    let currency=document.getElementById('select_currency').value;
    let search=document.getElementById('rates_search').value;
    let rate_our_comp=document.getElementById('rate_our_comp').value;
    if (currency!=='') formData.append("currency", currency);
    if (search!=='')formData.append("search", search);
    if (rate_our_comp!=='')formData.append("rate_our_comp", rate_our_comp);
    var req = getXmlHttp()  
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) { 
		statusElem.innerHTML =req.responseText;
            }
	}
    };
    req.open('POST', 'service_rates_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
 }