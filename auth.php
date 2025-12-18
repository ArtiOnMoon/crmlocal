/*<?php
require_once './functions/main.php';
require_once './functions/auth.php';
require_once './functions/db.php';
$username=$_POST['username'];
$password=sha1($_POST['password']);
    if (userlogin($username, $password)){
        header( 'Location: index.php' );
    }
    else echo '<p><a href="/index.php">Try again</a></p>';
    