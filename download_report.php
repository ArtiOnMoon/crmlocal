<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
startSession();
security ();
$a=$_GET[doc];
file_force_download('service/'.$a.'/service_report.pdf');