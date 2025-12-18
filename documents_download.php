<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
startSession();
security ();
//file_force_download('uploaded_documents/'.$id.'/'.$_POST['file']);