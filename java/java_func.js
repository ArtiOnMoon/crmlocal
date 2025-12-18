function display(elem){
    document.getElementById(elem).style.display="block";
    document.getElementById('wrap').style.display="block";
};
function cancel(){
    document.getElementById('wrap').style.display="none";
    $(".hidden").css("display", "none");
    window_div=document.getElementById('window');
    if (window_div !== null) window_div.innerHTML='';
};
function window_close(elem){
    let targ = elem.closest('.window_div');
    document.body.removeChild(targ);
}
function new_customer(){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                targ.innerHTML=req.responseText;
            }
	}
    };
    req.open('POST', '/ajax/customers_add_new_form.php');  
    req.send();  // отослать запрос 
    targ.style.display='block';
    targ.innerHTML='Please, wait. Data is loaded...';
}
function service_new(){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let comp=document.getElementById('service_our_company').value;
    var formData = new FormData;
    formData.append("service_our_comp",comp);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) {
                targ.innerHTML=req.responseText;
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });
            }
	}
    };
    req.open('POST', '/ajax/service_new_form.php');  
    req.send(formData);  // отослать запрос 
    targ.style.display='block';
    targ.innerHTML='Please, wait. Data is loaded...';    
}
function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
function getXmlHttp(){
  let xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}
function insertAfter(elem, refElem) {
  return refElem.parentNode.insertBefore(elem, refElem.nextSibling);
}
function select_control (elem){
    for (i=0;i<elem.options.length;i++){
        if (i!==elem.selectedIndex)elem.options[i].removeAttribute("selected",""); else elem.options[i].setAttribute("selected","");
    }
};
function display_menu(div_id, sign){
    var elem = document.getElementById(div_id);
    var sign=document.getElementById(sign);
    var inv_wrap=document.getElementById('invis_wrap');
    if (elem.style.display=='none' || elem.style.display==''){
        inv_wrap.style.display='block';
        elem.style.display='block';
        sign.innerHTML='&#9650';
    }
    else{
        elem.style.display='none';
        sign.innerHTML='&#9660';
        inv_wrap.style.display='none';
    }
}
function close_menu(){
    $(".hidden_div").hide();
    $("#invis_wrap").hide();
    $(".sign").html('&#9660');
}
function activate(elem){
    inp=document.getElementById(elem.value);
    if (elem.checked)inp.disabled=false; else inp.disabled=true;
}
function activate_td(elem){
    let conteiner=elem.closest('tr').querySelector('.td_for_disable');
    if (elem.checked)conteiner.classList.remove('disabledbutton'); else conteiner.classList.add('disabledbutton');
}
function disabled_control(elem1,elem2){
    targ=document.getElementById(elem2);
    if (elem1.checked)targ.classList.remove("disabledbutton"); else targ.classList.add("disabledbutton");
}
function display_control(elem1,elem2){
    targ=document.getElementById(elem2);
    if (elem1.checked)targ.classList.remove("disnone"); else targ.classList.add("disnone");
}
function check_all_checkboxes(elem){
    if (elem.checked)$('.table_checkbox').prop("checked",true);
    else $('.table_checkbox').prop("checked",false);
}
function check_delete(text){
    console.log(text);
    if(text===undefined)text='Delete this record?';
    if (confirm(text))
        return true;
    else 
        return false;
}
function check_copy(id){
    if (confirm("Are you sure?")){
        document.location.href="/scripts/service_order_copy.php?id=" + id;
    } else return false;
}
function uploaded_files_show(number,elem){
    let targ=elem.closest('.window_div');
    let files_div=targ.querySelector('#uploaded_files');
    let formData = new FormData();
//    formData.append("comp_id", comp_id);
    formData.append("number", number);
//    formData.append("type", type);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            files_div.innerHTML = req.statusText; // показать статус (Not Found, ОК..)
            if(req.status === 200) { 
		files_div.innerHTML =req.responseText;
                let files_form=targ.querySelector('#files_upload_form');
                files_form.onsubmit=files_upload;
            }
	}
    };
    req.open('POST', '/ajax/files_form_ajax.php');  
    req.send(formData);  // отослать запрос 
}
function files_upload(){
    let targ=this.closest('.window_div');
    let comp_id=this.querySelector('#files_upload_form_comp_id').value;
    let number=this.querySelector('#files_upload_form_number').value;
    let type=this.querySelector('#files_upload_form_type').value;
    let formData = new FormData(this);
    let req = getXmlHttp(); 
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) {
                if (req.responseText==='true'){
                    uploaded_files_show(number,targ);                     
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/files_upload.php');  
    req.send(formData);  // отослать запрос 
    return false;
}
function add_new_stock_item_ajax(elem){
    var stock_wind=document.getElementById('new_item');
    var formData = new FormData(elem);
    var req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState === 4) {
            if(req.status === 200) { 
                if (req.responseText==='true'){
                    stock_wind.style.display='none';
                    alert('Successfully added.');
                }
                else alert(req.responseText);
            };
	};
    };
    req.open('POST', '/scripts/stock_nmnc_new.php');  
    req.send(formData);
    return false;
}
function table_row_up(elem){
    let row = elem.closest('TR');
    if (row.rowIndex===1)return;
    row.parentNode.insertBefore(row, row.previousElementSibling);
}
function table_row_down(elem){
    let row = elem.closest('TR');
    if (row.rowIndex===row.parentNode.rows.length) return;
    insertAfter(row, row.nextElementSibling);
}
function table_copy_line(elem){
    let row = elem.closest('TR');
    let line = row.cloneNode(true);
    insertAfter(line, row); 
}
function table_delete_row(elem){
    let tr = elem.closest('TR');
    tr.parentNode.parentNode.deleteRow(tr.rowIndex);
}
function customer_view_add(elem){
    let targ=elem.closest('.customer_conteiner').querySelector('.selector_tosend_field');
    cust_edit(targ.value);    
}
function stock_nmnc_new(){
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
            }
	}
    };
    req.open('POST', '/ajax/stock_nmnc_new_form.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function openTab(elem) {
    let tab=elem.getAttribute('tab');
    let container=elem.closest('.window_internal');
    let elements=container.querySelectorAll('.tab_button');
    for(let a of elements){
        a.classList.remove('selected_tab');
    }
    elem.classList.add("selected_tab");
    elements=container.querySelectorAll('.tab');
    for (a of elements){
        a.style.display='none';
    }
    container.querySelector('#'+tab).style.display='block';
}
function switchTab(elem){
    let tab=elem.getAttribute('data-tab');
    let container=elem.closest('.window_internal');
    let elements=container.querySelectorAll('.nd_tab');
    for(let a of elements){
        a.classList.remove('nd_tab_active');
    }
    elem.classList.add("nd_tab_active");
    elements=container.querySelectorAll('.nd_tabdiv');
    for (a of elements){
        a.classList.add('nd_tabdiv_inactive');
    }
    container.querySelector('#'+tab).classList.remove('nd_tabdiv_inactive');
}
function delay(f, ms) {
  return function() {
    setTimeout(() => f.apply(this, arguments), ms);
  };
}
$(".datepicker").datepicker({
      changeMonth: true,
      changeYear: true,
      firstDay:1,
      dateFormat: 'yy-mm-dd'
});
function display_switch(id){
    let target = document.getElementById(id);
    if (target.style.display=='none')target.style.display='block';
    else target.style.display='none'
}
//COMBOBOX
$( function() {
    $.widget( "custom.combobox", {
        options: { 
            /* override default values here */
            minLength: 2,
            /* the argument to pass to ajax to get the complete list */
            ajaxGetAll: {get: "all"}
        },
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .attr( "required", "true")
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            classes: {
              "ui-tooltip": "ui-state-highlight"
            }
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .on( "mousedown", function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .on( "click", function() {
            input.trigger( "focus" );
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        var select_el = this.element.get(0); // get dom element
        var rep = new Array(); // response array
        // simple loop for the options
        for (var i = 0; i < select_el.length; i++) {
        var text = select_el.options[i].text;
        if ( select_el.options[i].value && ( !request.term || matcher.test(text) ) )
            // add element to result array
            rep.push({
                label: text, // no more bold
                value: text,
                option: select_el.options[i]
            });
        }
        // send response
        response( rep );
        },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
 
    $( ".combobox" ).combobox();
    $( ".equipment_combobox" ).combobox(
            {
            select: function (event, ui) { 
            show_equipment_table(1); 
            }}
        );    
  } );
//СОТРИТРОВКА ТАБЛИЦ
function table_sort(event, type){
    let target = event.target; 
    if (target.tagName == 'TH') {
        let key=target.getAttribute('keyword');
        if (key==undefined) return;
        if (key==keyword){
            if (sort=='DESC')sort='';
            else sort='DESC';
        }
        keyword=key;
    } else return;
    switch(type){
        case 'customers':
            show_customers_table(1);
            return;
        case 'stock':
            show_stock_new_table(1);
            return;
        case 'stock_nmnc':
            show_nmnc_table(1);
            return;
        case 'service':
            show_service_table(1);
            return;
        case 'sales':
            show_sales_table(1);
            return;
        case 'vessels':
            show_vessel_table(1);
            return;
        case 'doc_control':
            show_docs_table(1);
            return;
        case 'invoices':
            show_invoice_table(1);
            return;
        case 'purchase':
            show_purchase_table(1);
            return;
    }
};
var sort='';
var keyword='';
var expanded = false;
function showCheckboxes() {
  var checkboxes = document.getElementById("checkboxes");
  if (!expanded) {
    checkboxes.style.display = "block";
    expanded = true;
  } else {
    checkboxes.style.display = "none";
    expanded = false;
  }
}
function java_refresh_func(this_elem, elem_id, type, cat,flag){
    var elem = this_elem.nextSibling;
    var arr=[elem.getAttribute('NAME'),elem.getAttribute('ID'),elem.getAttribute('CLASS')];
    var params=JSON.stringify(arr);
    var statusElem = this_elem.parentNode;
    var formData = new FormData();
    formData.append("elem_id", elem_id);
    formData.append("type", type);
    formData.append("params", params);
    formData.append("cat", cat);
    formData.append("flag", flag);
    var req = getXmlHttp()  
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
            if(req.status == 200) {
                statusElem.innerHTML =req.responseText;
                $( ".combobox" ).combobox();                
            }
	}
    }
    req.open('POST', 'ajax/ajax_select_.php');  
    req.send(formData);  // отослать запрос
    statusElem.innerHTML = 'Please, wait...';
}
function checked_field(elem,target){
    field=document.getElementById(target);
    if (elem.checked===true)field.disabled=true;
    else field.disabled=false;
}
function checked_field_reverse(elem,target){
    field=document.getElementById(target);
    if (elem.checked===true)field.disabled=false;
    else field.disabled=true;
}
function sales_radio_flag(elem){
    let target = document.getElementById(elem.getAttribute("data-target"));
    if (elem.value==='0')target.classList.add('disabledbutton');
    else target.classList.remove('disabledbutton');
}
function download_file(file,elem){
    let targ=elem.closest('.window_div');
    targ.querySelector('#file_name').value=file;
    targ.querySelector('#file_action').value='download';
    targ.querySelector('#blankform').submit();
}
function delete_file(file, elem){
    let targ=elem.closest('.window_div');
    if (!confirm("Delete this file?")) return false;
    let comp_id=targ.querySelector('#files_upload_form_comp_id').value;
    let number=targ.querySelector('#files_upload_form_number').value;
    let type=targ.querySelector('#files_upload_form_type').value;
    targ.querySelector('#file_name').value=file;
    targ.querySelector('#file_action').value='delete';
    let formData = new FormData(targ.querySelector('#blankform'));
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) {
                if (req.responseText==='true'){
                   uploaded_files_show(number,targ);               
                }
                else alert(req.responseText);
            }
	}
    };
    req.open('POST', '/scripts/files_manage.php');  
    req.send(formData);  // отослать запрос 
}
function checkbox_switch(elem, target_id){
    if (elem.checked) document.getElementById(target_id).disabled=false;
    else document.getElementById(target_id).disabled=true;
}
function copy_to_clipboard(elem,text){
    if (text !== ''){
        navigator.clipboard.writeText(text)
        .then(()=>{elem.style.background = 'green';})
        .catch(()=>{elem.style.background = 'red';});
    }
}
//Nomenclature selector
function stock_nmnc_selector(elem){
    for (i=0;i<elem.options.length;i++){
        if (i!==elem.selectedIndex)elem.options[i].removeAttribute("selected",""); else elem.options[i].setAttribute("selected","");
    }
    var category_id=elem.parentNode.firstChild;
    category_id=category_id.options[category_id.selectedIndex].value;
    var nmnc_selector=elem.nextElementSibling;
    var formData = new FormData();
    formData.append("category_id",category_id);
    var req = getXmlHttp();
    req.onreadystatechange = function(){ 
            if (req.readyState == 4) {
		nmnc_selector.innerHTML = req.statusText // показать статус (Not Found, ОК..)
		if(req.status == 200) {
                    nmnc_selector.innerHTML =req.responseText;
                    $(nmnc_selector).combobox();
		}
            }
	};
    req.open('POST', '/ajax/stock_nmnc_selector.php');  
    req.send(formData);  // отослать запрос
}
function get_our_bank_det(elem){
    let conteiner=elem.closest('.bank_details_container');
    let comp_id=conteiner.querySelector('.bank_det_company').value;
    let currency=conteiner.querySelector('.bank_det_currency').value;
    let selector=conteiner.querySelector('.our_bank_det');
    let formData = new FormData();
    formData.append("id", comp_id);
    formData.append("currency", currency);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
		selector.innerHTML=req.responseText;
            }
	}
    };
    req.open('POST', 'ajax/get_our_bank_details.php');  
    req.send(formData);  // отослать запрос
}
function nmnc_view (id){
    if (id===0 || id==='')return;
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
                targ.innerHTML =req.responseText;
                targ.style.display='block';
            }
	}
    };
    req.open('POST', 'stock_nmnc_view.php');  
    req.send(formData);  // отослать запрос
}
function stock_new_add(elem){
    let formData = new FormData(elem);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
        if (req.readyState == 4) {
            if(req.status == 200) {
                // если статус 200 (ОК) - выдать ответ пользователю
                if(req.responseText==='true'){
                    alert('Added successfully.');
                    window_close(elem);
                    if(window.location.pathname=='/stock_new.php')show_stock_new_table();
                }
                else alert(req.responseText);
            }
        }
    }
    req.open('POST', '/scripts/stock_add.php');  
    req.send(formData);  // отослать запрос 
    return false;
}
//cross_docs
//function cross_docs_load(targ,type,comp_id,number){
function cross_docs_load(targ, number){
    let conteiner = targ.closest('.window_div').querySelector('.related_orders_conteiner');
    let formData = new FormData();
//    formData.append("type", type);
//    formData.append("comp_id", comp_id);
    formData.append("number", number);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) { 
		conteiner.innerHTML =req.responseText;
            }
	}
        else conteiner.innerHTML='aaa';
    };
    req.open('POST', '/ajax/cross_docs_display.php');  
    req.send(formData);  // отослать запрос
    conteiner.innerHTML = '<img src="./img/loading.gif">';
}
function view_order(type,comp,number){
    if (type===1) view_service_order(comp,number);
    else if (type===2) sales_view(comp,number);
    else if (type===3) purchase_view(comp,number);
    else if (type===4) invoice_view(comp,number);
}
function view_order_by_id(type,id){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    targ.style.display='block';
    let formData = new FormData();
    formData.append("order_id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) { 
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
    switch(type){
        case 1:req.open('POST', 'service_view.php'); break;
        case 2:req.open('POST', 'sales_view.php'); break;
        case 3:req.open('POST', 'purchase_view.php'); break;
        case 4:req.open('POST', 'invoice_view.php'); break;
    }     
    req.send(formData);  // отослать запрос 
}
function related_orders_add(elem){
    let conteiner=elem.closest('.related_orders_wrapper');
//    let type=conteiner.querySelector('.related_orders_type').value;
//    let comp_id=conteiner.querySelector('.related_orders_comp_id').value;
    let number=conteiner.querySelector('.related_orders_number').value;
//    let type1=conteiner.querySelector('.related_docs_select').value;
//    let comp_id1=conteiner.querySelector('.related_docs_comp_id').value;
    let number1=conteiner.querySelector('.related_orders_number2').value; 
    let formData = new FormData();
//    formData.append("type", type);
//    formData.append("type1", type1);
//    formData.append("comp_id", comp_id);
//    formData.append("comp_id1", comp_id1);
    formData.append("number", number);
    formData.append("number1", number1);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
                alert(req.responseText);
                cross_docs_load(elem, number);
            }
	}
    };
    req.open('POST', '/scripts/related_orders_add.php');  
    req.send(formData);  // отослать запрос
}
function related_docs_delete(elem,id){
    if (!confirm("Delete this link?")) return false;
    let conteiner=elem.closest('.related_orders_wrapper');
    let order=conteiner.querySelector('.related_orders_number').value;
    let formData = new FormData();
    formData.append("id", id);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState == 4) {
            if(req.status == 200) { 
                alert(req.responseText);
                cross_docs_load(elem,order);
            }
	}
    };
    req.open('POST', '/scripts/related_orders_delete.php');  
    req.send(formData);  // отослать запрос
}

function qte_total(elem){
    let conteiner = elem.closest('.window_internal');
    let total = conteiner.querySelector('#total_field');
    let targ=conteiner.querySelectorAll('.quotation_line');
    let total_num=0;
    for (let i=0; i<(targ.length); i++){
        let qty = targ[i].querySelector('.inp_qty').value;
        let price = targ[i].querySelector('.inp_price').value;
        let discount = targ[i].querySelector('.inp_discount').value;
        let amount=qty*price*(1 - discount/100);
        targ[i].querySelector('.inp_amount').value=amount.toFixed(2);
        total_num+=amount;
    }
    total.value=total_num.toFixed(2);
}

//links
function view_service_order(order){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    targ.style.display='block';
    let formData = new FormData();
    formData.append("order", order);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) { 
		targ.innerHTML =req.responseText;
                targ.style.display='block';
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });
                cross_docs_load(targ,order);
                uploaded_files_show(order,targ);
            }
	}
    };
    req.open('POST', 'service_view.php');  
    req.send(formData);  // отослать запрос 
}
function sales_view(order){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("order", order);
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
                cross_docs_load(targ,order);
                uploaded_files_show(order,targ);
            }
	}
    };
    req.open('POST', 'sales_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}
function purchase_view(order){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("order", order);
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
                cross_docs_load(targ,order);
                uploaded_files_show(order,targ);
            }
	}
    };
    req.open('POST', 'purchase_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}

function adm_view(order){
    let targ = document.createElement("DIV");
    targ.classList.add('window_div');
    document.body.appendChild(targ);
    let formData = new FormData();
    formData.append("order", order);
    let req = getXmlHttp();
    req.onreadystatechange = function() {  
	if (req.readyState === 4) {
            if(req.status === 200) { 
		targ.innerHTML =req.responseText;
                targ.style.display='block';
                $( ".datepicker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    firstDay:1,
                    dateFormat: 'yy-mm-dd'
                });
                cross_docs_load(targ,order);
                uploaded_files_show(order,targ);
            }
	}
    };
    req.open('POST', 'adm_view.php');  
    req.send(formData);  // отослать запрос
    targ.innerHTML = '<img src="./img/loading.gif">';
}

//New link

function view_link (order){
    if (order.length !== 12) return;
    let order_type = order.substring(0,2);
    switch (order_type){
        case 'SL' : sales_view(order);
            break;
        case 'SR' : view_service_order(order);
            break;
        case 'PO' : purchase_view(order);
            break;
        case 'AD' : adm_view(order);
            break;
    }
}