<?php
function get_file_folder($id,$type,$uploads_folder){
    $sub= intval((int)$id/1000);
    $subdir=$uploads_folder.$type.'/'.$sub.'/';
    if (!is_dir($subdir))mkdir($subdir,0777,true);//Create folder
    $uploaddir=$subdir.$id.'/';
    if (!is_dir($uploaddir))mkdir($uploaddir,0777,true);//Create folder
    return $uploaddir;
}
function reArrayFiles($file){
    $file_ary = array();
    $file_count = count($file['name']);
    $file_key = array_keys($file);
    for($i=0;$i<$file_count;$i++)
    {
        foreach($file_key as $val)
        {
            $file_ary[$i][$val] = $file[$val][$i];
        }
    }
    return $file_ary;
}