<?php
require_once 'config.php';

// 查询函数===========================
// 无法重用一个数据库连接对象，每次查询都是创建一个新的数据库连接，非常消耗资源。
// 如果希望使用其他的查询函数，比如 mysqli_fetch_assoc、mysqli_fetch_row。
// function xiu_query($sql){
//     $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
//     if(!$connection){
//         die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
//     }
//     $data = array();
//     $result = mysqli_query($connection,$sql);
//     if($result){
//         while($row = mysqli_fetch_assoc($result)){
//             $data[] = $row;
//         }
//         mysqli_free_result($result);
//     }
//     mysqli_close($connection);
//     return $data;
// }




// 数据库链接函数
function xiu_connect(){
  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if(!$connection){
        die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
    }
    mysqli_set_charset($connection,'utf8');
    return $connection;
}

// 数据库查询函数
function xiu_query($sql){
    $connection = xiu_connect();
    $data = array();
    $result = mysqli_query($connection,$sql);
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        mysqli_free_result($result);
    }
    mysqli_close($connection);
    return $data;
}

// //当前用户登录

function xiu_get_current_user () {
    if (isset($GLOBALS['current_user'])) {
      // 已经执行过了（重复调用导致）
      return $GLOBALS['current_user'];
    }
  
    // 启动会话
    session_start();
  
    if (empty($_SESSION['current_login_user_id']) || !is_numeric($_SESSION['current_login_user_id'])) {
      // 没有登录标识就代表没有登录
      // 跳转到登录页
      header('Location: /admin/login.php');
      exit; // 结束代码继续执行
    }
  
    // 根据 ID 获取当前登录用户信息（定义成全局的，方便后续使用）
    $GLOBALS['current_user'] = xiu_query(sprintf('select * from users where id = %d limit 1', intval($_SESSION['current_login_user_id'])))[0];
    return $GLOBALS['current_user'];
  }

  //查询受影响的行数
  function xiu_execute($sql){
    $connection = xiu_connect();
    $result = mysqli_query($connection,$sql);
    if($result){
        $affected_rows = mysqli_affected_rows($connection);
    }
    mysqli_close($connection);
    // 返回受影响的行数
    return isset($affected_rows) ? $affected_rows : 0;
  }


  /**
 * 输出分页链接
 * @param  integer $page    当前页码
 * @param  integer $total   总页数
 * @param  string  $format  链接模板，%d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为 5）
 * @example
 *   <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
 */
  function xiu_pagination($page,$total,$format,$visible = 5){
    $left = floor($visible/2);
    $begin = $page - $left;
    $end = $begin + $visible -1;
    $begin = $begin < 1 ? 1 : $begin;
    $end = $end > $total ? $total : $end;
    $begin = $end - $visible + 1;
    $begin = $begin < 1 ? 1 : $begin;
    //上一页
    if($begin-1>0){
      printf('<li><a href="%s">上一页</a></li>',sprintf($format,$page-1));
    }
    //省略号
    if($begin>1){
      print('<li class="disabled"><span>...</span></li>');
    }
    //数字页码
    for($i=$begin;$i<=$end;$i++){
      $activeClass = $i == $page ? 'class="active"' : '';
      printf('<li %s><a href="%s">%d</a></li>',$activeClass,sprintf($format,$i),$i);
    }
    //省略号
    if($end<$total){
      print('<li class="disabled"><span>...</span></li>');
    }
    //下一页
    if($page+1<$total){
      printf('<li><a href="%s">下一页</a></li>',sprintf($format,$page+1));
    }
  }
  
