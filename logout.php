<?php
require_once 'functions/main.php';
require_once 'functions/auth.php';
startSession();
unset($_REQUEST);
unset($_POST);
unset($_SESSION['valid_user']);
destroySession();
header( 'Location: index.php' );