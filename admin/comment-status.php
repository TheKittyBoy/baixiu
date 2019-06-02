<?php
    require '../functions.php';
    header('Content-type:application/json');
    

    if(empty($_GET['id']) || empty($_GET['status'])){
        exit(json_encode(array(
            'success' => false,
            'message' => '缺少必要参数！'
        )));
    }
    $rows = xiu_execute(sprintf("update comments set status = '%s' where id in (%s)", $_POST['status'], $_GET['id']));
    echo json_encode(array(
        'success' => $rows > 0
    ));
?>