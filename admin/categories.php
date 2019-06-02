<?php
require_once '../functions.php';
xiu_get_current_user();


//增加函数
function add_category(){
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['message'] = '请完整填写表单！';
    $GLOBALS['success'] = false;
    return;
  }

  // 接收并保存
  $name = $_POST['name'];
  $slug = $_POST['slug'];


// %% - 返回一个百分号 %
// %b - 二进制数
// %c - ASCII 值对应的字符
// %d - 包含正负号的十进制数（负数、0、正数）
// %e - 使用小写的科学计数法（例如 1.2e+2）
// %E - 使用大写的科学计数法（例如 1.2E+2）
// %u - 不包含正负号的十进制数（大于等于 0）
// %f - 浮点数（本地设置）
// %F - 浮点数（非本地设置）
// %g - 较短的 %e 和 %f
// %G - 较短的 %E 和 %f
// %o - 八进制数
// %s - 字符串
// %x - 十六进制数（小写字母）
// %X - 十六进制数（大写字母）
  $rows = xiu_execute(sprintf("insert into categories values (null,'%s','%s') ",$slug,$name));
  // $rows = xiu_execute("insert into categories values (null, '{$slug}', '{$name}');");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功!';
}
function edit_category(){

  global $current_edit_category;
  $id = $_GET['id'];

  $name = empty($_POST['name']) ? $current_edit_category[0]['name'] : $_POST['name'];
  $current_edit_category['name'] = $name;
  $slug = empty($_POST['slug']) ? $current_edit_category[0]['slug'] : $_POST['slug'];
  $current_edit_category['slug'] = $slug;

  $rows = xiu_execute("update categories set slug = '%s',name = '%s' where id = '%s';",$slug,$name,$id);
  // $rows = xiu_execute("update categories set slug = '{$slug}', name = '{$name}' where id = {$id}");

  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功!';

}

if(empty($_GET['id'])){
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    add_category();
  }
}else{
  $current_edit_category = xiu_query(sprintf("select * from categories where id = '%d';",$_GET['id']));
  edit_category();
}
$categories = xiu_query('select * from categories');

?>





<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
  <?php include 'inc/navbar.php' ;?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <?php if ($success): ?>
      <div class="alert alert-success">
        <strong>成功！</strong> <?php echo $message; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if(isset($current_edit_category)): ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>编辑分类《<?php echo $current_edit_category['name']; ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name'] ;?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug'] ;?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
          <?php else:?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm btn-delete" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox" data-id=""></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($categories as $item): ?>
                <tr>
                  <td class="text-center"><input type="checkbox"></td>
                  <td><?php echo $item['name']; ?></td>
                  <td><?php echo $item['slug']; ?></td>
                  <td class="text-center">
                    <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs btn-edit">编辑</a>
                    <a href="/admin/category-delect.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function(){
      //定义需要用的到元素
      var btnDelete = $('.btn-delete');
      var allcheckbox = $('th > input[type=checkbox]');
      var checkbox = $('td > input[type=checkbox]');
      //定义空的数组
      var allchecked = [];
      checkbox.on('change',function(){
        var $this = $(this);
        var $id = $this.data('id');
        if($this.prop('checked')){
          allchecked.includes($id) || allchecked.push($id);
        }else{
          allchecked.splice(allchecked.indexOf($id),1);
        }
        allchecked.length ? btnDelete.fadeIn() : btnDelete.fadeOut();
        console.log(checkbox);
        btnDelete.prop('search','?id='+allchecked.join(','));
      })
      allcheckbox.on('change',function(){
        var checked = $(this).prop('checked');
        checkbox.prop('checked',checked).trigger('change');
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
