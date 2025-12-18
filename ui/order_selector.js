function order_selector_oninput(targ){
    let container = targ.closest('.order_selector');
    let type = container.querySelector('.order_selector_type');
    let value =  container.querySelector('.order_selector_value');
    let to_send =  container.querySelector('.order_selector_to_send');
    let search =  container.querySelector('.order_selector_search');
    
    to_send.value='';
    search.style.display = 'block';
    order_selector_search(search, type.value, value.value);
    
}
function order_selector_blur(targ){
    let container = targ.closest('.order_selector');
    let value =  container.querySelector('.order_selector_value');
    let to_send =  container.querySelector('.order_selector_to_send');
    let search =  container.querySelector('.order_selector_search');
    search.style.display = 'none';
    search.innerHTML='';
    if (to_send.value === '') value.value = '';
}
function order_selector_search(targ, type, value){
    if (type === '0') targ.innerHTML = '<strong>Please select order type</strong>';
    return;
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
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
    req.open('POST', 'ajax/tasks_new_form.php');  
    req.send();  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}