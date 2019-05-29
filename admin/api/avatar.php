<?php
    require_once '../../config.php';
    
    if(empty($_GET['email'])){
        exit('缺少邮箱！');
    }
    $email = $_GET['email'];
    $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if(!$connection){
        exit('数据库链接失败！');
    }
    $result = mysqli_query($connection, sprintf("select avatar from users where email = '%s' limit 1", $email));

    if(!$reslut){
        exit('查询失败！');
    }
    $rows = mysqli_fetch_assoc($reslut);
    echo $rows['avatar']; 
?>