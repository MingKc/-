<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Auth as AuthModel;

class Auth extends AdminController{
    // 权限列表
    public function showlist(){
        $auth = new AuthModel();
        $authList = $auth->select();
        $list = array();
        foreach ($authList as $key => $value) {
            $list[] = [
                "auth_id" => $value->auth_id,
                "auth_name" => $value->auth_name,
                "pid" => $value->auth_pid,
                "children" => 
            ];
        }
    }
}