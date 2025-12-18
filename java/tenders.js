function tender_new(){
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
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', '/ajax/tender_new.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}