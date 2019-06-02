<?php
    require '../functions.php';
    if(empty($_GET['id'])){
        exit(json_encode(array(
            'success' => false,
            'message' => '缺少必要参数！'
        )));
    }
    $rows = xiu_execute(sprintf('delete from comments where id in (%s)',$_GET['id']));
   echo json_encode(array(
        'success' => $rows >0
   ));
?>