<?php

//当前用户登录
function current_user(){
    
    session_start();
    if(empty($_SESSION['current_login_user'])){
        header('Location: /admin/login.php');
        exit;
    }
}