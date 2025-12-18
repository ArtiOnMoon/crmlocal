document.addEventListener("DOMContentLoaded", show_expend_table('1'));
function show_expend_table (page, search){
    var statusElem = document.getElementById('expenditure_main');
    var year = document.getElementById('year').value;
    var formData = new FormData();
    if(page==undefined)page=1;
    formData.append("page", page);
    formData.append("year", year);
    var type=document.getElementById('expend_type');
    if (type.value!='All') formData.set("type", type.value);
    if (search!=undefined) formData.set("search", search)
    //if (search.value.length>1) formData.set("search", search.value)
    var req = getXmlHttp()  
	req.onreadystatechange = function() {  
            if (req.readyState == 4) {
		statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) { 
                    statusElem.innerHTML =req.responseText;
                    prepTabs();
		}
            }
	};
	req.open('POST', 'expenditure_display.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = 'Ожидаю ответа сервера...';
 }
function row_link(event){
var table=document.getElementById('expend_table');
var target = event.target;
while (target != table) {
    if (target.tagName == 'TR') {
      // нашли элемент, который нас интересует!
        location.href = 'expenditure_view.php?id='+target.cells[0].innerHTML;
        return;
    }
    target = target.parentNode;
  }
}
function fast_search(val){
   if (val.length>=2) show_expend_table(1, val);
   if (val.length<2) show_expend_table(1);
}