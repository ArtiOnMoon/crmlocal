<?php
function db_connect() {
  $result = new mysqli('MySQL-8.0', 'root', '', 'ms-service'); 
  $result->set_charset("utf8");
  if (!$result){
    echo 'Невозможно подключиться к серверу баз данных';}
  else{
      return $result;
  }
}