<?php
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime', 0);
startSession();
function startSession() {
	if ( session_id() ) return true;
	else return session_start();
}
function check_valid_user () {
    if (isset($_SESSION['valid_user'])) {
        return $_SESSION['valid_user'];
    } else {
        return '';
  }
};
function userlogin ($username, $password) {
          if (!isset($_POST)){
              echo 'Problem';
              exit();
          }
          $db =  db_connect();  
          $query='SELECT * FROM users WHERE user_deleted=0 AND username="'.$username.'"';
          $result=$db->query($query);
          $row=$result->fetch_assoc();
          if (!$result) exit('User not found.');
          if ($result->num_rows === 1){
              $_SESSION['valid_user']=$row['username'];
              $_SESSION['role']=$row['role'];
              $_SESSION['uid']=$row['uid'];
              $_SESSION['default_company']=$row['u_comp_id'];
              $_SESSION['full_name']=$row['full_name'];
              $_SESSION['access_level']=$row['access_level'];
              $_SESSION['stock_page']=1;
              $_SESSION['order_page']=1;
              $_SESSION['cust_page']=1;
              $_SESSION['service_page']=1;
              $_SESSION['sales_page']=1;
              $_SESSION['acl_full_access']=$row['acl_full_access'];
              $_SESSION['acl_stock']=$row['acl_stock'];
              $_SESSION['acl_cust']=$row['acl_cust'];
              $_SESSION['acl_service']=$row['acl_service'];
              $_SESSION['acl_sales']=$row['acl_sales'];
              $_SESSION['acl_purchase']=$row['acl_purchase'];
              $_SESSION['acl_documents']=$row['acl_documents'];
              $_SESSION['acl_invoices']=$row['acl_invoices'];
              if ($row['comp_access']!=''){
                  $_SESSION['comp_access']= explode(',', $row['comp_access']);
              } 
              else $_SESSION['comp_access']=0;
              return true;
          }
          else
            echo 'User not found. '.var_dump($result);
};
function security (){
    if (!isset($_SESSION['valid_user'])) { 
        header('Location: /index.php');
        //echo '<meta http-equiv="Refresh" content="1; url=/index.php">';
        exit('Access denied.');
    }
}
function access_check($role=[''],$id=[''],$level=0){
    if ($_SESSION['role']==='admin') return true;
    if($role!==['']){
        foreach ($role as $value){
            if ($_SESSION['role']===$value) return true;
        }
    }
    if($id!==['']){
        foreach ($id as $value){
            if ($_SESSION['valid_user']===$value) return true;
        }
    }
    if ($_SESSION['access_level']>=$level) return true;
    return false;
}
function check_access($section,$level,$comp_access=0){
    if ($_SESSION['acl_full_access']==1) {return false;}
    if ($comp_access!=0){
        if ($_SESSION['comp_access']==0 || in_array($comp_access, $_SESSION['comp_access'])){return false;}
        else {return true;}
    }
    if ($_SESSION[$section]>=$level) return false;
    return true;
}