<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
startSession();
security ();
$id=$_GET['id'];
$file=$_GET['file'];
file_force_download('upload_files/'.$id.'/'.$file);