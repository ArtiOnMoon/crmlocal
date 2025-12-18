function task_new(){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
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

function tasks_new_form(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) {
                let result =JSON.parse(req.responseText);
                if (result.result=='true'){
                    window_close(elem);
                    task_view(result.id)
                }
                else alert(result.error);
            };
	};
    };
    req.open('POST', '/scripts/tasks_new.php');  
    req.send(formData);
    return false;
}

function task_view (id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("task_id", id);
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
    req.open('POST', 'task_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}

function tasks_change(elem){
    let formData = new FormData(elem);
    
    // Checklists
    let data = [];
    let container = elem.querySelector('.task_checklist_body');
    let checklist_list = container.querySelectorAll('.task_checklist_container');
    for (let i = 0; i < checklist_list.length; i++) {
        let id = checklist_list[i].getAttribute('data-id');
        let text = checklist_list[i].getAttribute('data-text');
        let order = checklist_list[i].getAttribute('data-order');
        let сheck = checklist_list[i].querySelector('.task_checklist_checkbox').checked;
        data.push([id,order,text,сheck]);
    };
    formData.append("checklists",JSON.stringify(data));
    
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) {
                let response =JSON.parse(req.responseText);
                if (response.result === 'true'){
                    alert('Changed successfully');
//                    window_close(elem);
//                    task_view(response.id);
                }
                else alert(response.error);
            };
	};
    };
    req.open('POST', '/scripts/task_change.php');  
    req.send(formData);
    return false;
}

function task_add_reply(elem){
    let formData = new FormData(elem.closest('FORM'));
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) {
                let response =JSON.parse(req.responseText);
                if (response.result=='true'){
                    window_close(elem);
                    task_view(response.id)
                }
                else alert(response.error);
            };
	};
    };
    req.open('POST', '/scripts/task_add_reply.php');  
    req.send(formData);
}

function task_add_checklist(elem){
    let conteiner = elem.closest('.task_checklist').querySelector('.task_checklist_body');
    let num = conteiner.querySelectorAll('.task_checklist_container').length;
//    console.log(num);
    let text = prompt('Add checklist');
    if (!text) return;
    
    let newDiv = document.createElement('div');
    newDiv.setAttribute('data-order',num++);
    newDiv.setAttribute('data-text',text);
    newDiv.setAttribute('data-id',0);
    newDiv.classList.add('task_checklist_container');
    newDiv.innerHTML = '<div class="task_checklist_switch">\n\
                        <input type="checkbox" class="task_checklist_checkbox"> \n\
                    </div>\n\
                    <div class="task_checklist_text">' + text + '</div>\n\
                    <div class="task_checklist_controls">\n\
                        <div class="task_checklist_button task_checklist_up" onclick="checklist_move_up(this)"></div>\n\
                        <div class="task_checklist_button task_checklist_del" onclick="checklist_delete(this)"></div>\n\
                        <div class="task_checklist_button task_checklist_down" onclick="checklist_move_down(this)"></div>\n\
                    </div>';
    
    conteiner.append(newDiv);
//    newDiv.innerHTML = 
}

function checklist_move_down(elem){
    let row = elem.closest('.task_checklist_container');
    if (row.parentNode.lastElementChild === row) return;
    insertAfter(row, row.nextElementSibling);
    checklist_set_numbers(elem);
}

function checklist_move_up(elem){
    let row = elem.closest('.task_checklist_container');
    if (row.parentNode.firstElementChild === row) return;
    row.parentNode.insertBefore(row, row.previousElementSibling);
    checklist_set_numbers(elem);
}

function checklist_set_numbers(elem){
    let conteiner = elem.closest('.task_checklist').querySelector('.task_checklist_body');
    let checklist_list = conteiner.querySelectorAll('.task_checklist_container');
    for (let i = 0; i < checklist_list.length; i++) {
        checklist_list[i].setAttribute('data-order',i);
    };
}

function checklist_delete(elem){
    let row = elem.closest('.task_checklist_container');
    if(!confirm('Are you sure?')) return;
    let container = row.parentNode;
    container.removeChild(row);
    checklist_set_numbers(container);
}

function load_tasks (){
    let for_me=document.getElementById('for_me').checked;
    let from_me=document.getElementById('from_me').checked;
    let search = document.getElementById('task_search_field').value;
    let statusElem = document.getElementById('main_div_menu');
    let hide_completed = document.getElementById('tasks_hide_completed').checked;
    let formData = new FormData();
    formData.append('for_me',for_me);
    formData.append('from_me',from_me);
    formData.append('search',search);
    formData.append('hide_completed',hide_completed);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if (req.status === 200) { 
                statusElem.innerHTML =req.responseText;
            };
        }
    };
    req.open('POST', '/task_display.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = '<img src="/img/loading.gif">';
}

function task_close(elem){
    load_tasks();
    window_close(elem);
}

function task_signal (id, value){
    let formData = new FormData();
    formData.append('id',id);
    formData.append('value',value);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if (req.status === 200) { 
                let response =JSON.parse(req.responseText);
                if (response.result === 'true'){
                    load_tasks();
                    alert('Changed successfully');
                }
                else alert(response.error);
            };
        }
    };
    req.open('POST', '/scripts/tasks/task_signal.php');  
    req.send(formData);  // отослать запрос
}