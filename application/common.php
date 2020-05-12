<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
    // 解析请求类型
    function processRequest(){
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $data = array();
        switch ($request_method){
           case 'get':
           $data = $_GET;
           break;
           case 'post':
           $data = $_POST;
           break;
           case 'put':
           $data = input('put.');
           break;
           case 'delete':
           $data = $_SERVER['REQUEST_URI'];
           break;
       }
       return $data;
   }

   // API返回信息
   function jsonAPI($msg = "" , $status = 200, $data = ""){
        $meta = ["msg" => $msg, "status" => $status];
        $json["data"] = $data;
        $json["meta"] =$meta;
        return json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;
   }