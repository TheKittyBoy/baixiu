<?php

 require_once '../config.php';

 session_start();
 
 //用户登陆函数

function xiu_login(){
  if(empty($_POST['email'])){
    $GLOBALS['message'] = '请填写邮箱！';
    return;
  }
  if(empty($_POST['password'])){
    $GLOBALS['message'] = '请填写密码！';
    return;
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  //数据库链接
  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if(!$connection){
    // 链接数据库失败，打印错误信息，注意：生产环境不能输出具体的错误信息（不安全）
    die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
  }

  // mysqli_query() 函数执行某个针对数据库的查询。   返回值为一个mysqli_result 对象
  $result = mysqli_query($connection, sprintf("select * from users where email = '%s' limit 1", $email));
  if($result){
    //mysqli_fetch_assoc（） 从结果集中取得一行作为关联数组。
    if($user = mysqli_fetch_assoc($result)){
      if($user['password'] == $password){
        $_SESSION['current_login_user_id'] = $user['id'];
        header('Location: /admin/index.php');
        exit;
      }else{
        $GLOBALS['message'] = '密码错误！';
        return;
      }
    }
    $GLOBALS['message'] = '用户不存在！';
    mysqli_free_result($result);
    }else{
    $GLOBALS['message'] = '登陆失败，请重试！';
    }
  mysqli_close($connection);
}



if($_SERVER['REQUEST_METHOD'] === 'POST'){
  xiu_login();
}

?>



<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.min.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">

  
</head>
<body>
  <div class="login">
    <form class="login-wrap <?php echo isset($message) ? 'shake animated':'' ?>" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.php">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <script>
    $(function($){
      var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;
      $('#email').on('blur',function(){
        var value = $(this).val();
        if(!value || !emailFormat.test(value)) return;
        $.get('/admin/api/avatar.php',{email:value},function(res){
          // console.log(res);
          if(!res) return;
          $('.avatar').fadeOut(function () {
            $(this).on('load',function () {
              $(this).fadeIn();
              }).attr('src',res);
          })
        })
      })
    })
  </script>
</body>
</html>
