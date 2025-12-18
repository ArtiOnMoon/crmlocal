function show_docs_table(page){
    let statusElem = document.getElementById('main_div_menu') 
    let formData = new FormData();
    let doctype = document.getElementById('doctype').value;
    let search=document.getElementById('doc_search').value;
    let comp=document.getElementById('doc_our_company').value;
    let archived = document.getElementById('doc_archived').checked;
    if (search!='') formData.append("search", search);
    formData.append("page", page);
    formData.append("comp", comp);
    formData.append("doctype", doctype);
    formData.append("archived", archived);
    formData.append("sort_field", keyword);
    formData.append("sort_type", sort);
    let req = getXmlHttp();
    req.onreadystatechange = function(){  
        if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) {
                statusElem.innerHTML =req.responseText;
            }
        }
    };
    req.open('POST', 'doc_control_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
}
function doc_control_view(id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                targ.innerHTML=req.responseText;
                $(".datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', '/doc_control_view.php');  
    req.send(formData);  // отослать запрос 
    targ.style.display='block';
    targ.innerHTML='Please, wait. Data is loaded...';
}
function doc_delete_file (elem,id){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    formData.append("delete_button", 1);
    req.onreadystatechange = function(){  
        if (req.readyState == 4) {
            if(req.status == 200) {
                if(req.responseText=='true'){
                    window_close(elem);
                    doc_control_view(id);                    
                }
                else alert(req.responseText);
            }
        }
    };
    req.open('POST', 'download_doc.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function doc_upload_file (elem,id){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    formData.append("download_button", 1);
    req.onreadystatechange = function(){  
        if (req.readyState == 4) {
            if(req.status == 200) {
                if(req.responseText=='true'){
                    window_close(elem);
                    doc_control_view(id);
                }
                else alert(req.responseText);
            }
        }
    };
    req.open('POST', 'upload_document.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function doc_control_change(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    formData.append("download_button", 1);
    req.onreadystatechange = function(){  
        if (req.readyState == 4) {
            if(req.status == 200) {
                if(req.responseText=='true'){
                    window_close(elem);
                    show_docs_table(1);
                }
                else alert(req.responseText);
            }
        }
    };
    req.open('POST', '/scripts/doc_control_change.php');  
    req.send(formData);  // отослать запрос
    return false;
}
function CheckFile(file){
    // Максимальный размер 3MB
    var maxsize = 1024*1024*3;

    // Для хранения размера загружаемого файла
    var iSize = 0;

    // Если браузер IE
    if($.browser.msie)
    {
        var objFSO = new ActiveXObject("Scripting.FileSystemObject");
        var sPath = $(file)[0].value;
        var objFile = objFSO.getFile(sPath);
        iSize = objFile.size;
    }
    else
    {
        // В других браузерах
        iSize = $(file)[0].files[0].size;
    }

    // Делаем проверку что файл не превышает допустимого размера
    if(maxsize > iSize)
    {
        // Если файл допустимого размера - выставляем флаг
        good_size = true;
    }
    // Для хранения ошибки
    var error = '';
    // Если не прошли валидацию по размеру файла
    if(!good_size)
    {
        alert( 'Invalid file size! Use no more than 3 MB file.');
        return false;
    }
    return true;
}