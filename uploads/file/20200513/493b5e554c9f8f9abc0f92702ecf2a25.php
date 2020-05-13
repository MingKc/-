<?php

    $localhost = "localhost";
    $username = "root";
    $password = "123456";
    $db_name = "test";
    $port = "3306";

    // 连接数据库
    $conn = mysqli_connect($localhost, $username, $password, $db_name, $port);
    if (!$conn) {
        die("数据库连接失败！");
    }
    
?>