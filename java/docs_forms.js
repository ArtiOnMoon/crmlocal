//document.addEventListener("DOMContentLoaded", load_documents());

function load_forms_content (){
    var statusElem = document.getElementById('forms');
    var formData = new FormData();
    var keyword= document.getElementById('keyword').value;
    if (keyword.length>=1)formData.append("keyword", keyword);
    var req = getXmlHttp()  
		req.onreadystatechange = function() {  
		if (req.readyState == 4) {
			statusElem.innerHTML = req.statusText;
			if(req.status == 200) { 
                            statusElem.innerHTML =req.responseText;
                            prepTabs();
			}
		}
	};
	req.open('POST', 'doc_forms_display.php');  
	req.send(formData);  // отослать запрос
	statusElem.innerHTML = '<img src="/img/loading.gif">';
 }
function download_docs_file(type, id, file_name){
    document.getElementById('type').value=type;
    document.getElementById('id').value=id;
    document.getElementById('file_name').value=file_name;
    document.getElementById('download_file').submit();
 }
function openTab(elem) {
    $(".tab_button").removeClass("selected_tab")
    elem.classList.add("selected_tab");
    //document.getElementById('docs_header').innerHTML=elem.getAttribute('tab_name');
    //Вызов функции отрисовки
    load_df_content(elem.getAttribute('tab'));
}
function load_df_content(tab){
    alert('SYTD');
    var main_block = document.getElementById('main_block');
    var formData = new FormData();
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) { 
                main_block.innerHTML =req.responseText;
                }
        }
    };
    if (tab=='contracts'){
        req.open('POST', 'df_contracts.php');
    }
    else return;
    req.send(formData);  // отослать запрос
    main_block.innerHTML = '<img src="/img/loading.gif">';
    
}
function tr_to_link(event,type){
    var target = event.target;
    if (target.tagName == 'A') return;
    while (target.tagName != 'TABLE') {
    if (target.tagName == 'TR') {
      // нашли элемент, который нас интересует!
      gotolink(type,target.getAttribute('elem_id'));
      return;
      }
    target = target.parentNode;
    }
};
function gotolink(type,id){
    if (type=='forms')location.href = '/docs_view_form.php?id='+id;
}