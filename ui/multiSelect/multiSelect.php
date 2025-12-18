<?php

function multi_selector(array $data, $values = [], $class="multiselect",$name="miltiselect"){
    if (!is_array($values)){$values = [];}
    $num = count($values);
    if ($num > 0) { $message = $num.' values'; }
    else {$message = 'Select users';}
    $out='<div class="'.$class.'"><div class="selectBox" onclick="showCheckboxes(this)">';
    $out.='<input class="multiselect_checkbox" type="checkbox" style="display:none;">';
    $out.='<select class="task_select"><option value="">'.$message.'</option></select>';
    $out.='<div class="overSelect"></div>';
    $out.='</div><div id="checkboxes">';
    foreach ($data as $key => $value) {
        if (in_array($key, $values)) {$checked = ' checked';}
        else {$checked = '';}
        $out.='<label><input type="checkbox"'.$checked.' name="'.$name.'[]" value='.$key.' />'.$value.'</label>';
    }
    $out.= '</div></div>';    
    return $out;
}

?>
<style>
.multiselect {
  width: 100%;
}

.selectBox {
  position: relative;
}

.selectBox select {

}

.overSelect {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}

#checkboxes {
  display: none;
  border: 1px #dadada solid;
  position: absolute;
  background: white;
  z-index: 10;
}

#checkboxes label {
  display: block;
}

#checkboxes label:hover {
  background-color: #1e90ff;
}
</style>

<script>
function showCheckboxes(elem) {
    let expanded = elem.querySelector('.multiselect_checkbox');
    var checkboxes = document.getElementById("checkboxes");
    if (!expanded.checked) {
        checkboxes.style.display = "block";
        expanded.checked = true;
    } else {
        checkboxes.style.display = "none";
        expanded.checked = false;
    }
}
</script>