<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Role as RoleModel;
use app\index\model\Auth as AuthModel;

class Role extends AdminController{
    // 添加角色
    public function add(){
        $data = processRequest();
        if(!isset($data["role_name"]) || !isset($data["role_desc"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $role = new RoleModel([
                "role_name" => $data["role_name"],
                "role_desc" => $data["role_desc"]
            ]);
        if($role->save()){
            return jsonAPI("角色添加成功！", 200);
        }
        return jsonAPI("角色添加失败！", 500);
    }

    // 删除角色
    public function delete(){
        $data = processRequest();
        if(!isset($data["role_id"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $role = RoleModel::where("role_id", $data["role_id"])->find();
        if($role->delete()){
            return jsonAPI("角色删除成功！", 200);
        }
        return jsonAPI("角色删除失败！", 500);
    }

    // 获取所有角色及权限列表
    public function list(){
        $data = processRequest();
        $role = new RoleModel();
        if(isset($data["role_id"])){
            $roleList = $role->where('role_id', $data["role_id"])->find();
            if(!$roleList){
                return jsonAPI("查询角色不存在！", 500);
            }
            $auth = new AuthModel();
            $list = $auth->level($data["role_id"]);
            if($list !== null){
                $one = $list["one"];
                $two = $list["two"];
                $three = $list["three"];
                // 将三级权限挂载在二级权限的children
                $m = $auth->loadChildren($two, $three);
                // 将二级权限挂载在一级权限的children
                $n = $auth->loadChildren($one, $m);
            }
            $data = [
                "role_id" => $data["role_id"],
                "role_name" => $roleList->role_name,
                "role_desc" => $roleList->role_desc,
                "children" => $n
            ];
        }else{
            $roleList = $role->select();
            $data = array();
            foreach ($roleList as $key => $value) {
                $role_id = $value->role_id;
                $auth = new AuthModel();
                $list = $auth->level($role_id);
                if($list !== null){
                    $one = $list["one"];
                    $two = $list["two"];
                    $three = $list["three"];
                    // 将三级权限挂载在二级权限的children
                    $m = $auth->loadChildren($two, $three);
                    // 将二级权限挂载在一级权限的children
                    $n = $auth->loadChildren($one, $m);
                }
                $data[] = [
                    "role_id" => $role_id,
                    "role_name" => $value->role_name,
                    "role_desc" => $value->role_desc,
                    "children" => $n
                ];
            }
        }
        return jsonAPI("角色列表获取成功！", 200, $data);
    }

    // 修改角色信息
    public function modify(){
        $data = processRequest();
        if(!isset($data["role_id"]) || !isset($data["role_name"]) || !isset($data["role_desc"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $role = RoleModel::where("role_id", $data["role_id"])->find();
        $role->role_name = $data["role_name"];
        $role->role_desc = $data["role_desc"];
        if($role->save()){
            return jsonAPI("角色信息修改成功！", 200);
        }
        return jsonAPI("角色信息修改失败！", 500);
    }

    // 修改角色权限
    public function auth(){
        $data = processRequest();
        if(!isset($data["role_id"]) || !isset($data["role_auth_ids"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $role = RoleModel::where("role_id", $data["role_id"])->find();
        $role_auth = $role->saveAuth($data["role_auth_ids"]);
        $role->role_auth_ids = $data["role_auth_ids"];
        $role->role_auth_ac = $role_auth;
        if($role->save()){
            return jsonAPI("权限分配成功！", 200);
        }
        return jsonAPI("权限分配失败！", 500);
    }
}