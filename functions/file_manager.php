<?php
function files_table($directory=''){
    ?>
    <h2 align="center">Uploaded files</h2>
    <form action="/scripts/files_upload.php" method="post" multipart="" enctype="multipart/form-data">
        <table width="100%" border="0" cellspacing = "0" cellpadding="2px">
        <thead>
            <th width="100px">File</th>
            <th width="100px">Download</th>
            <th width="100px">Delete</th>
        </thead>
    <?php
    $list=array_slice(scandir($directory),2);
    foreach ($list as $arg){
    }
}