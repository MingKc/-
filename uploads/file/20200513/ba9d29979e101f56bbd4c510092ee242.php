<?php

    // 页面跳转  $message提示信息   $url跳转地址
    function page_redirect ($flag, $message, $url) {
        // $flag 1为直接跳转
        if ($flag) {
            url($url);
        } else {
            alert($message);
            url($url);
        } 
    }

    // 页面重载
    function url($url) {
        echo "<script>
        window.location='$url';
        </script>";
    }

    // 警告框
    function alert($message){
        echo "<script>
        alert('$message');
        </script>";
    }
?>