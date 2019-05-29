<?php
  require '../functions.php';
  xiu_get_current_user();
  
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(empty($_POST['slug'])
      ||empty($_POST['title'])
      ||empty($_POST['created'])
      ||empty($_POST['content'])
      ||empty($_POST['status'])
      ||empty($_POST['category'])){
        $GLOBALS['message'] = '请填写完整内容!';
      }else if(xiu_query(sprintf("select count(1) from posts where slug = '%s'",$_POST['slug']))[0][0]>0){
        $GLOBALS['message'] = '别名以重复，请修改！';
      }else{
         // 图像上传
         if (empty($_FILES['feature']['error'])) {
          // PHP 在会自动接收客户端上传的文件到一个临时的目录
          $temp_file = $_FILES['feature']['tmp_name'];
          // 我们只需要把文件保存到我们指定上传目录
          $target_file = '../static/uploads/' . $_FILES['feature']['name'];
          if (move_uploaded_file($temp_file, $target_file)) {
            $image_file = '/static/uploads/' . $_FILES['feature']['name'];
            var_dump($image_file);
          }
        }
        $slug = $_POST['slug'];
        $title = $_POST['title'];
        $feature = '';
        $created = $_POST['created'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        $user_id = $current_user['id'];
        $category_id = $_POST['category'];

        //保存数据
        $sql = sprintf("insert into posts values (null,'%s','%s','%s','%s','%s',0,0,'%s','%d','%d')",
          $slug,$title,$feature,$created,$content,$status,$user_id,$category_id);
        if(xiu_execute($sql)>0){
          $GLOBALS['success'] = true;
          $GLOBALS['message'] = '保存成功！';
          // header('Location:/admin/posts.php');
          exit;
        }else{
          $GLOBALS['message'] = '保存失败！';
        }
      }
  }
  $categories = xiu_query('select * from categories');


 


?>



<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
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
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
      <?php if(isset($success)): ?>
      <div class="alert alert-success">
        <strong>成功！</strong><?php echo $message;?>
      </div>
      <?php else:?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message;?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <form class="row" action="/admin/post-add.php" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '' ?>"  placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" value="<?php echo isset($_POST['content']) ? $_POST['content'] : '' ?>"  placeholder="内容"></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" value="<?php echo isset($_POST['slug']) ? $_POST['slug'] : '' ?>"   placeholder="slug">
            <p class="help-block">https://baixiu.net/admin/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" value="<?php echo isset($_POST['feature']) ? $_POST['feature'] : '' ?>"   type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach($categories as $item): ?>
              <option value="<?php echo $item['id']; ?>"><?php echo $item['name'] ;?></option>
              <?php endforeach?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted" <?php echo isset($_POST['status']) && $_POST['status'] == 'draft' ? ' selected' : ''; ?>>草稿</option>
              <option value="published" <?php echo isset($_POST['published']) && $_POST['published'] == 'draft' ? ' selected' : ''; ?>>已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'post-add'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    // 当文件域文件选择发生改变过后，本地预览选择的图片
    $('#feature').on('change', function () {
      var file = $(this).prop('files')[0]
      // 为这个文件对象创建一个 Object URL
      var url = URL.createObjectURL(file)
      // url => blob:http://zce.me/65a03a19-3e3a-446a-9956-e91cb2b76e1f
      // 不用奇怪 BLOB: binary large object block
      // 将图片元素显示到界面上（预览）
      $(this).siblings('.thumbnail').attr('src', url).fadeIn()
    })
    // slug 预览
    $('#slug').on('input', function () {
      $(this).next().children().text($(this).val())
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
