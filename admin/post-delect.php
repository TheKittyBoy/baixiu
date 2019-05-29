<?php
require '../functions.php';
if(empty($_GET['id'])){
    exit('缺少必要参数！');
}
$rows = xiu_execute(sprintf('delete from posts where id in (%s)', $_GET['id']));
$target = empty($_SERVER['HTTP_REFERER']) ? '/admin/posts.php' : $_SERVER['HTTP_REFERER'];
header('Location:'.$target);