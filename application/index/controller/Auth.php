<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Auth as AuthModel;
use app\tools\UserToken;
use app\index\model\User;

class Auth extends AdminController{
    // 权限列表
    public function showlist(){
        $data = processRequest();
        if(!isset($data["type"])){
            return jsonAPI("查询参数为空！", 500);
        }
        $type = $data["type"];
        $auth = new AuthModel();
        $authList = $auth->select();
        if($type === "list"){
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
        }else if($type === "tree"){
            // 一级权限菜单
            $one = array();
            // 二级权限菜单
            $two = array();
            // 三级权限菜单
            $three = array();
            foreach ($authList as $key => $value) {
                if($value->auth_level === 0){
                    $one[] = [
                        "auth_id" => $value->auth_id,
                        "auth_name" => $value->auth_name,
                        "auth_pid" => $value->auth_pid,
                        "path" => $value->auth_path,
                        "children" => []
                    ];
                }else if($value->auth_level === 1){
                    $two[] = [
                        "auth_id" => $value->auth_id,
                        "auth_name" => $value->auth_name,
                        "auth_pid" => $value->auth_pid,
                        "path" => $value->auth_path,
                        "children" => []
                    ];
                }else if($value->auth_level === 2){
                    $three[] = [
                        "auth_id" => $value->auth_id,
                        "auth_name" => $value->auth_name,
                        "auth_pid" => $value->auth_pid,
                        "path" => $value->auth_path
                    ];
                }
            }
            // 将三级权限挂载在二级权限的children
            $m = $auth->loadChildren($two, $three);
            // 将二级权限挂载在一级权限的children
            $right = $auth->loadChildren($one, $m);
            return jsonAPI('查询成功！', 200, $right);
        }
        return jsonAPI("查询失败", 500);
    }

    // 左侧菜单栏
    public function menu(){
        $userToken = new UserToken();
        $user_id = $userToken->checkToken();
        if(!$user_id){
            return jsonAPI("查询失败", 500);
        }
        $user = User::where("user_id", $user_id)->find();
        $role_id = $user->role_id;
        $auth = new AuthModel();
        $right = $auth->level($role_id);
        $one = $right["one"];
        $two = $right["two"];
        $data = $auth->loadChildren($one, $two);
        return jsonAPI("查询成功！", 200, $data);
    }
}