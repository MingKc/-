<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Auth as AuthModel;
use app\tools\UserToken;
use app\index\model\User;

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
                "level" => $value->auth_level 
            ];
        }
        return jsonAPI("查询成功", 200 ,$list);
    }

    // 左侧菜单栏
    public function menu(){
        $userToken = new UserToken();
        $user_id = $userToken->checkToken();
        $user = User::where("user_id", $user_id)->find();
        $role_id = $user->role_id;
        $auth = new AuthModel();
        $right = $auth->level($role_id);
        $one = $right["one"];
        $two = $right["two"];
        $data = $auth->loadChildren($one, $two);
        return jsonAPI("查询吃成功！", 200, $data);
    }
}