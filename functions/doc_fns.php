<?php
function create_download_button($file_id, $file_name ){
    $output='';
    $output.='<form action="download_doc.php" name="file_form" method="POST">';
    $output.='<input type="submit" name="download_button" value="Download file">';
    $output.='<input type="hidden" name="id" value="'.$file_id.'">';
    $output.='<input type="hidden" name="file" value="'.$file_name.'">';
    $output.='</form>';
    return $output;
}
function select_docs_type($current=''){
    $types=array(1=>'Forms',2=>'Documents',3=>'Procedures',4=>'Instructions');
    $out='';
    $out.='<select name="cat">';
    foreach ($types as $key => $value) {
        $out.='<option ';
        if ($key==$current) $out.='selected ';
        $out.='value="'.$key.'">'.$value.'</option>';
    }
    $out.='</select>';
    return $out;
}
function select_doc_type($current='', $flag=0, $headers='name="doctype" id="doctype" onchange=show_docs_table(1)'){
    $ar=['approval','pass','calibration','license', 'engineer certificates','warranty','other'];
    echo '<select '.$headers.'>';
    if ($flag===1) echo '<option>All</option>';
    foreach ($ar as $value) {
        echo '<option';
        if ($current===$value) echo' selected';
        echo '>'.$value.'</option>';
    }
    echo '</select>';
}
function view_doc_link ($id){
    return '<a href="#" onclick="doc_control_view(\''.$id.'\')">'.$id.'</a>';
}