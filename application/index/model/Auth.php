<?php
namespace app\index\model;
use think\Model;
use app\index\model\Role;

class Auth extends Model{

    // 获取当前角色的权限
    public function level($role_id){
        $role = Role::where("role_id", $role_id)->find();
        $role_auth_ids = $role->role_auth_ids;
        if($role_auth_ids === ""){
            return null;
        }
        $auth_id = explode(",", $role_auth_ids);
        // 一级权限菜单
        $one = array();
        // 二级权限菜单
        $two = array();
        // 三级权限菜单
        $three = array();
        for ($i=0; $i < count($auth_id); $i++) {
            $auth = Auth::where("auth_id", $auth_id[$i])->find();
            if($auth->auth_level === 0){
                $one[] = [
                    "auth_id" => $auth->auth_id,
                    "auth_name" => $auth->auth_name,
                    "auth_pid" => $auth->auth_pid,
                    "path" => $auth->auth_path,
                    "children" => []
                ];
            }else if($auth->auth_level === 1){
                $two[] = [
                    "auth_id" => $auth->auth_id,
                    "auth_name" => $auth->auth_name,
                    "auth_pid" => $auth->auth_pid,
                    "path" => $auth->auth_path,
                    "children" => []
                ];
            }else if($auth->auth_level === 2){
                $three[] = [
                    "auth_id" => $auth->auth_id,
                    "auth_name" => $auth->auth_name,
                    "auth_pid" => $auth->auth_pid,
                    "path" => $auth->auth_path
                ];
            }
        }
        $list = [
            "one" => $one,
            "two" => $two,
            "three" => $three
        ];
        return $list;
    }


    // 将子集权限挂载的父级权限
    public function loadChildren($parent, $children){
        for ($i=0; $i < count($parent); $i++) { 
            for ($j=0; $j < count($children); $j++) { 
                if($parent[$i]["auth_id"] === $children[$j]["auth_pid"]){
                     array_push($parent[$i]["children"], $children[$j]);
                }
            }
        }
        return $parent;
    }
}