<?php
require_once '../functions.php';
xiu_get_current_user();

//处理表单提交事件
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  if(empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname'])){
    $GLOBALS['message'] = '请填写完整表单!';
  }else if(empty($_POST['id'])){
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    $sql = sprintf("insert into users values (null ,'%s','%s','%s','%s',null,null,'unactivated')",$slug,$email,$password,$nickname);
    $GLOBALS['message'] = xiu_execute($sql) > 0 ? '添加成功' : '添加失败';
  }else{
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $id = $_POST['id'];
    $sql = sprintf("update users set slug = '%s', email = '%s', nickname = '%s' where id = %d",$slug,$email,$nickname,$id);
    $GLOBALS['message'] = xiu_execute($sql) > 0 ? '保存成功' : '保存失败';
  }
}


//数据过滤
function convert_status($status){
  switch($status){
    case 'unactivated':
    return '未激活';
    case 'activated':
    return '激活';
    case 'forbidden':
    return '禁止';
    case 'trashed':
    return '回收站';
    default:
    return '未知';
  }
}

//获取数据
$user = xiu_query('select * from users');
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>

<body>
  <script>
    NProgress.start()
  </script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
      <div class="alert alert-<?php echo $message == '添加成功' || $message == '保存成功'? 'success' : 'danger'; ?>">
        <strong><?php echo $message == '添加成功' || $message == '保存成功'? '成功!' : '失败!'; ?></strong><?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="button" style="display: none;">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm btn_all" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($user as $item): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td class="text-center"><img class="avatar" src="<?php echo $item['avatar']; ?>"></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td><?php echo $item['nickname']; ?></td>
                <td><?php echo convert_status($item['status']); ?></td>
                <td class="text-center">
                  <a href="javascript:;" class="btn btn-default btn-xs btn-edit">编辑</a>
                  <a href="/admin/user-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function() {
      //获取相关元素
      var $btnall = $('.btn_all');
      var $thcheckbox = $('th > input[type=checkbox]');
      var $tdcheckbox = $('td > input[type=checkbox]');
      var $btnEidt = $('.btn-edit');
      var $btnCancel = $('.btn-cancel');
      var checkedbox = [];
      $tdcheckbox.on('change',function(){
        var id = $(this).data('id');
        if($(this).prop('checked')){
          checkedbox.includes(id) || checkedbox.push(id);
        }else{
          checkedbox.splice(checkedbox.indexOf(id),1);
        }
        console.log(checkedbox);
        checkedbox.length ? $btnall.fadeIn() : $btnall.fadeOut();
      })
      $thcheckbox.on('change',function(){
        var checked = $(this).prop('checked');
        $tdcheckbox.prop('checked',checked).trigger('change');
      })

      //编辑按钮
      $btnEidt.on('click',function(){
        var $tr = $(this).parent().parent();
        var $tds = $tr.children();
        
        var id = $tdcheckbox.data('id');
        var email = $tds.eq(2).text();
        var slug = $tds.eq(3).text();
        var nickname = $tds.eq(4).text();

        $('#id').val(id);
        $('#email').val(email);
        $('#slug').val(slug);
        $('#nickname').val(nickname);
        $('#password').parent().hide();

        $('form > h2').text('编辑用户');
        $('form > div > .btn-save').text('保存');
        $('form > div > .btn-cancel').show();
      })
      $btnCancel.on('click',function(){
        // 清空表单元素上的数据
        $('#id').val('')
        $('#email').val('')
        $('#slug').val('').trigger('input')
        $('#nickname').val('')
        $('#password').parent().show()
        // 界面显示变化
        $('form > h2').text('添加新用户')
        $('form > div > .btn-save').text('添加')
        $('form > div > .btn-cancel').hide()
      })
    })
  </script>
  <script>
    NProgress.done()
  </script>
</body>
</html>