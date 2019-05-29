<?php

  require_once '../functions.php';
  xiu_get_current_user();

  function convert_status($status){
    switch($status){
      case 'drafted':
      return '草稿';
      case 'published':
      return '已发布';
      case 'trashed':
      return '回收站';
      default:
      return '未知';
    }
  }

  function convert_time($created){
      // 设置默认时区！！！
    date_default_timezone_set('UTC');

    // 转换为时间戳
    $timestamp = strtotime($created);

    // 格式化并返回 由于 r 是特殊字符，所以需要 \r 转义一下
    return date('Y年m月d日 <b\r> H:i:s', $timestamp);
  }
  // 在筛选问题中，如果只出现问号，但是没有参数的传入，该区看看表单的name值是否填写完整
  //请求数据--筛选条件
  $query = '';
  $where = ' 1 = 1 ';
  // 状态筛选
if (isset($_GET['s']) && $_GET['s'] != 'all') {
  $where .= sprintf(" and posts.status = '%s'", $_GET['s']);
  $query .= '&s=' . $_GET['s'];
}
  // 分类筛选
if (isset($_GET['c']) && $_GET['c'] != 'all') {
  $where .= sprintf(" and posts.category_id = %d", $_GET['c']);
  $query .= '&c=' . $_GET['c'];
}

  // is_numeric() 函数用于检测变量是否为数字或数字字符串。
  $page = isset($_GET['p']) && is_numeric($_GET['p']) ? intval($_GET['p']) : 1;
  // $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
  $size = 10;
  $offset = ($page-1) * 10;
  //总条数
  $totle_count = intval(xiu_query('
  select count(1) as count
  from posts
  inner join users on posts.user_id = users.id
  inner join categories on posts.category_id = categories.id
  where' .$where)[0]['count']);
  //总页数 ---ceil向上取整
  $totle_page = ceil($totle_count / $size);

  if($page <= 0){
    header('Location: /admin/posts.php?p=1' . $query);
    exit;
  }
  
  if($page > $totle_page){
  header('Location: /admin/posts.php?p=' . $totle_page . $query);
    exit;
  }

  $posts = xiu_query(sprintf('
  select
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  categories.name as category_name,
  users.nickname as author_name
  from posts
  inner join users on posts.user_id = users.id
  inner join categories on posts.category_id = categories.id
  where %s
	ORDER BY posts.created DESC
	LIMIT %d,%d
 ',$where,$offset,$size));
 
 $category = xiu_query('select * from categories');

?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm btn-all" href="/admin/post-delect.php" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="c" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach($category as $item): ?>
            <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['c']) && $_GET['c'] == $item['id'] ? ' selected' : ''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="s" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['s']) && $_GET['s'] == 'drafted' ? ' selected' : ''; ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['s']) && $_GET['s'] == 'published' ? ' selected' : ''; ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['s']) && $_GET['s'] == 'trashed' ? ' selected' : ''; ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php xiu_pagination($page,$totle_page,'?p=%d'. $query); ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($posts as $item) : ?>
            <tr>
              <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
              <td><?php echo $item['title']; ?></td>
              <td><?php echo $item['author_name']; ?></td>
              <td><?php echo $item['category_name']; ?></td>
              <td class="text-center"><?php echo convert_time($item['created']); ?></td>
              <td class="text-center"><?php echo convert_status($item['status']); ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="/admin/post-delect.php?id=<?php echo $item['id'];?>" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function(){
      var allDelete =  $('.btn-all');
      //全部选择
      var thcheckbox = $('th > input[type=checkbox]');
      // 部分选择
      var tdcheckbox = $('td > input[type=checkbox]');
      //封装一个接收数组
      var checkbox = [];
      tdcheckbox.on('change',function(){
        var $this = $(this);
        var id = $this.data('id');
        if($this.prop('checked')){
          checkbox.includes(id) || checkbox.push(id);
        }else{
          checkbox.splice(checkbox.indexOf(id),1);
        }
        checkbox.length ? allDelete.fadeIn() : allDelete.fadeOut();
        console.log(checkbox);
        allDelete.prop('search','?id='+checkbox.join(','));
      })
      thcheckbox.on('change',function(){
        var checked = $(this).prop('checked');
        tdcheckbox.prop('checked',checked).trigger('change');
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
